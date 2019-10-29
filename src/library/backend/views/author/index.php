<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
?>

<?php echo Html::a('Add New Author', ['create'], ['class' => 'btn btn-success']) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        //'created_at',
        //'created_by',
        //'updated_at',
        //'updated_by',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]) ?>