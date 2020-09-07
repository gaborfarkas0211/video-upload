<?php

namespace app\models;

use yii\helpers\Url;

/**
 * This is the model class for table "video".
 *
 * @property string $id
 * @property string $extension
 * @property int|null $status
 * @property string|null $quality
 */
class Video extends \yii\db\ActiveRecord
{

    const UNDER_PROCESS = 0;
    const READY = 1;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'extension'], 'required'],
            [['status'], 'integer'],
            [['id'], 'string', 'max' => 11],
            [['extension'], 'string', 'max' => 5],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'extension' => 'Extension',
            'status' => 'Status',
            'quality' => 'Quality',
        ];
    }
    
    public static function typeValidation($type)
    {
        return $type == 'video/mp4' || $type == 'video/webm';
    }

    public function afterFind()
    {
        $this->quality = json_decode($this->quality, true);
    }

    public function beforeSave($insert)
    {
        if(!$insert && $this->quality != null) {
            $this->quality = json_encode($this->quality);
        }
        return parent::beforeSave($insert);
    }

    public static function createLink($file) {
        if(file_exists($file)) {
            return Url::base('http') . "/$file";
        }
        return false;
    }
}
