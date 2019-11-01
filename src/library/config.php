<?php

return [
    'id' => 'library',
    'class' => \ant\library\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		'v1' => \ant\library\api\v1\Module::class,
		'backend' => \ant\library\backend\Module::class,
	],
	'depends' => [], 
];