<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\WxTagSearch;
use common\helpers\WXApi\Tag;
use common\models\WxMemberTagAssoc;
use common\models\WxTag;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * WxTagController implements the CRUD actions for WxTag model.
 */
class WxTagController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Lists all WxTag models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看标签')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new WxTagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new WxTag model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加标签')) {
            return $this->redirect(['site/login']);
        }
        $model = new WxTag();
        $data  = Yii::$app->request->post();
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if ($data) {
            if (!$model->load($data) || !$model->save()) {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $data['WxTag']['tagid'] = $model->tagid;
            $wxTagObj               = new Tag();
            $res                    = $wxTagObj->tagAdd($data['WxTag']);
            if ($res !== 'created') {
                Yii::$app->getSession()->setFlash('error', '添加标签接口失败' . $res);
                $transaction->rollBack();
                return $this->redirect(['index']);
            }
            $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "标签管理", ManagerLog::CREATE, $model->tagname);
            if (!$managerLogRes) {
                $transaction->rollBack();die('操作日志添加失败');
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WxTag model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑标签')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $data  = Yii::$app->request->post();
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        if ($model->load($data) && $model->save()) {
            $data['WxTag']['tagid'] = $model->tagid;
            $wxTagObj               = new Tag();
            $res                    = $wxTagObj->tagEdit($data['WxTag']);
            if ($res != 'updated') {
                echo '更新标签接口调用失败' . $res;
                $transaction->rollBack();die;
            }
            $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "标签管理", ManagerLog::UPDATE, $model->tagname);
            if (!$managerLogRes) {
                $transaction->rollBack();die('操作日志添加失败');
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WxTag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除标签')) {
            return $this->redirect(['site/login']);
        }
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        if ($model->delete()) {
            $wxTagObj = new Tag();
            $res      = $wxTagObj->tagDel($id);
            if ($res == 'deleted' || $res == 'invalid tagid') {
                $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "标签管理", ManagerLog::DELETE, $model->tagname);
                if (!$managerLogRes) {
                    $transaction->rollBack();die('操作日志添加失败');
                }
                $transaction->commit();
                return $this->redirect(['index']);
            } else {
                echo '删除失败,失败原因：' . $res;
            }
            $transaction->rollBack();
        } else {
            echo '删除失败';
        }

    }
    /**
     * 添加标签用户
     * @return [type]
     */
    public function actionTagUserAdd()
    {
        if (!Yii::$app->user->can('编辑标签成员')) {
            return $this->redirect(['site/login']);
        }
        $model = new WxMemberTagAssoc();
        $data  = Yii::$app->request->post('WxMemberTagAssoc');
        if ($data) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($data['wx_memberid'] as $v) {

                $_model              = clone $model;
                $_model->wx_memberid = $v;
                $_model->wx_tagid    = $data['wx_tagid'];
                if (!$_model->save()) {
                    die('添加标签成员失败');
                }
            }
            // 调用企业微信添加标签成员接口
            $tagUserAdd                  = new Tag();
            $qywxTagUserData['tagid']    = $data['wx_tagid'];
            $qywxTagUserData['userlist'] = $data['wx_memberid'];
            $res                         = $tagUserAdd->tagUserAdd($qywxTagUserData);
            if ($res != 'ok') {
                $transaction->rollBack();die('添加企业微信标签成员失败');
            }
            //添加操作日志
            $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "标签用户管理", ManagerLog::CREATE, $data['wx_tagid']);
            if (!$managerLogRes) {
                $transaction->rollBack();die('操作日志添加失败');
            }
            $transaction->commit();
            return $this->redirect(['index']);
        }
        $tagid = Yii::$app->request->get('id');
        return $this->render('tagUserAdd', [
            'model' => $model,
            'tagid' => $tagid,
        ]);
    }

    public function actionTagUserDel()
    {
        if (!Yii::$app->user->can('删除标签成员')) {
            return $this->redirect(['site/login']);
        }
        $model = new WxMemberTagAssoc();
        $data  = Yii::$app->request->post('WxMemberTagAssoc');
        if ($data) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            $delres      = WxMemberTagAssoc::deleteAll(array('wx_memberid' => $data['wx_memberid'], 'wx_tagid' => $data['wx_tagid']));
            if (!$delres) {
                die('删除失败');
            }

            // 调用企业微信添加标签成员接口
            $tagUserAdd                  = new Tag();
            $qywxTagUserData['tagid']    = $data['wx_tagid'];
            $qywxTagUserData['userlist'] = $data['wx_memberid'];
            $res                         = $tagUserAdd->tagUserDel($qywxTagUserData);
            if ($res != 'deleted') {
                $transaction->rollBack();die('删除企业微信标签成员失败');
            }
            //添加操作日志
            $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "标签用户管理", ManagerLog::DELETE, $data['wx_tagid']);
            if (!$managerLogRes) {
                $transaction->rollBack();die('操作日志添加失败');
            }
            $transaction->commit();
            return $this->redirect(['index']);
        }
        $tagid = Yii::$app->request->get('id');
        return $this->render('tagUserDel', [
            'model' => $model,
            'tagid' => $tagid,
        ]);
    }

    /**
     * Finds the WxTag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WxTag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WxTag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 同步企业微信中的标签
     * @return [type]
     */
    public function actionSyncTag()
    {
        $result    = true;
        $tagIdArr  = [];
        $wxTagObj  = new Tag();
        $wxTagList = $wxTagObj->tagList();
        if (!is_array($wxTagList)) {
            $result = false;
        }

        foreach ($wxTagList as $tagArr) {
            //同步标签
            $tagIdArr[] = $tagArr['tagid'];
            $_model     = WxTag::findOne(['tagid' => $tagArr['tagid']]);
            $_model     = empty($_model) ? new WxTag() : $_model;
            $_model->setAttributes($tagArr);
            if (!$_model->save()) {
                $result = false;
            }

            // 同步标签下的成员
            $wxTagUserList = $wxTagObj->tagUserList($tagArr['tagid']);
            foreach ($wxTagUserList as $tagUserArr) {
                $_model = WxMemberTagAssoc::findOne(['wx_memberid' => $tagUserArr['userid'], 'wx_tagid' => $tagArr['tagid']]);
                if ($_model) {
                    continue;
                }

                $_model              = new WxMemberTagAssoc();
                $_model->wx_memberid = $tagUserArr['userid'];
                $_model->wx_tagid    = $tagArr['tagid'];
                if (!$_model->save()) {
                    $result = false;
                }
            }
            $tagUsers = ArrayHelper::getColumn($wxTagUserList, 'userid');
            // 删除不在企业微信标签中的用户
            $delModel = WxMemberTagAssoc::deleteAll(['and', ['not in', 'wx_memberid', $tagUsers], ['wx_tagid' => $tagArr['tagid']]]);

        }

        // 删除企业微信中没有的标签
        $delModel = WxTag::deleteAll(['not in', 'tagid', $tagIdArr]);

        if ($result) {
            return $this->redirect(['index']);
        } else {
            die('同步失败');
        }

    }

}
