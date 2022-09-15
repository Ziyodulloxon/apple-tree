<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m220915_095453_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(6)->notNull(),
            'created_date' => $this->integer(10)->notNull(),
            'fallen_date' => $this->integer(10),
            'status' => $this->string(10)->notNull()->defaultValue('on_tree'),
            'remained' => $this->decimal(3,2)->notNull()->defaultValue(1.00),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
