<?php

namespace app\models;

use Yii;
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
    const UNSUCCESSFUL_CONVERT = 2;

    public $file;

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

    /**
     * @param $type
     * @return bool
     */
    public static function typeValidation($type)
    {
        return $type == 'video/mp4' || $type == 'video/webm';
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        $this->quality = json_decode($this->quality, true);
        $this->file = $this->replace();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!$insert && $this->quality != null) {
            $this->quality = json_encode($this->quality);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param $file
     * @return bool|string
     */
    public static function createLink($file)
    {
        if (file_exists($file)) {
            return Url::base('http') . "/$file";
        }
        return false;
    }

    /**
     * @param $path
     * @param $file
     * @param $method
     * @return bool
     */
    public static function deleteFile($path, $file, $method)
    {
        $fullPath = $path . $file;
        if (file_exists($fullPath)) {
            try {
                unlink($fullPath);
                Yii::info("The '$file' file successfully deleted from '$path'.", $method);
                return true;
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), $method);
                return false;
            }
        }
        Yii::error("The '$file' not found in '$path'.", $method);
        return false;
    }

    /**
     * @param null $quality
     * @return string
     */
    public function replace($quality = null)
    {
        $fullName = $this->id;
        if ($quality) {
            $fullName .= "_" . $quality;
        }
        $fullName .= "." . $this->extension;
        return $fullName;
    }
}
