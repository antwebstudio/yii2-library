<?php

namespace ant\library\migrations\db;

use ant\db\Migration;

/**
 * Class M190729132856_create_library_deposit_money
 */
class M190729132856_create_library_deposit_money extends Migration
{
    protected $tableName = '{{%library_deposit_money}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'invoice_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'user_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'returned_at' => $this->timestamp()->defaultValue(null),
            'returned_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
        ],  $this->getTableOptions());

        $this->addForeignKeyTo('{{%payment_invoice}}', 'invoice_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_RESTRICT);
        $this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_RESTRICT);
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
        echo "M190729132856_create_library_deposit_money cannot be reverted.\n";

        return false;
    }
    */
}
