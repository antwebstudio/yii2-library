<?php

namespace ant\library\models;

use Yii;
use Overtrue\Pinyin\Pinyin;
use ant\category\models\Category;
use ant\category\models\CategoryType;
use ant\library\models\CategoryCode;

/**
 * This is the model class for table "ks_library_book".
 *
 * @property int $id
 * @property string $isbn
 * @property string $title
 * @property string $small_title
 * @property int $publisher_id
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property BookPublisher $publisher
 * @property BookAuthorMap[] $libraryBookAuthorMaps
 */
class Book extends \yii\db\ActiveRecord
{
    use \ant\db\traits\ActiveRecordShortcut;
    
    const LANGUAGE_ENGLISH = 2;
    const LANGUAGE_CHINESE = 1;
	const LANGUAGE_MALAY = 3;
    const LANGUAGE_UNKNOWN = 0;

    const CATEGORY_TYPE = 'book';

    public $newCopyQuantity = 1;
    public $categories;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_book}}';
    }

    public function behaviors() {
        //$categoryType = CategoryType::find()->andWhere(['type' => self::CATEGORY_TYPE])->one();
        return [
            [
                'class' => \ant\behaviors\EventHandlerBehavior::className(),
                'events' => [
                    self::EVENT_AFTER_INSERT => [$this, 'afterInsert'],
                ],
            ],
            [
                'class' => \ant\category\behaviors\CategorizableBehavior::className(),
                'attribute' => 'categories',
                'type' => self::CATEGORY_TYPE,
				'modelClassId' => \ant\models\ModelClass::getClassId((self::class)),
            ],
			[
				'class' => \voskobovich\linker\LinkerBehavior::className(),
				'relations' => [
                    'author_ids' => 'authors',
                    'category_ids' => ['categories', 'updater' => [
                        'viaTableAttributesValue' => [
                            'model_class_id' => \ant\models\ModelClass::getClassId((self::class)),
                        ]],
                    ],
				],
			],
			[
				'class' => \ant\tag\behaviors\TaggableBehavior::class,
				'relation' => 'adminUseTags',
				'attribute' => 'adminUseTagsValue',
				'modelClassId' => \ant\models\ModelClass::getClassId((self::class)),
			],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            //[['title'], 'unique'],
			//[['isbn'], 'unique'],
            [['title', 'isbn'], 'unique', 'targetAttribute' => ['title', 'isbn']],
            // [['title', 'small_title'], 'ant\validators\ChineseStringValidator'],
            [['newCopyQuantity'], 'integer', 'min' => 0],
            [['created_by', 'updated_by'], 'integer'],
            [['publisher_id'], 'integer', 'enableClientValidation' => false],
            [['adminUseTagsValue', 'category_code', 'created_at', 'updated_at'], 'safe'],
            [['isbn'], 'string', 'max' => 20],
            [['title', 'small_title'], 'string', 'max' => 255],
            [['author_ids', 'category_ids'], 'each', 'rule' => ['integer']],
            [['language'], 'default', 'value'=> 0],
            [['publisher_id'], 'exist', 'skipOnError' => true, 'targetClass' => BookPublisher::className(), 'targetAttribute' => ['publisher_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'isbn' => 'Isbn',
            'title' => 'Title',
            'small_title' => 'Small Title',
            'publisher_id' => 'Publisher ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
	
	public static function importerConfig() {
		return [
			'book.title', // Need to specify model name before attribute name
			'book.isbn',
			'book.publisher_id' => function($value) {
				$value = (new \SteelyWing\Chinese\Chinese)->to(\SteelyWing\Chinese\Chinese::CHS, $value);
				$publisher = \ant\library\models\BookPublisher::find()->andWhere(['name' => $value])->one();
				
				if (!isset($publisher)) {
					$publisher = new \ant\library\models\BookPublisher;
					$publisher->name = $value;
					if (!$publisher->save()) throw new \Exception(print_r($publisher->errors, 1));
				}
				return $publisher->id;
			},
			'book.author_ids' => function($value) {
				$value = (new \SteelyWing\Chinese\Chinese)->to(\SteelyWing\Chinese\Chinese::CHS, $value);
				$author = \ant\library\models\BookAuthor::find()->andWhere(['name' => $value])->one();

				if (!isset($author)) {
					$author = new \ant\library\models\BookAuthor;
					$author->name = $value;
					if (!$author->save()) throw new \Exception(print_r($author->errors, 1));
				}
				return [$author->id];
			},
			'book.category_ids' => function($value) {
				// Category Code
				$code = \ant\library\models\CategoryCode::findOne([self::categoryCodeSystem() => $value]);
				if (isset($code)) return [$code->category_id];
				
				// Title
				if ($value != '') {
					$value = (new \SteelyWing\Chinese\Chinese)->to(\SteelyWing\Chinese\Chinese::CHS, $value);
					$category = \ant\category\models\Category::ensureByTitle($value, self::CATEGORY_TYPE);
					if (!isset($category)) throw new \Exception('Something is wrong');
					return [$category->id];
				}
			},
			'book.category_code',
			'book.language' => function($value) {
				$value = (new \SteelyWing\Chinese\Chinese)->to(\SteelyWing\Chinese\Chinese::CHS, $value);
				$nonChineseValue = strtoupper($value);
				
				if (in_array($value, ['中文', '华文', '华语', '华']) || in_array($nonChineseValue, ['CHINESE', 'CHI', 'C', 'BAHASA CINA'])) {
					return \ant\library\models\Book::LANGUAGE_CHINESE;
				} else if (in_array($value, ['英文', '英语', '英']) || in_array($nonChineseValue, ['ENGLISH', 'ENG', 'E', 'BI', 'BAHASA INGGERIS'])) {
					return \ant\library\models\Book::LANGUAGE_ENGLISH;
				} else if (in_array($value, ['马来文', '国文', '马来语', '国语', '国', '巫', '巫文']) || in_array($nonChineseValue, ['MALAY', 'M', 'MELAYU', 'BM', 'BAHASA MELAYU'])) {
					return \ant\library\models\Book::LANGUAGE_MALAY;
				}
			},
			'book.adminUseTagsValue' => function($value) {
				return \ant\helpers\ArrayHelper::trim(explode(',', $value));
			}
		];
	}

    public static function categoryCodeSystem() {
        $module = Yii::$app->getModule('library');
        return $type ?? ($module->category['code_system'] ?? 'udc');
    }
	
	public function getLanguageText() {
		switch ($this->language) {
			case self::LANGUAGE_ENGLISH:
				return 'English';
			case self::LANGUAGE_CHINESE:
				return 'Chinese';
			case self::LANGUAGE_MALAY:
				return 'Malay';
			default:
				return '(Unknown)';
		}
		
	}
	
	public function getAdminUseTags() {
		return $this->getBehaviorRelation(\ant\models\ModelClass::getClassId((self::class)));
	}

	public function getBehaviorRelation($modelClassId) {
		return $this->hasMany(\ant\tag\models\Tag::className(), ['id' => 'tag_id'])
			->onCondition(['model_class_id' => $modelClassId])
			->viaTable('{{%tag_map}}', ['model_id' => 'id']);
	}

	public function getCategories() {
        return $this->getCategoriesRelation(self::CATEGORY_TYPE);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublisher()
    {
        return $this->hasOne(BookPublisher::className(), ['id' => 'publisher_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(BookAuthor::className(), ['id' => 'author_id'])
            ->viaTable('{{%library_book_author_map}}', ['book_id' => 'id']);
    }

    public function getShortLanguageCode() {
        if ($this->language == self::LANGUAGE_ENGLISH) {
            return 'E';
        } else if ($this->language == self::LANGUAGE_CHINESE) {
            return 'C';
        } else if ($this->language == self::LANGUAGE_MALAY) {
			return 'M';
		}
    }
	
	public function getCategoryCode() {
		return $this->hasMany(CategoryCode::className(), ['category_id' => 'category_id'])
			->viaTable('{{%category_map}}', ['model_id' => 'id'], function($q) {
				$q->onCondition(['model_class_id' => \ant\models\ModelClass::getClassId((self::class))]);
			});
	}

    public function getShortCategoryCode($type = null) {
        $type = $type ?? self::categoryCodeSystem();
        
		return isset($this->categoryCode[0]) ? $this->categoryCode[0]->{$type} : null;
        //return isset($this->category_code) && $this->category_code ? substr($this->category_code, 0, 3) : '000';
    }

    public function getBookShelfCode() {
        // 小内存型
        $pinyin = new Pinyin(); // 默认
        // 内存型
        // $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        // I/O型
        // $pinyin = new Pinyin('Overtrue\Pinyin\GeneratorFileDictLoader');

        return strtoupper($pinyin->abbr($this->title, PINYIN_KEEP_ENGLISH + PINYIN_KEEP_NUMBER));

    }

    public function getImageFilename() {
        return $this->image ?? $this->isbn;
    }

    public function beforeValidate() {
        // Process category_ids
        $categoryIds = [];
        if (is_array($this->category_ids)) {
            foreach ($this->category_ids as $id) {
                if (!is_numeric($id)) {
                    $categoryTitle = substr($id, strlen('new:'));
                    $newCategory = \ant\category\models\Category::ensureByTitle($categoryTitle, self::CATEGORY_TYPE);

                    // $newCategory = new Category;
                    // $newCategory->title = $categoryTitle;
                    // $newCategory->type = self::CATEGORY_TYPE;
                    // if (!$newCategory->save()) throw new \Exception(print_r($newCategory->errors, 1));

                    $categoryIds[] = $newCategory->id;
                } else {
                    $categoryIds[] = $id;
                }
            }
            $this->category_ids = $categoryIds;
        }

        // Process author_ids
        $authorIds = [];
        if (is_array($this->author_ids)) {
            foreach ($this->author_ids as $id) {
                if (!is_numeric($id)) {
                    $newAuthor = new BookAuthor;
                    $newAuthor->name = substr($id, strlen('new:'));
                    if (!$newAuthor->save()) throw new \Exception(print_r($newAuthor->errors, 1));

                    $authorIds[] = $newAuthor->id;
                } else {
                    $authorIds[] = $id;
                }
            }
            $this->author_ids = $authorIds;
        }

        // Process publisher_id
        if (!is_numeric($this->publisher_id) && $this->publisher_id != '') {
            $newPublisher = new BookPublisher;
            $newPublisher->name = substr($this->publisher_id, strlen('new:'));
            if (!$newPublisher->save()) throw new \Exception(print_r($newPublisher->errors, 1));

            $this->publisher_id = $newPublisher->id;
        }

        return true;
    }

    public function afterInsert($event) {
        for ($i = 0; $i < $this->newCopyQuantity; $i++) {
            $newCopy = new BookCopy(['book_id' => $this->id]);

            if (!$newCopy->save()) throw new \Exception(print_r($newCopy->errors, 1));
        }
    }
}
