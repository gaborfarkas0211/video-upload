<?php

namespace app\modules\api\controllers;

use app\modules\api\components\RestController;
use yii\web\NotFoundHttpException;

class SiteController extends RestController
{

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors["basicAuth"]);

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actionError()
    {
        return $this->renderResult(new NotFoundHttpException());
    }
}
