<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Picqer\Barcode\BarcodeGeneratorSVG;
use ant\library\models\BookCopy;
use ant\library\models\BookPublisher;
use ant\category\models\Category;
/* @var $this yii\web\View */
$generator = new Picqer\Barcode\BarcodeGeneratorSVG();
?>

<?php $this->beginBlock('content-header-buttons') ?>
    <?= Html::a('Print Stickers', ['print-sticker'], ['class' => 'btn btn-sm btn-primary']) ?>
    <?= Html::a('Re-print Stickers', ['print-sticker', 'status' => BookCopy::STICKER_LABEL_NEED_REPRINT], ['class' => 'btn btn-sm btn-primary']) ?>
	
	<?= Html::a('Add New Book', ['/library/backend/book/create'], ['class' => 'btn btn-success']) ?>
<?php $this->endBlock() ?>

<?php /*
    <div class="row">
        <div class="col-md-2">
            <?= Html::dropDownList('owner_user_id', '', ['c'=>'Confirmed','nc'=>'No Confirmed'], ['prompt' => 'Mark selected owned by: ', 'class' => 'form-control col-md-2']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton('Mark', ['class' => 'btn btn-info',]);?>
        </div>
    </div>
*/ ?>
<div class="table-responsive">
<?= \yii\grid\GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $model,
	'columns' => [
		// [
		// 	'class' => '\yii\grid\CheckboxColumn',
		// ],
		[
			'attribute' => BookCopy::barcodeAttribute() == 'id' ? 'barcodeId' : BookCopy::barcodeAttribute(),
			'value' => 'barcode',
		],
		// [
		// 	'attribute' => 'book_id',
		// 	'label' => 'Book Id',
		// ],
		[
			'attribute' => 'isbn',
			'value' => 'book.isbn',
		],
		[
			'attribute' => 'title',
			'value'=> 'book.title',
			'headerOptions' => [
				'style' => 'min-width: 200px',
			],
		],
		[
			'label' => 'Category 分类',
			'attribute' => 'category',
			'value' => 'defaultCategoryName',
			'headerOptions' => ['style' => 'min-width: 100px'],
			// 'filter' => Category::find()->count() > 200 ? null : Select2::widget([
			// 	'model' => $searchModel,
			// 	'attribute' => 'category_ids',
			// 	'data' => ArrayHelper::map(Category::find()->all(), 'id', 'title'),
			// 	'size' => Select2::SMALL,
			// 	'options' => ['placeholder' => ''],
			// 	'pluginOptions' => [
			// 		'allowClear' => true
			// 	],
			// ]),
		],
		// 'book.category_code',
		'bookShelfCode',
		[
			'filter' => BookPublisher::find()->count() > 100 ? null : Select2::widget([
				//'value' => $model,
				//'name' => 'IncidentSearch[customer_id]',
				'model' => $searchModel,
				'attribute' => 'publisher_id',
				'data' => ArrayHelper::map(BookPublisher::find()->all(), 'id', 'name'),
				'size' => Select2::SMALL,
				'options' => ['placeholder' => ''],
				'pluginOptions' => [
					'allowClear' => true
				],
			]),
			'attribute' => 'publisher',
			'value' => 'book.publisher.name',
			'label' => 'Publisher 出版社',
		],
		[
			'format' => 'raw',
			'label' => 'Barcode',
			'value' => function($model) use ($generator) {
				return $generator->getBarcode((string)$model->id, $generator::TYPE_CODE_128);
			}
		],
		//'created_at',
		//'created_by',
		//'updated_at',
		//'updated_by',
		[
			'label' => 'Borrowed?',
			'attribute' => 'isBorrowed',
			'format' => 'html',
			'value' => function ($model) {
				if ($model->isBorrowed) {
					$borrow = $model->getBookBorrow()->expireLast()->notReturned()->excludeReserved()->one();
					$until = $borrow->expireAt->format('Y-m-d');
				}
				return $model->isBorrowed ? 'Yes<br/>'.$until : 'No';
			}
		],
		[
			'label' => 'Reserved?',
			'attribute' => 'isReserved',
			'format' => 'html',
			'value' => function ($model) {
				if ($model->isReserved) {
					$borrow = $model->getBookBorrow()->expireLast()->reserved()->one();
					$until = $borrow->expireAt->format('Y-m-d');
				}
				return $model->isReserved ? 'Yes<br/>'.$until : 'No';
			}
		],
		[
			'class' => 'ant\grid\ActionColumn',
			'template' => '{view} {update} {delete} {mark-sticker-label}',
			'buttons' => [
				'mark-sticker-label' => function($url, $model) {
					if ($model->sticker_label_status != BookCopy::STICKER_LABEL_NEED_REPRINT) {
						return Html::a('Mark Print Sticker', ['mark-sticker-label-status', 'id' => $model->id, 'status' => BookCopy::STICKER_LABEL_NEED_REPRINT], [
							'class' => 'btn btn-default',
						]);
					} else {
						return Html::a('Mark As Sticker Printed', ['mark-sticker-label-status', 'id' => $model->id, 'status' => BookCopy::STICKER_LABEL_PRINTED], [
							'class' => 'btn btn-default',
						]);
					}
				},
			],
		],
	],
]) ?>
</div>