<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use ant\category\models\Category;
use ant\library\models\Book;
use ant\library\models\BookAuthor;
use ant\library\models\BookPublisher;
?>
<div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>

    <?= $form->field($model, 'custom_barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shelf_mark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>