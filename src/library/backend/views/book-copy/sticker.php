<?php
use yii\helpers\Url;
use yii\widgets\ListView;

$this->context->layout = '//_clear';

\project\themes\kongsi\assets\PrintPageAsset::register($this);
?>
<style>
    .stickers .sticker { 
		padding: 4mm 2mm 4mm 4mm; width: 6cm; height: 2.8cm; border: 1px #eeeeee solid; float:left; 
		/*background: url('/yellow.png');*/
	}
    @media print {
        .pagination, footer, .d-print-none { display: none; }
        .stickers .page-break {
            page-break-after:always;
            clear:both;
            margin: 0mm;
        }
		.page-start {
            clear:both;
			/*margin-top: 10mm;*/
		}
        body {
			margin-top: 0mm;
			margin-bottom: 0mm;
            margin-left: 10mm;
			margin-right: 10mm;
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
