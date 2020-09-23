<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = (isset($model->category->type->title) ? $model->category->type->title : 'Category').': '.$model->category->title;
?>

<?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'udc') ?>
    <?= $form->field($model, 'dewey_my') ?>
    <?= $form->field($model, 'dewey_tw') ?>
    <?= $form->field($model, 'custom') ?>

    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>