<?php

namespace app\modules\api\controllers;

use app\models\Video;
use app\modules\api\components\RestController;
use Yii;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class VideoController extends RestController
{

    public $uploadPath = 'uploads/';
    public $watchPath = 'watch/';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ['GET'];
        $verbs["upload"] = ['POST'];
        $verbs["delete"] = ['DELETE'];
        return $verbs;
    }

    public function actionIndex($v, int $quality = 720)
    {
        $video = Video::find()->where(['id' => $v])->one();
        if ($video) {
            if ($video->status == Video::READY && isset($video->quality[$quality])) {
                return ["link" => Url::base('http') . "/$this->watchPath" . $video->id . "_$quality" . ".$video->extension"];
            }
            Yii::$app->response->statusCode = 404;
            return ["link" => Url::base('http') . "/$this->uploadPath" . $video->id . ".$video->extension"];
        }
        throw new NotFoundHttpException('The requested video could not be found.');
    }

    public function actionUpload()
    {
        if ($file = UploadedFile::getInstanceByName('file')) {
            if (!Video::typeValidation($file->type)) {
                throw new HttpException(400, 'Invalid file type.');
            }

            $extension = $file->getExtension();
            $file->name = Yii::$app->security->generateRandomString(11);

            $video = new Video();
            $video->id = $file->name;
            $video->extension = $extension;
            $video->status = Video::UNDER_PROCESS;

            if ($file->saveAs($this->uploadPath . $file->name . '.' . $extension) && $video->save()) {
                return ['id' => $video->id];
            }
        }
        throw new HttpException(500, 'The video could not be uploaded.');
    }

    public function actionDelete($v)
    {
        $video = Video::find()->where(['id' => $v])->one();
        if ($video) {
            foreach ((array) $video->quality as $quality => $value) {
                if ($value) {
                    $this->delete($this->watchPath . $video->id . "_$quality" . ".$video->extension");
                }
            }
            $this->delete($this->uploadPath . $video->id . '.' . $video->extension);
            $video->delete();
            throw new HttpException(200, 'Video deleted successfully.');
        }
        throw new NotFoundHttpException('The requested Video could not be found.');
    }

    private function delete($file)
    {
        try {
            unlink($file);
        } catch (\Throwable $th) {
        }
    }
}
