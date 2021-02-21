<?php
namespace ant\library\backend\controllers;

use Yii;
use ant\user\models\User;
use ant\library\models\BookBorrow;
use ant\library\models\BookBorrowSearch;
use ant\library\models\BorrowBookForm;
use ant\library\models\CategoryCode;

class CategoryController extends \yii\web\Controller {
    public function actionUpdate($id) {
        $model = CategoryCode::findOne(['category_id' => $id]);
        if (!isset($model)) {
            $model = new CategoryCode;
            $model->category_id = $id;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Category updated successfully. ');
            return $this->redirect(['/category/backend', 'type' => $model->category->type->name ?? null]);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}