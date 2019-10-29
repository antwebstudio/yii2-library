<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M181102111631_create_library_book_borrow
 */
class M181102111631_create_library_category_code extends Migration
{
    protected $tableName = '{{%library_category_code}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'udc' => $this->string()->null()->defaultValue(null),
			'dewey_my' => $this->string(10)->null()->defaultValue(null),
			'dewey_tw' => $this->string(10)->null()->defaultValue(null),
			'custom' => $this->string(10)->null()->defaultValue(null),
        ],  $this->getTableOptions());

		$this->addForeignKeyTo('{{%category}}', 'category_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_RESTRICT);
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
        echo "M181102111631_create_library_book_borrow cannot be reverted.\n";

        return false;
    }
    */
}
