<?php
$generator = new Picqer\Barcode\BarcodeGeneratorSVG();
$nthCount = $stickerPerPage;
$id = $model->id;

//$id = 99999999;
$id = (string)$id;

if (strlen($id) < 2 && $barcodeType == Picqer\Barcode\BarcodeGeneratorSVG::TYPE_CODABAR) {
	$id = '0'.$id;
}
?>
<style>
	svg { max-width: 160px }
</style>
<?php if ($index % $nthCount == 0): ?>
    <div class="page-start">&nbsp;</div>
<?php endif ?>

<div class="sticker">
    <div style="margin-bottom: 2mm; font-size:8px; width: 85%; float:left; "><?= $generator->getBarcode($id, $barcodeType); ?><br/><?= $id ?></div>
    <span style="float: right; font-size:10px; writing-mode:vertical-rl; text-orientation: mixed; "><?= $model->book->getShortCategoryCode()  ?> (<?= $model->book->shortLanguageCode ?>)</span>
	<div style="width: 85%; float: left; overflow: hidden;">
		<div style="font-size:8px"><div style="white-space: nowrap; margin-bottom: 2px;"><?= $model->book->title ?></div><div><?= Yii::$app->name ?></div></div>
	</div>
</div>

<?php if (($index + 1) % $nthCount == 0): ?>
    <div class="page-break">&nbsp;</div>
<?php endif ?>