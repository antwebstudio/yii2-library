<?php
namespace ant\library\notifications;

use tuyakhov\notifications\NotificationInterface;
use tuyakhov\notifications\NotificationTrait;

class BookBorrowed implements NotificationInterface {
    use NotificationTrait;
	
	protected $bookBorrowed;
	
    public function __construct($bookBorrowed) {
		$this->bookBorrowed = is_array($bookBorrowed) ? $bookBorrowed : [$bookBorrowed];
	}
	

    public function exportForMail() {
        return \Yii::createObject([
           'class' => '\tuyakhov\notifications\messages\MailMessage',
           'subject' => 'Receipt for book borrowed',
           'view' => ['html' => '@ant/library/mails/book-borrowed'],
           'viewData' => [
               'borrowedBooks' => $this->bookBorrowed,
           ]
        ]);
    }
}