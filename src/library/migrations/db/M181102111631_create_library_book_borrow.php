<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M181102111631_create_library_book_borrow
 */
class M181102111631_create_library_book_borrow extends Migration
{
    protected $tableName = '{{%library_book_borrow}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'book_copy_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'remark' => $this->string()->null()->defaultValue(null),
            'borrow_days' => $this->smallInteger()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'returned_at' => $this->timestamp()->defaultValue(null),
            'returned_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
        ],  $this->getTableOptions());

		$this->addForeignKeyTo('{{%library_book_copy}}', 'book_copy_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);
		$this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);

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
