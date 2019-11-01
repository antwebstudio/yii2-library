<?php
use yii\widgets\ActiveForm; 
use yii\widgets\Pjax; 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use ant\helpers\StringHelper;
use ant\library\models\BookBorrow;

$this->title = 'Return Book';

$daysCount = 1; // How many before borrowed book expired
$expiredBorrowDataProvider = new \yii\data\ActiveDataProvider([
	'query' => BookBorrow::find()->notReturned(),
]);
?>

<?php if (Yii::$app->request->post() && $model->validate()): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Book Detail</div>
                <div class="panel-body">
					<?= \yii\widgets\DetailView::widget([
						'model' => $model,
						'attributes' => [
							'bookCopy.book.id',
							'bookCopy.book.title',
							'bookCopy.book.languageText',
							'bookBorrow.expireAt',
						],
					]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Member Detail</div>
                <div class="panel-body">
					<?= \yii\widgets\DetailView::widget([
						'model' => $model->user,
						'attributes' => [
							'id',
							'username',
							'email',
							'profile.contact_number',
                            'membershipExpireAt',
							[
								'format' => 'html',
								'label' => 'Borrowed Book',
								'value' => '<ul>'.StringHelper::forEach($model->getBookBorrowedInfo(), function($row) {
									return '<li>'.$row.'</li>';
								}).'</ul>',
							],
						],
					]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'bookCopyId')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'confirm')->hiddenInput(['value' => 1])->label(false) ?>

        <?= Html::submitButton('Confirm', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php else: ?>

    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'bookCopyId')->textInput() ?>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php endif ?>