<?php

namespace ant\library;

/**
 * library module definition class
 */
class Module extends \yii\base\Module
{
    public $barcode;

    public $category;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function getPolicy($user) {
        return \ant\library\models\LibraryPolicy::for($user);
    }
}
