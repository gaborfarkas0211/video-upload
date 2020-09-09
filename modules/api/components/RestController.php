<?php

namespace app\modules\api\components;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpHeaderAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;

class RestController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'ips' => Yii::$app->params['allowedIps'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException("Access denied for this action.");
                }
            ],
            'basicAuth' => [
                'class' => HttpHeaderAuth::className(),
                'header' => 'Authorization',
            ],
        ]);
    }

    /**
     * @param array $data
     * @param null $message
     * @return array
     */
    public function renderResult($data = [], $message = null)
    {
        $result = [];
        $result["message"] = $message;
        return array_merge($result, $data);
    }
}
