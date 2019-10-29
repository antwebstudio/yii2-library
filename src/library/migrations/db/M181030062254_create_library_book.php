<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M181030062254_create_library_book
 */
class M181030062254_create_library_book extends Migration
{
    protected $tableName = '{{%library_book}}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'isbn' => $this->string(20),
            'title' => $this->string()->notNull(),
            'small_title' => $this->string()->null()->defaultValue(null),
			'language' => $this->smallInteger(3)->notNull(),
            'publisher_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'category_code' => $this->string(20)->null()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(null),
        ],  $this->getTableOptions());

		$this->addForeignKeyTo('{{%library_book_publisher}}', 'publisher_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
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
        echo "M181030060254_create_library_book cannot be reverted.\n";

        return false;
    }
    */
}
