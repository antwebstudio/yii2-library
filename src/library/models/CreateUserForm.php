<?php
namespace ant\library\models;

use Yii;
use yii\db\ActiveRecord;
use ant\helpers\StringHelper;
use ant\user\models\SignupForm;

class CreateUserForm extends \ant\web\FormModel {
	const SCENARIO_BACKEND = SignupForm::SCENARIO_BACKEND;
	const TYPE_LIBRARY = 'library';
	const TYPE_RK = 'member';
	
	const LIBRARY_MEMBER = 1;
	const LIBRARY_SPECIAL_MEMBER = 2;

	public $validSignupType = [self::TYPE_RK, self::TYPE_LIBRARY];
	public $memberType;
	public $agreeTnc;
	public $agreeDeclaration;
	public $userIp;
	public $password;
	public $confirmPassword;
	public $reCaptcha;
	public $roleName = [
		self::TYPE_RK => ['rk-member', 'library-member'],
		self::TYPE_LIBRARY => ['library-member'],
	];
	public $signupTypeQueryParamName = 'signupType';

	public function scenarios() {
		return \yii\helpers\ArrayHelper::merge(parent::scenarios(), [
			self::SCENARIO_BACKEND => [],
		]);
	}

	public function rules() {
		return \yii\helpers\ArrayHelper::merge(parent::rules(), [
			[['memberType'], 'required', 'message' => 'Please select member type. '],
			[['agreeDeclaration', 'agreeTnc'], 'required', 'requiredValue' => 1, 'message' => 'Agreement needed to continue.'],
			//[['agreeDeclaration', 'agreeTnc'], 'boolean'],
			['reCaptcha', \himiklab\yii2\recaptcha\ReCaptchaValidator2::className(),
				// 'secret' => 'your secret key', // unnecessary if reСaptcha is already configured
				'when' => function() {
					$key = env('RECAPTCHA_SECRET_KEY');
					return trim($key) != '';
				},
				'uncheckedMessage' => 'Please check this to continue.'
			],
		]);
	}
	
	public function configs() {
		return [
			'user' => [
				'class' => 'ant\user\models\User',
                'scenario' => \ant\user\models\User::SCENARIO_EMAIL_AS_USERNAME,
                'on '.\yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
                    $user = $event->sender;
                    $user->username = $user->email;
                    $user->generateAuthKey();
                    
					$password = isset($this->password) ? $this->password : StringHelper::generateRandomString(8);
                    $user->setPassword($password);
                },
				'on '.ActiveRecord::EVENT_AFTER_INSERT => function($event) {
					$user = $event->sender;
					
					\Yii::$app->authManager->revokeAll($user->id);
		
					// Assign new role for new user
					foreach ((array) $this->roleName[$this->getSignupType()] as $roleName) {
						$role = \ant\user\rbac\Role::ensureRole($roleName, null);
						$role->assign($user);
					}
				},
				'as configurable' => [
					'class' => 'ant\behaviors\ConfigurableModelBehavior',
					'extraRules' => [
						[['email'], 'email'],
					],
					'extraAttributeLabels' => [
						'email' => 'Email 電郵',
					]
				],
			],
			'identity:optional' => [
				'class' => 'ant\user\models\UserIdentity',
                'on '.\yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
					$identity = $event->sender;
					$identity->type = 'ic';
                },
                'on '.\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => function($event) {
                    $identity = $event->sender;
                    $identity->user_id = $this->user->id;
                },
				'as configurable' => [
					'class' => 'ant\behaviors\ConfigurableModelBehavior',
					'extraAttributeLabels' => [
						'value' => 'I/C no. 身份證號',
					],
					'extraRules' => [
						['value', 'ant\user\validators\MalaysiaIcOrAnyPassportValidator'],
					],
				],
			],
			'contact:optional' => [
				'class' => 'ant\contact\models\Contact',
                'on '.\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => function($event) {
                    $model = $event->sender;
					$model->email = $this->user->email;
				},
			],
			'profile:optional' => [
				'class' => 'ant\user\models\UserProfile',
                'on '.\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => function($event) {
                    $model = $event->sender;
					$model->main_profile = 1;
					$model->email = $this->user->email;
                    $model->user_id = $this->user->id;
				},
				'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) {
					$model = $event->sender;
					$this->contact->attributes = [
						'contact_number' => $this->profile->contact_number,
						'email' => $this->profile->email,
						'firstname' => $this->profile->firstname,	
						'lastname' => $this->profile->lastname,
					];
					$model->link('contact', $this->contact);
				},
				'as configurable' => [
					'class' => 'ant\behaviors\ConfigurableModelBehavior',
					'extraRules' => [
						[['lastname', 'contact_number'], 'required'],
						['data', 'ant\validators\SerializableDataValidator', 'rules' => $this->scenario == self::SCENARIO_BACKEND ? [] : [
							[['origin', 'city', 'language'], 'required'],
							[['speciality', 'affiliation', 'interest'], 'required'],
						]],
					],
					'extraAttributeLabels' => [
						'lastname' => 'Name (as per IC) 姓名（根据身份证）',
						'contact_number' => 'H/P no. 手機號碼',
						'memberType' => 'Member Type 會員類別',
						'data[origin]' => 'Place of Origin 來自哪裡',
						'data[city]' => 'Current City 長居城市',
						'data[language]' => 'Languages 擅長及其它可溝通的語言',
						'data[speciality]' => 'Strength / Specialities / Occupation 专长 / 专研领域 / 职业',
						'data[affiliation]' => 'Affiliation 所屬機構 / 社團',
						'data[interest]' => 'Interest 興趣',
						'data[readingPreference]' => 'Reading Preference 想閱讀的書類',
					],
				],
			],
		];
	}

	public function init() {
		if (!in_array($this->getSignupType(), $this->validSignupType)) {
			throw new \Exception('Invalid signup type. ');
		}
	}

	public function getSignupType() {
		return Yii::$app->request->get($this->signupTypeQueryParamName, self::TYPE_LIBRARY);
	}
	
	public function getFormAttributes($name = null) {
		$attributes = [
			'user' => [
				'email' => [
					'attribute' => 'email',
				],
			],
			'identity' => [
				'value' => [
					'attribute' => 'value',
					'label' => 'IC Number',
				],
			],
			'profile' => [
				/*'firstname' => [
					'attribute' => 'firstname',
				],*/
				'lastname' => [
					'attribute' => 'lastname',
				],
				'contact_number' => [
					'attribute' => 'contact_number',
				],
				'data[origin]' => [
					'attribute' => 'data[origin]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[origin]'),
				],
				'data[city]' => [
					'attribute' => 'data[city]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[city]'),
				],
				'data[language]' => [
					'attribute' => 'data[language]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[language]'),
				],
				'data[speciality]' => [
					'attribute' => 'data[speciality]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[speciality]'),
				],
				'data[affiliation]' => [
					'attribute' => 'data[affiliation]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[affiliation]'),
				],
				'data[interest]' => [
					'attribute' => 'data[interest]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[interest]'),
				],
				'data[readingPreference]' => [
					'attribute' => 'data[readingPreference]',
					'label' => $this->getModel('profile')->getAttributeLabel('data[readingPreference]'),
				],
			],
		];
		return $attributes[$name];
    }
	
	public function signup() {
		return $this->save();
	}
}