<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$validUntil = new DateTime();
$status = Yii::$app->request->get('status');
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['sticker']]) ?>
	<?= Html::label('Book Registered Date') ?>
	
	<?= \ant\widgets\DateRangePicker::widget([
		//'startAttribute' => 'startDateTime',
		//'endAttribute' => 'endDateTime',
		//'model' => $model,
		//'attribute' => 'range',
		'name' => 'date',
		'rangePreset' => \ant\widgets\DateRangePicker::RANGE_BY_MONTH,
		'validDate' => [
			[null, $validUntil->format('Y-m-d')],
		],
		//'options' => ['data' => ['method' => 'get']],
	]) ?>
	
	<?= Html::hiddenInput('status', $status) ?>
	
	<?= Html::submitButton('Print', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>