<?php

namespace ant\library\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class PrintPageAsset extends AssetBundle
{
	public $sourcePath = __DIR__.'/print-page';
	
    public $css = [
        'style.css',
		'pagination.css',
		'button.css',
    ];

    public $js = [
    ];
	
    public $depends = [
        //'yii\bootstrap\BootstrapAsset',
    ];

    public $fonts = [
    ];
}
