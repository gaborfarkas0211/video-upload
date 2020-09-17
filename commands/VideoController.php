<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Video;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;

class VideoController extends Controller
{

    public static $qualities = [360, 720];
    public $uploadPath = 'web/uploads';
    public $watchPath = 'web/watch';
    const MAX_ATTEMPTS = 3;
    const PROCESS_FILE = 'runtime/logs/process.pid';

    public function actionConvert()
    {
        $lockFile = $this->checkProcessRunning();

        $videos = Video::find()->where(['status' => Video::UNDER_PROCESS])->all();
        foreach ($videos as $video) {
            $currentQuality = $video->quality;
            $converts = [];
            foreach (self::$qualities as $quality) {
                $isConverted = $currentQuality[$quality]["converted"] ?? false;
                $attempts = $currentQuality[$quality]["attempts"] ?? 0;
                if (!$isConverted && $attempts < self::MAX_ATTEMPTS) {
                    $value = 1;
                    $output = null;
                    $newFileName = $video->replace($quality);
                    $cmd = "ffmpeg -n -i $this->uploadPath/$video->file 2>&1 -vcodec libx264 -acodec aac -crf 25 -level 3.0 -profile:v baseline -vf scale=-2:$quality $this->watchPath/$newFileName";

                    exec($cmd, $output, $value);
                    if ($value === 0) {
                        $converts[] = Video::READY;
                        $currentQuality[$quality]["converted"] = true;
                        Yii::info("$video->file successfully converted to $quality quality.", __METHOD__);
                        continue;
                    } else {
                        $currentQuality[$quality]["attempts"] = ++$attempts;
                        $currentQuality[$quality]["converted"] = false;
                        if ($attempts === self::MAX_ATTEMPTS) {
                            $converts[] = Video::UNSUCCESSFUL_CONVERT;
                        }
                        Yii::warning("$video->file video could not been converted to $quality. Number of attempts: $attempts.\nStack trace:" . VarDumper::export($output), __METHOD__);
                    }
                }

                if ($isConverted) {
                    $converts[] = Video::READY;
                }
            }

            if (count($converts) === count(self::$qualities)) {
                $status = $video->status;
                if (count(array_unique($converts)) === 1) {
                    $status = reset($converts);
                } else if (in_array(1, $converts, true)) {
                    $status = Video::READY;
                }
                $video->status = $status;
            }

            $video->quality = $currentQuality;
            if (!$video->update()) {
                foreach ($currentQuality as $quality => $values) {
                    Video::deleteFile("$this->watchPath/", $video->replace($quality), __METHOD__);
                }
                Yii::error("The '$video->file' video could not been updated.", __METHOD__);
            }
        }

        $this->unLockFile($lockFile);
    }

    private function checkProcessRunning()
    {
        $lockFile = fopen(self::PROCESS_FILE, 'c');
        $gotGock = flock($lockFile, LOCK_EX | LOCK_NB, $wouldBlock);
        if ($lockFile === false || (!$gotGock && !$wouldBlock)) {
            Yii::error("Unexpected error opening or locking lock file. Perhaps you don't  have permission to write to the lock file or its containing directory?");
            exit;
        } else if (!$gotGock && $wouldBlock) {
            Yii::info("Another instance is already running; terminating.");
            exit;
        }
        ftruncate($lockFile, 0);
        fwrite($lockFile, getmypid() . "\n");
        return $lockFile;
    }

    private function unLockFile($lockFile)
    {
        ftruncate($lockFile, 0);
        flock($lockFile, LOCK_UN);
    }
}
