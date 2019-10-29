<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Picqer\Barcode\BarcodeGeneratorSVG;
use ant\library\models\BookCopy;
/* @var $this yii\web\View */
$generator = new Picqer\Barcode\BarcodeGeneratorSVG();
?>

<?php $this->beginBlock('content-header-buttons') ?>
    <?= Html::a('Print Stickers', ['sticker'], ['class' => 'btn btn-sm btn-primary']) ?>
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

<?= \yii\grid\GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $model,
	'columns' => [
		[
			'class' => '\yii\grid\CheckboxColumn',
		],
		[
			'attribute' => 'barcodeId',
			//'label' => 'Barcode ID',
			'value' => function($model) {
				return $model->id;
			}
		],
		[
			'attribute' => 'book_id',
			'label' => 'Book Id',
		],
		'book.isbn',
		[
			'attribute'=> 'title',
			'value' => function($model) {
				return $model->book->title;
			}
		],
		'book.category_code',
		'book.bookShelfCode',
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
			'class' => 'yii\grid\ActionColumn',
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