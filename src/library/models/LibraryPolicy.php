<?php
namespace ant\library\models;

class LibraryPolicy {
    protected $user;

    protected $_item;
    protected $_memberPackage;
   
    public function __construct($user) {
        $this->user = $user;
        $this->_item = $this->getMemberTypePackageItem($this->user);
        $this->_memberPackage = $this->getMemberPackage($this->user);
    }

    public static function for($user) {
        return static::forUser($user);
    }

    public static function forUser($user) {
        return new static($user);
    }

    public function getMemberTypeName() {
        return $this->_memberPackage->name ?? null;
    }

    public function getRenewDays() {
        return $this->_item->content_valid_period ?? 0;
    }

    public function getBorrowDays() {
        if ($this->getMaxBorrow()) {
            return $this->_item->content_valid_period;
        }
    }

    public function getDepositNeeded() {
        return $this->_item->options['depositAmount'] ?? 0;
    }

    public function getMaxBorrow() {
        return $this->_item->book_limit ?? null;
    }

    public function getMaxReserve() {

    }

    public function getMaxRenew() {
        return $this->_item->max_no_of_renew ?? null;
    }

    protected function getMemberPackage($user) {
        $userId = is_object($user) ? $user->id : $user;
        $subscription = \ant\subscription\models\Subscription::find()->currentlyActiveForUser($userId)
            ->type('member')
            ->isPaid()
            ->orderBy('expire_at DESC')
            ->one();

        return isset($subscription) ? $subscription->package : null;
    }

    protected function getMemberTypePackageItem($user) {
        $memberType = $this->getMemberPackage($user);
        if (isset($memberType)) {
            return $memberType->packageItems[0];
        }
    }
}
