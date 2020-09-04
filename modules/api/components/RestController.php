<?php

namespace app\modules\api\components;

use Yii;
use yii\rest\Controller;

class RestController extends Controller
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'ips' => Yii::$app->params['allowedIps'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\ForbiddenHttpException("Access denied for this action.");
                }
            ],
            'basicAuth' => [
                'class' => \yii\filters\auth\HttpHeaderAuth::className(),
                'header' => 'Authorization',
            ],
        ]);
    }
}
