<?php
use yii\widgets\ActiveForm; 
use yii\widgets\Pjax; 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use ant\helpers\StringHelper as Str;
use ant\helpers\ArrayHelper as Arr;

$this->title = 'Borrow Book';

$showDetail = Yii::$app->request->post() && $model->validate();
$showDetail = Yii::$app->request->post();
$userIc = isset($model->user) ? $model->user->getIdentityId()->andWhere(['type' => 'ic'])->one() : null;
?>

<?php if ($showDetail): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Book Detail</div>
                <div class="panel-body">
                    <?php if (isset($model->bookCopy->book)): ?>
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $model->bookCopy->book,
                            'attributes' => [
                                'id',
                                'title',
                                'languageText',
                            ],
                        ]) ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Member Detail</div>
                <div class="panel-body">
                    <?php if (isset($model->user)): ?>
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $model->user,
                            'attributes' => [
                                'id',
                                'username',
                                'email',
                                'profile.contact_number',
                                [
                                    'attribute' => 'identityId.value',
                                    'label' => 'IC Number',
                                    'value' => isset($userIc) ? $userIc->value : null,
                                ],
                                'membershipExpireAt',
                                [
                                    'format' => 'html',
                                    'label' => 'Borrowed Book',
                                    'value' => '<ul>'.Arr::implode('', $model->getBookBorrowedInfo(), function($row) {
                                        return '<li>'.$row.'</li>';
                                    }).'</ul>',
                                ],
                            ],
                        ]) ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'bookCopyId')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'userId')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'confirm')->hiddenInput(['value' => 1])->label(false) ?>

        <?= Html::submitButton('Confirm', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
    <?php ActiveForm::end() ?>
<?php else: ?>

    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'bookCopyId')->textInput() ?>
        
        <?= $form->field($model, 'userId')->widget(\kartik\select2\Select2::className(), [
            //'data' => ArrayHelper::map(BookPublisher::find()->andWhere(['id' => $model->publisher_id])->asArray()->all(), 'id', 'name'),
            'options' => ['placeholder' => 'Search for a user ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/library/backend/borrow/ajax-users']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                    'processResults' => new JsExpression('function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    }'),
                    'cache' => true
                ],
				'templateSelection' => new JsExpression('function(data) {
					if (data !== undefined && data.firstname !== undefined) {
						return data.firstname + (data.identityId[0] !== undefined ? " - " + data.identityId[0].value : "") + (data.email !== undefined ? " - " + data.email : "");
					}
					return data.text;
				}'),
				'templateResult' => new JsExpression('function(data) {
					console.log(data);
					if (data !== undefined && data.firstname !== undefined) {
						return data.firstname + (data.identityId[0] !== undefined ? " - " + data.identityId[0].value : "") + (data.email !== undefined ? " - " + data.email : "");
					}
					return "Searching ... ";
				}'),
            ],
        ]) ?>

        <?php /*
        <?= $form->field($model, 'bookCopyId')->widget(\kartik\select2\Select2::className(), [
            //'data' => ArrayHelper::map(BookPublisher::find()->andWhere(['id' => $model->publisher_id])->asArray()->all(), 'id', 'name'),
            'options' => ['placeholder' => 'Search for a book ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/library/book-copy/ajax-list']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                    'processResults' => new JsExpression('function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    }'),
                    'cache' => true
                ],
            ],
        ]) ?>
        */ ?>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php endif ?>