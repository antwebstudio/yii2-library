<?php
use yii\helpers\Url;
use yii\widgets\ListView;
?>
<style>
    .stickers .sticker { 
		padding: 4mm 2mm 4mm 4mm; width: 6cm; height: 2.8cm; border: 1px #eeeeee solid; float:left; 
		/*background: url('/yellow.png');*/
	}
    @media print {
        .pagination, footer { display: none; }
        .stickers .page-break {
            page-break-after:always;
            clear:both;
            margin: 0mm;
        }
        body {
            margin: 0mm;
			/*background: url('/yellow.png');*/
        }
    }
</style>
<?php if ($dataProvider->totalCount): ?>
<script>
    window.print();
</script>
<?php endif ?>

<a class="btn btn-primary d-print-none" href="<?= Url::to(['print-sticker']) ?>">Back</a>

<div class="stickers clearfix">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_sticker',
        'layout' => '{pager} {items}',
		'viewParams' => ['stickerPerPage' => $stickerPerPage],
    ]) ?>
</div>
