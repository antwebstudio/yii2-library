<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M181030061746_create_library_book_author_map
 */
class M181030062746_create_library_book_author_map extends Migration
{
    protected $tableName = '{{%library_book_author_map}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'book_id' => $this->integer()->unsigned()->notNull(),
            'author_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
        ],  $this->getTableOptions());

		$this->addForeignKeyTo('{{%library_book}}', 'book_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);
		$this->addForeignKeyTo('{{%library_book_author}}', 'author_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);
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
        echo "M181030061746_create_library_book_author_map cannot be reverted.\n";

        return false;
    }
    */
}
