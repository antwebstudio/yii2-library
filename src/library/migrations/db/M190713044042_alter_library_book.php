<?php

namespace ant\library\migrations\db;

use yii\db\Migration;

/**
 * Class M190713044042_alter_book
 */
class M190713044042_alter_library_book extends Migration
{
	protected $tableName = '{{%library_book}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'trashed_remark', $this->string()->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'trashed_remark');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190713044042_alter_book cannot be reverted.\n";

        return false;
    }
    */
}
