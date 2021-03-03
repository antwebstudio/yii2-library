<?php 
use ant\library\models\Book;
use ant\library\models\BookCopy;
use ant\user\models\User;
use ant\helpers\DateTime;

class BookCopyCest
{
    public function _before(UnitTester $I)
    {
    }
	
    public function _fixtures()
    {
        return [
            'user' => \tests\fixtures\UserFixture::className(),
        ];
    }

    // tests
    public function testLendTo(UnitTester $I)
    {
        $expectedExpireAt = new DateTime();
        $expectedExpireAt->addDays(10);
        $user = $I->grabFixture('user', 0);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        $borrow = $bookCopy->lendTo($user, 10);
        $expireAt = new DateTime($borrow->expireAt);

        $I->assertFalse($bookCopy->isAvailableForBorrow);
        $I->assertFalse($bookCopy->isAvailableForBorrow($user));
        $I->assertFalse($bookCopy->isReserved);
        $I->assertTrue($bookCopy->isBorrowed);
        $I->assertEquals($expireAt->format('Y-m-d'), $expectedExpireAt->format('Y-m-d'));
    }

    public function testReserveBy(UnitTester $I) 
    {
        $expectedExpireAt = new DateTime();
        $expectedExpireAt->addDays(10);
        
        $user = $I->grabFixture('user', 0);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        $borrow = $bookCopy->reserveBy($user, 10);
        $expireAt = new DateTime($borrow->expireAt);

        $I->assertFalse($bookCopy->isAvailableForBorrow);
        $I->assertTrue($bookCopy->isAvailableForBorrow($user));
        $I->assertTrue($bookCopy->isReserved);
        $I->assertFalse($bookCopy->isBorrowed);
        $I->assertEquals($expireAt->format('Y-m-d'), $expectedExpireAt->format('Y-m-d'));
    }

    public function testReserveByAfterLendTo(UnitTester $I) {

        $expectedExpireAt = new DateTime();
        $expectedExpireAt->addDays(20); // 10 + 10
        
        $user1 = $I->grabFixture('user', 0);
        $user2 = $I->grabFixture('user', 1);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        // Lend to before reserve
        $bookCopy->lendTo($user1, 10);

        $borrow = $bookCopy->reserveBy($user2, 10);
        $expireAt = new DateTime($borrow->expireAt);

        $I->assertFalse($bookCopy->isAvailableForBorrow);
        $I->assertFalse($bookCopy->isAvailableForBorrow($user2)); // Since lend to $user1
        $I->assertTrue($bookCopy->isReserved);
        $I->assertTrue($bookCopy->isBorrowed);
        $I->assertEquals($expireAt->format('Y-m-d'), $expectedExpireAt->format('Y-m-d'));
    }

    public function testReserveByAfterReserve(UnitTester $I) {

        $expectedExpireAt = new DateTime();
        $expectedExpireAt->addDays(20); // 10 + 10
        
        $user1 = $I->grabFixture('user', 0);
        $user2 = $I->grabFixture('user', 1);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        // First reserve
        $bookCopy->reserveBy($user1, 10);

        $borrow = $bookCopy->reserveBy($user2, 10);
        $expireAt = new DateTime($borrow->expireAt);

        $I->assertFalse($bookCopy->isAvailableForBorrow);
        $I->assertTrue($bookCopy->isAvailableForBorrow($user1));
        $I->assertFalse($bookCopy->isAvailableForBorrow($user2));
        $I->assertTrue($bookCopy->isReserved);
        $I->assertFalse($bookCopy->isBorrowed);
        $I->assertEquals($expireAt->format('Y-m-d'), $expectedExpireAt->format('Y-m-d'));
    }

    public function testIsBorrableAfterReturned(UnitTester $I) {
        
        $expectedExpireAt = new DateTime();
        $expectedExpireAt->addDays(20); // 10 + 10
        
        $user1 = $I->grabFixture('user', 0);
        $user2 = $I->grabFixture('user', 1);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        // Lend to before reserve
        $borrow = $bookCopy->lendTo($user1, 10);

        $bookCopy->reserveBy($user2, 10);

        $borrow->markAsReturned(null);

        $I->assertFalse($bookCopy->isAvailableForBorrow);
        $I->assertFalse($bookCopy->isAvailableForBorrow($user1));
        $I->assertTrue($bookCopy->isAvailableForBorrow($user2));
        $I->assertTrue($bookCopy->isReserved);
        $I->assertFalse($bookCopy->isBorrowed);
    }

    /**
     * Scenario: Two user reserve the book copy
     * Expected result: Only current reservee can borrow the book copy
     */
    public function testIsAvailableForBorrowWhenReservedByTwoUser(UnitTester $I) {
        $user1 = $I->grabFixture('user', 0);
        $user2 = $I->grabFixture('user', 1);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        $bookCopy->reserveBy($user1, 10);
        $bookCopy->reserveBy($user2, 10);

        $I->assertTrue($bookCopy->isAvailableForBorrow($user1));
        $I->assertFalse($bookCopy->isAvailableForBorrow($user2));
    }

    /**
     * Scenario: Book copy reserved is lend to reservee
     * Expected: Book copy no more "isReserved"
     */
    public function testIsReservedAfterLendToReservee(UnitTester $I) {
        $user1 = $I->grabFixture('user', 0);
        $user2 = $I->grabFixture('user', 1);
        
        $book = Book::createOrFail([
            'title' => 'Test Title',
        ]);

        $bookCopy = BookCopy::findOne(['book_id' => $book->id]);

        // Lend to before reserve
        $borrow = $bookCopy->lendTo($user1, 10);

        $bookCopy->reserveBy($user2, 10);

        $borrow->markAsReturned(null);

        $borrow = $bookCopy->lendTo($user2, 10);

        $I->assertFalse($bookCopy->isReserved);
        $I->assertTrue($bookCopy->isBorrowed);
    }
}
