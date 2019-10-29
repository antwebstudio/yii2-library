<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(['method' => 'get']) ?>
	<?= $form->field($model, 'identityId') ?>
	
	<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>

<?= \yii\widgets\ListView::widget([
	'dataProvider' => $dataProvider,
	'itemView' => '_member',
]) ?>