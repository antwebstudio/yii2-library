<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use ant\widgets\GridView;
use ant\category\models\Category;

/* @var $this yii\web\View */
$model = new \ant\library\models\Book;

$columns = [
	[
		'label' => isset($model) ? $model->getAttributeLabel('id') : null,
		'attribute' => 'id',
	],
	[
		'label' => isset($model) ? $model->getAttributeLabel('isbn') : null,
		'attribute' => 'isbn',
	],
	[
		'label' => isset($model) ? $model->getAttributeLabel('title') : null,
		'attribute' => 'title',
	],
	//'small_title',
	//'category_code',
	[
		'label' => '分类',
		'value' => function($model) {
			$category = $model->getCategories()->one();
			return isset($category) ? $category->title : null;
		},
		'filter' => Select2::widget([
			//'value' => $model,
			//'name' => 'IncidentSearch[customer_id]',
			'model' => $searchModel,
			'attribute' => 'category_ids',
			'data' => ArrayHelper::map(Category::find()->all(), 'id', 'title'),
			'size' => Select2::SMALL,
			'options' => ['placeholder' => ''],
			'pluginOptions' => [
				'allowClear' => true
			],
		]),
	],
	[
		'attribute' => 'shortCategoryCode',
		'value' => function($model) {
			$data = [
				'030' => '综合参考资料',
				'008' => '文明、文化、进展',
				'940' => '历史',
				'94(3/9)' => '古代和现代地方史',
				'94(5)' => '亚洲史',
				'945' => '本土历史',
				'100' => '哲学类',
				'200' => '宗教',
				'300' => '社会科学',
				'305' => '性别研究. 性别角色. 性别. 多学科角度的人',
				'320' => '政治',
				'323' => '民政事务、内政',
				'330' => '经济',
				'710' => '实际规划.区域,城市与乡村规划',
				'150' => '心理学',
				'360' => '社会保障',
				'370' => '教育',
				'500' => '数学、自然科学',
				'655' => '制图工业、印刷、出版、图书贸易',
				'659' => '大众传播，大规模公众告知',
				'070' => '报纸. 新闻界. 包括: 新闻工作',
				'700' => '艺术',
				'791' => '电影',
				'792' => '戏剧',
				'780' => '音乐',
				'778' => '摄影',
				'820' => '文学',
				'821' => '诗、诗歌、韵文',
				'823' => '小说.散文叙事',
				'824' => '散文',
				'828' => '杂记、多图作品、选集',
				'829' => '其他各种体裁包括: 通俗文学、新闻、随想、论战和政治著作、小册子、历史作为文学体裁、儿童和少年文学、历史写作、史学、编年史、记录、回忆录、期刊、日记、传记、自传、批判、评论、文学虚构等',
				'741' => '漫画、卡通、讽刺和幽默画',
				'910' => '一般问题、地理作为一门科学、探索、旅游',
				'945' => '本土历史',
			];
			
			$name = isset($data[$model->shortCategoryCode]) ? $data[$model->shortCategoryCode] : null;
			
			return isset($name) ? $model->shortCategoryCode.' - '.$name : $model->shortCategoryCode;
		},
	],
	'bookShelfCode',
	'publisher_id',
	//'created_at',
	//'created_by',
	//'updated_at',
	//'updated_by',
	['class' => 'yii\grid\ActionColumn'],
];
?>

<?php echo Html::a('Add New Book', ['create'], ['class' => 'btn btn-success']) ?>

<?=  \customit\excelreport\ExcelReport::widget([
    'columns' => $columns,
    'dataProvider' => $searchModel->getDataProviderForExport(),
]) ?>

<?= GridView::widget([
	'autoXlFormat' => true,
	'filterModel' => $searchModel,
	/*'export'=>[
		'fontAwesome' => true,
		'showConfirmAlert' => false,
		'target' => \kartik\grid\GridView::TARGET_BLANK
	],*/
	'panel'=>[
		'type' => 'default',
		'heading' => 'Book',
	],
    'dataProvider' => $dataProvider,
    'columns' => $columns,
]) ?>