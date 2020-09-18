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
        $convertedQualities = [
            360 => [
                "converted" => true
            ],
            720 => [
                "attempts" => 2,
                "converted" => true
            ]
        ];

        $items = [
            ["id" => "JjfGDio4mnp", "extension" => "mp4", "status" => Video::UNDER_PROCESS, "quality" => null],
            ["id" => "pCq08PgrZTK", "extension" => "mp4", "status" => Video::READY, "quality" => json_encode($convertedQualities)],
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
