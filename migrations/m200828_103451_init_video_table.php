<?php

use app\models\Video;
use yii\db\Migration;

/**
 * Class m200828_103451_init_video_table
 */
class m200828_103451_init_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $items = [
            ["id" => "JjfGDio4mnp", "extension" => "mp4", "status" => Video::UNDER_PROCESS, "quality" => null],
            ["id" => "pCq08PgrZTK", "extension" => "mp4", "status" => Video::READY, "quality" => json_encode([360 => true, 720 => true])],
        ];
        foreach ($items as $item) {
            $this->insert('video', $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('video');
    }
}
