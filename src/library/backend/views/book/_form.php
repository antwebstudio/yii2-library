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

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'small_title')->textInput() ?>
	
    <?= $form->field($model, 'language')->dropdownlist([
		Book::LANGUAGE_CHINESE => 'Chinese',
		Book::LANGUAGE_ENGLISH => 'English',
		Book::LANGUAGE_MALAY => 'Malay',
		Book::LANGUAGE_UNKNOWN => 'Unknown',
	]) ?>

    <?= $form->field($model, 'category_code')->textInput() ?>
    
    <?= $form->field($model, 'category_ids')->widget(\kartik\select2\Select2::className(), [
        'model' => $model,
        'maintainOrder' => true,
        'options' => [
            'placeholder' => 'Search for a category ...',
            'multiple' => true, 
        ],
        'data' => ArrayHelper::map(Category::find()->andWhere(['id' => $model->category_ids])->asArray()->all(), 'id', 'title'),
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/category/backend/category/ajax-list']),
                'dataType' => 'json',
                'delay' => 250,                
                'data' => new JsExpression('function(params) { return { q:params.term, page: params.page }; }'),
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

    <?= $form->field($model, 'author_ids')->widget(\kartik\select2\Select2::className(), [
        'model' => $model,
        'maintainOrder' => true,
        'options' => [
            'placeholder' => 'Search for a author ...',
            'multiple' => true, 
        ],
        'data' => ArrayHelper::map(BookAuthor::find()->andWhere(['id' => $model->author_ids])->asArray()->all(), 'id', 'name'),
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/library/backend/author/ajax-authors']),
                'dataType' => 'json',
                'delay' => 250,                
                'data' => new JsExpression('function(params) { return { q:params.term, page: params.page }; }'),
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
    
    <?= $form->field($model, 'publisher_id')->widget(\kartik\select2\Select2::className(), [
        'data' => ArrayHelper::map(BookPublisher::find()->andWhere(['id' => $model->publisher_id])->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => 'Search for a publisher ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/library/backend/publisher/ajax-publishers']),
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
	<?php $defaultOptions = ['暂时借用', '永久捐献'] ?>
	
    <?= $form->field($model, 'adminUseTagsValue')->widget(\kartik\select2\Select2::className(), [
        'data' => array_merge(array_combine((array) $model->adminUseTagsValue, (array) $model->adminUseTagsValue), array_combine($defaultOptions, $defaultOptions)),
        'options' => ['placeholder' => 'Add a tag ...', 'multiple' => true],
		
        'pluginOptions' => [
            'tags' => true,
        ],
    ]) ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'newCopyQuantity')->textInput(['type' => 'number']) ?>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create and create another' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>