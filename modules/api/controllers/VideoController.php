<?php

namespace app\modules\api\controllers;

use app\models\Video;
use app\modules\api\components\RestController;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class VideoController extends RestController
{

    public $uploadPath = 'uploads/';
    public $watchPath = 'watch/';

    /**
     * @return array
     */
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

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpload()
    {
        if ($file = UploadedFile::getInstanceByName('file')) {
            if (!Video::typeValidation($file->type)) {
                throw new BadRequestHttpException('Invalid file type.');
            }

            $extension = $file->getExtension();
            $file->name = Yii::$app->security->generateRandomString(11);

            $video = new Video();
            $video->id = $file->name;
            $video->extension = $extension;
            $video->status = Video::UNDER_PROCESS;

            $fileName = $video->replace();
            if ($file->saveAs($this->uploadPath . $fileName)) {
                if ($video->save()) {
                    Yii::info("The '$fileName' file successfully uploaded to: '$this->uploadPath'.", __METHOD__);
                    return $this->renderResult(['id' => $video->id]);
                }
                Video::deleteFile($this->uploadPath, $fileName, __METHOD__);
            }
        }
        throw new ServerErrorHttpException('The video could not be uploaded.');
    }

    /**
     * @param $v
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete($v)
    {
        $video = Video::find()->where(['id' => $v])->one();
        if ($video) {
            foreach ((array) $video->quality as $quality => $attributes) {
                if (isset($attributes["converted"]) && $attributes["converted"]) {
                    Video::deleteFile($this->watchPath, $video->replace($quality), __METHOD__);
                }
            }
            if (!$video->delete()) {
                throw new ServerErrorHttpException("Video could not be deleted.");
            } else {
                if(Video::deleteFile($this->uploadPath, $video->file, __METHOD__)) {
                    return $this->renderResult([], 'Video deleted successfully.');
                }
            }

            Yii::$app->response->setStatusCode(404);
            return $this->renderResult([], 'Video could not be deleted, the file not found.');
        }
        throw new NotFoundHttpException('The requested Video could not be found.');
    }
}
