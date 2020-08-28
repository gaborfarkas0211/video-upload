<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%video}}`.
 */
class m200826_085728_create_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%video}}', [
            'id' => $this->string(11)->unique()->notNull(),
            'extension' => $this->string(5)->notNull(),
            'status' => $this->boolean()->defaultValue(0),
            'quality' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%video}}');
    }
}
