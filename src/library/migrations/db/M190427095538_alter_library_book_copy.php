<?php

namespace ant\library\migrations\db;

use yii\db\Migration;

/**
 * Class M190427095538_alter_book_Copy
 */
class M190427095538_alter_library_book_copy extends Migration
{
	protected $tableName = '{{%library_book_copy}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'is_trashed', $this->boolean()->notNull()->defaultValue(0));
		$this->addColumn($this->tableName, 'sticker_label_status', $this->smallInteger()->notNull()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'is_trashed');
		$this->dropColumn($this->tableName, 'sticker_label_status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190427095538_alter_book_Copy cannot be reverted.\n";

        return false;
    }
    */
}
