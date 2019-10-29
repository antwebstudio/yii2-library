<?php

namespace ant\library\migrations\db;

use yii\db\Migration;

/**
 * Class M190414071327_alter_library_book
 */
class M190414071327_alter_library_book extends Migration
{
	protected $tableName = '{{%library_book}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'is_trashed', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'is_trashed');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190414071327_alter_library_book cannot be reverted.\n";

        return false;
    }
    */
}
