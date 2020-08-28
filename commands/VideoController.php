<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Video;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class VideoController extends Controller
{

    public static $qualities = [360, 720];

    public function actionConvert($v)
    {
        $video = Video::find()->where(['id' => $v])->one();
        if (!$video) {
            $this->stderr('Error: ' . ExitCode::getReason(ExitCode::DATAERR));
            return ExitCode::DATAERR;
        }
        $video->status = Video::UNDER_PROCESS;
        $video->update();

        $video = Video::find()->where(['id' => $v])->one();
        $selected = BaseConsole::select("Choose a quality:", self::$qualities);
        $quality = self::$qualities[$selected];

        shell_exec("ffmpeg -i web/uploads/$v.mp4 -vcodec libx264 -acodec aac -crf 25 -level 3.0 -profile:v baseline -vf scale=-2:$quality web/watch/$v" . "_" . "$quality.mp4");
        
        $video->status = Video::READY;
        if (empty($video->quality)) {
            $video->quality = [$quality => true];
        } else if (!isset($video->quality[$quality])) {
            $qualities = $video->quality;
            $qualities[$quality] = true;
            $video->quality = $qualities;
        }
        $video->save();

        $this->stderr('OK: ' . ExitCode::getReason(ExitCode::OK));
        return ExitCode::OK;
    }
}
