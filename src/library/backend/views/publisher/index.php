<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
?>

<?php echo Html::a('Add New Publisher', ['create'], ['class' => 'btn btn-success']) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
    ],
]) ?>