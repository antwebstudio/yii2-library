<?php

namespace ant\library\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M181030065358_library_permission extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\library\backend\controllers\MobileController::className() => [
                'index' => ['Mobile version', [Role::ROLE_ADMIN]],
                'member' => ['Mobile version', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\BookController::className() => [
                'index' => ['View book list', [Role::ROLE_ADMIN]],
                'create' => ['Create book', [Role::ROLE_ADMIN]],
                'update' => ['Update book', [Role::ROLE_ADMIN]],
				'delete' => ['Delete book', [Role::ROLE_SUPERADMIN]],
			],
			\ant\library\backend\controllers\AuthorController::className() => [
                'index' => ['View author list', [Role::ROLE_ADMIN]],
                'create' => ['Create author', [Role::ROLE_ADMIN]],
                'update' => ['Update author', [Role::ROLE_ADMIN]],
				'delete' => ['Delete author', [Role::ROLE_SUPERADMIN]],
				'ajax-authors' => ['Delete author', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\PublisherController::className() => [
                'index' => ['View publisher list', [Role::ROLE_ADMIN]],
                'create' => ['Create publisher', [Role::ROLE_ADMIN]],
                'update' => ['Update publisher', [Role::ROLE_ADMIN]],
				'delete' => ['Delete publisher', [Role::ROLE_SUPERADMIN]],
				'ajax-publishers' => ['Delete author', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\BookCopyController::className() => [
                'index' => ['View book copies list', [Role::ROLE_ADMIN]],
                'sticker' => ['Print book copies sticker', [Role::ROLE_ADMIN]],
                'ajax-list' => ['Get book copies list by ajax', [Role::ROLE_ADMIN]],
                'delete' => ['Delete book copy', [Role::ROLE_ADMIN]],
				'mark-sticker-label-status' => ['Mark book copy sticker label status', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\BorrowController::className() => [
                'index' => ['Borrow a book', [Role::ROLE_ADMIN]],
				'renew' => ['Renew a book', [Role::ROLE_ADMIN]],
				'borrowed' => ['List all book borrowed', [Role::ROLE_ADMIN]],
                'ajax-users' => ['Search a user by ajax', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\ReturnController::className() => [
                'index' => ['Return a book', [Role::ROLE_ADMIN]],
			],
			\ant\library\backend\controllers\MemberController::className() => [
                'index' => ['Manage members', [Role::ROLE_ADMIN]],
                'pay-deposit' => ['Pay deposit money', [Role::ROLE_ADMIN]],
                'return-deposit' => ['Return paid deposit money', [Role::ROLE_ADMIN]],
			],
		];
		
		parent::init();
    }

	public function up()
    {
        //rbac migration
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
