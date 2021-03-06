<?php
use yii\helpers\Url;
use ant\widgets\Nav;

$bookCopyId = Yii::$app->request->get('copy');
$backUrl = isset($bookCopyId) ? ['/library/backend/book-copy'] : ['/library/backend/book'];
?>

<?php $this->beginBlock('actions') ?>
    <a class="btn btn-dark float-right" href="<?= Url::to($backUrl) ?>">Back</a>
<?php $this->endBlock('actions') ?>

<?php if(isset($bookCopyId)): ?>
<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    // 'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
    'items' => [
        [
            'label' => 'Book Copy',
            'url' => Url::to(['/library/backend/book-copy/update', 'id' => $bookCopyId]),
        ],
        [
            'label' => 'Book Information',
            'active' => true,
        ],
    ],
]) ?>
<?php endif ?>

<?php echo $this->render('_form', [
    'model' => $model,
]) ?>