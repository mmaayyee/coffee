<?php

namespace backend\controllers;

use backend\models\BlackAndWhiteList;
use backend\models\BlackAndWhiteListSearch;
use common\models\CoffeeBackApi;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * BlackAndWhiteListController implements the CRUD actions for BlackAndWhiteList model.
 */
class BlackAndWhiteListController extends Controller
{
    /**
     * Lists all BlackAndWhiteList models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看黑白名单')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new BlackAndWhiteListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new BlackAndWhiteList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加黑白名单')) {
            return $this->redirect(['site/login']);
        }
        $model           = new BlackAndWhiteList();
        $model->add_type = Yii::$app->request->get('add_type', 1);
        $data            = Yii::$app->request->post('BlackAndWhiteList');
        $message         = '请确认名单内容是否正确';
        if ($data) {
            $model->load(['BlackAndWhiteList' => $data]);
            $data['add_type'] = $model->add_type;
            if ($data['add_type'] == 1) {
                $data['user_content'] = explode('|', $data['user_content']);
            } else {
                $uploadFile = UploadedFile::getInstance($model, 'user_content');
                $filePath   = '../web/uploads/' . time() . '.' . $uploadFile->extension;
                if ($uploadFile->extension != 'txt') {
                    if ($data) {
                        Yii::$app->getSession()->setFlash('error', '请上传TXT格式的文件');
                    }
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                $uploadFile->saveAs($filePath);
                $content              = mb_convert_encoding(file_get_contents($filePath), "UTF-8", "gb2312,UTF-8");
                $content              = BlackAndWhiteList::clearBom($content);
                $data['user_content'] = array_filter(preg_split('/[;\r\n]+/s', $content));
                foreach ($data['user_content'] as $contents) {
                    if ($data['user_list_type'] == 1) {
                        // 1-手机号 2-楼宇
                        //判断数据是否正常
                        if (!is_numeric($contents)) {
                            Yii::$app->getSession()->setFlash('error', '请确认导入文件内容是否正确');
                            @unlink($filePath);
                            return $this->render('create', [
                                'model' => $model,
                            ]);
                        }
                    }
                }
                $message = '请确认导入文件内容是否正确';
                @unlink($filePath);
            }
            $saveBlackAndWhiteList = CoffeeBackApi::saveBlackAndWhiteList($data);
            if ($saveBlackAndWhiteList['error_code'] == 1) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "黑白名单管理", \backend\models\ManagerLog::CREATE, "添加黑白名单");
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', $saveBlackAndWhiteList['msg']);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlackAndWhiteList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑黑白名单')) {
            return $this->redirect(['site/login']);
        }
        $model = new BlackAndWhiteList();
        $model->load(['BlackAndWhiteList' => CoffeeBackApi::getBlackAndWhiteListInfo($id)]);
        $data = Yii::$app->request->post('BlackAndWhiteList');
        if (CoffeeBackApi::UpdateBlackAndWhiteListRemark($data)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "黑白名单管理", \backend\models\ManagerLog::UPDATE, "编辑黑白名单");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除黑白名单
     * @author  zgw
     * @version 2017-09-05
     * @param   integer     $id 黑白名单id
     * @return  fixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除黑白名单')) {
            return $this->redirect(['site/login']);
        }
        if (CoffeeBackApi::deleteBlackAndWhiteList($id)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "黑白名单管理", \backend\models\ManagerLog::DELETE, "删除黑白名单");
            return $this->redirect(['index']);
        }
    }

    /**
     * 批量移除黑白名单
     * @author  zgw
     * @version 2017-09-06
     * @return  int      1-移除成功 2-移除失败
     */
    public function actionBatchUpdate()
    {
        if (Yii::$app->request->isAjax) {
            $userList   = Yii::$app->request->post();
            $saveResult = CoffeeBackApi::UpdateBlackAndWhiteListRemark($userList);
            if (!$saveResult) {
                return false;
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "黑白名单管理", \backend\models\ManagerLog::DELETE, "批量删除黑白名单");
            return true;
        }
    }

    /**
     * 批量移除黑白名单
     * @author  zgw
     * @version 2017-09-06
     * @return  int      1-移除成功 2-移除失败
     */
    public function actionBatchDelete()
    {
        if (Yii::$app->request->isAjax) {
            $userIDs    = Yii::$app->request->post('userID');
            $saveResult = CoffeeBackApi::deleteBlackAndWhiteList($userIDs);
            if (!$saveResult) {
                return false;
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "黑白名单管理", \backend\models\ManagerLog::DELETE, "批量删除黑白名单");
            return true;
        }
    }

}
