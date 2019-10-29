<?php

namespace ant\library\models;

use Yii;
use ant\user\models\User;
use ant\payment\models\Invoice;
use ant\payment\models\InvoiceItem;

/**
 * This is the model class for table "ks_library_deposit_money".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $user_id
 * @property string $updated_at
 * @property int $updated_by
 * @property string $created_at
 * @property int $created_by
 *
 * @property PaymentInvoice $invoice
 * @property User $user
 */
class DepositMoney extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_deposit_money}}';
    }

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\TimestampBehavior',
            ],
            [
                'class' => 'yii\behaviors\BlameableBehavior',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id', 'user_id', 'updated_by', 'created_by'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'user_id' => 'User ID',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function getIsPaid() {
        return $this->invoice->isPaid;
    }

    public function returnBy($userId) {
        $this->returned_by = $userId;
        $this->returned_at = new \yii\db\Expression('NOW()');

        return $this;
    }

    public function createInvoice($user, $price) {
        if (!isset($user->profile->contact)) throw new \Exception('Cannot issue invoice to user without default contact info. ');

        $invoice = new Invoice;
        $invoice->attributes = [
            'total_amount' => $price,
			'issue_to' => $user->id,
			'billed_to' => $user->profile->contact->id,
        ];
        if (!$invoice->save()) throw new \Exception(print_r($invoice->errors, 1));

        $invoiceItem = new InvoiceItem;
        $invoiceItem->attributes = [
            'title' => 'Library Deposit Money',
            'invoice_id' => $invoice->id,
            'unit_price' => $price,
        ];

        if (!$invoiceItem->save()) throw new \Exception(print_r($invoiceItem->errors, 1));
        
        $this->invoice_id = $invoice->id;

        return $invoice;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function checkIsPaid($userId) {
        $deposit = self::findOne(['user_id' => $userId, 'returned_by' => null, 'returned_at' => null]);
        return isset($deposit) && $deposit->isPaid;
    }
}
