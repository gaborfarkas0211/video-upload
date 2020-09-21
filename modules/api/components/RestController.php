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
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
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
        $array = $data;
        if(is_object($data)) {
            $array = ArrayHelper::toArray($data);
        }
        return ArrayHelper::merge($result, $array);
    }
}
