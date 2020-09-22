<?php
if (isset($borrowedBooks[0])) {
	$user = $borrowedBooks[0]->user;
	$userIc = isset($user) ? $user->getIdentityId()->andWhere(['type' => 'ic'])->one() : null;
}
?>
<?php if (isset($userIc)): ?>
	<p>Member ID: <?= $userIc->value ?></p>
<?php endif ?>

<p>Kindly reminded to return the book(s) to us before it is expired. <br/>
Please reply to this email to renew</p>

<table style="width: 100%; margin-bottom: 10px; border-color: 1px #cccccc solid; border-collapse: collapse;" border="1">
	<thead>
		<tr>
			<th style="padding: 3px 5px; text-align: left">Barcode</th>
			<th style="padding: 3px 5px; text-align: left">Book Title</th>
			<th style="padding: 3px 5px; text-align: left">Expire Date</th>
			<th style="padding: 3px 5px; text-align: left">Renewed (max 1)</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($borrowedBooks as $borrowed): ?>
			<?php $book = $borrowed->bookCopy->book ?>
			<tr>
				<td style="padding: 3px 5px; text-align: left"><?= $borrowed->bookCopy->barcode ?></td>
				<td style="padding: 3px 5px; text-align: left"><?= $book->title ?></td>
				<td style="padding: 3px 5px; text-align: left"><?= $borrowed->expireAt ?></td>
				<td style="padding: 3px 5px; text-align: left"><?= $borrowed->renewCount ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<p>Thank you.</p>