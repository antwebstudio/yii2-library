<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

$model = new \ant\library\models\BorrowBookForm;
?>

This is book is reserved by someone, process it now?
<?php $form = ActiveForm::begin(['action' => Url::to(['/library/backend/borrow']), 'layout' => ActiveForm::LAYOUT_INLINE]) ?>
    <?= $form->field($model, 'customBarcode')->hiddenInput(['value' => $bookCopy->barcode])->label(false) ?>
    <?= $form->field($model, 'bookCopyId')->hiddenInput(['value' => $bookCopy->id])->label(false) ?>
    <?= $form->field($model, 'userId')->hiddenInput(['value' => $bookCopy->currentReservee->id])->label(false) ?>

    <button class="btn btn-primary">Yes</button>
    <a href="<?= Url::to($skipUrl) ?>" class="btn btn-link">Skip</a>
<?php ActiveForm::end() ?>