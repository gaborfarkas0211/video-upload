<?php

namespace app\modules\api;

use Yii;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Yii::$app->setComponents([
            'response' => [
                'class' => 'yii\web\Response',
                'on beforeSend' => function ($event) {
                    $response = $event->sender;
                    $response->format = \yii\web\Response::FORMAT_JSON;
                    if ($response->data !== null) {
                        $message = $response->data['message'] ?? null;
                        unset($response->data['message']);
                        $response->data = [
                            'success' => $response->isSuccessful,
                            'message' => $message,
                            'data' => $response->data,
                        ];
                    }
                },
            ],
        ]);
    }
}