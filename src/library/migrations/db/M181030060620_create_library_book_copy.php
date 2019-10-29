<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M181030060620_create_library_book_copy
 */
class M181030060620_create_library_book_copy extends Migration
{
    protected $tableName = '{{%library_book_copy}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'book_id' => $this->integer()->unsigned()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
        ],  $this->getTableOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M181030060620_create_library_book_copy cannot be reverted.\n";

        return false;
    }
    */
}
