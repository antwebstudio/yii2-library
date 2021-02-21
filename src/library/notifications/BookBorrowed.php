<?php
namespace ant\library\notifications;

use Yii;
use tuyakhov\notifications\NotificationInterface;
use tuyakhov\notifications\NotificationTrait;
use ant\user\models\User;

class BookBorrowed implements NotificationInterface {
    use NotificationTrait;
	
	protected $bookBorrowed;
    protected $user;
	
    public function __construct($bookBorrowed, User $borrower) {
		$this->bookBorrowed = is_array($bookBorrowed) ? $bookBorrowed : [$bookBorrowed];
        $this->borrower = $borrower;
	}
	
    public function exportForMail() {
        $policy = Yii::$app->getModule('library')->getPolicy($this->borrower);

        return \Yii::createObject([
           'class' => '\tuyakhov\notifications\messages\MailMessage',
           'subject' => 'Receipt for book borrowed',
           'view' => ['html' => '@ant/library/mails/book-borrowed'],
           'viewData' => [
               'borrowedBooks' => $this->bookBorrowed,
               'maximumRenew' => $policy->getMaxRenew(),
           ]
        ]);
    }
}