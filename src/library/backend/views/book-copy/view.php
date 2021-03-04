<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('actions') ?>
    <a href="<?= Url::to(['/library/backend/book-copy']) ?>" class="btn btn-dark">Back</a>
<?php $this->endBlock() ?>
<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'barcode',
        'book.title',
        'book.languageText',
        
        [
            'label' => 'Currently borrowed?',
			'format' => 'html',
			'value' => function ($model) {
				if ($model->isBorrowed) {
					$borrow = $model->getBookBorrow()->expireLast()->notReturned()->excludeReserved()->one();
					$until = $borrow->expireAt->format('Y-m-d');
                    
                    $userIc = $borrow->user->getIdentityId()->andWhere(['type' => 'ic'])->one();
                    $who = $borrow->user->username . '<br/>'.$borrow->user->profile->firstname.' '.$borrow->user->profile->lastname.'<br/>'.($userIc->value ?? '');
				}
				return $model->isBorrowed ? 'Yes<br/>'.$until.'<br/>'.$who : 'No';
			}
        ],
        [
            'label' => 'Currently reserved?',
			'format' => 'html',
			'value' => function ($model) {
				if ($model->isReserved) {
					$borrow = $model->getBookBorrow()->expireLast()->reserved()->one();
					$until = $borrow->expireAt->format('Y-m-d');

                    $userIc = $borrow->user->getIdentityId()->andWhere(['type' => 'ic'])->one();
                    $who = $borrow->user->username . '<br/>'.$borrow->user->profile->firstname.' '.$borrow->user->profile->lastname.'<br/>'.($userIc->value ?? '');
				}
				return $model->isReserved ? 'Yes<br/>'.$until.'<br/>'.$who : 'No';
			}
        ],
    ],
]) ?>


