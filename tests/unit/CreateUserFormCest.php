<?php 

class CreateUserFormCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testSaveWithInvalidEmail(UnitTester $I)
    {
		$model = new \ant\library\models\CreateUserForm;
		$model->load([
			'User' => ['email' => 'invalid'],
		]);
		
		$I->assertFalse($model->validate());
		$I->assertTrue(isset($model->user->errors['email'][0]));
		$I->assertEquals($model->user->errors['email'][0], $model->user->getAttributeLabel('email').' is not a valid email address.');
    }
}
