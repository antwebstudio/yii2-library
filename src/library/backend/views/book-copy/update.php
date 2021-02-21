<?php
use yii\helpers\Url;
use ant\widgets\Nav;
?>
<?php $this->beginBlock('actions') ?>
    <a class="btn btn-dark float-right" href="<?= Url::to(['/library/backend/book-copy']) ?>">Back</a>
<?php $this->endBlock('actions') ?>

<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    // 'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
    'items' => [
        [
            'label' => 'Book Copy',
            'url' => Url::current(),
            'active' => true,
        ],
        [
            'label' => 'Book Information',
            'url' => Url::to(['/library/backend/book/update', 'id' => $model->book->id, 'copy' => $model->id]),
        ],
    ],
]) ?>

<?= $this->render('_form', ['model' => $model]) ?>