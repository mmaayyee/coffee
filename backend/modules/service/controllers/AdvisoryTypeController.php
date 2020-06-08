<?php

namespace backend\modules\service\controllers;

use backend\models\ManagerLog;
use backend\modules\service\helpers\Api;
use backend\modules\service\models\AdvisoryType;
use yii;
use yii\web\Controller;

/**
 * 客服咨询类型管理
 * @author wlw
 * @date   2018-09-13
 *
 */
class AdvisoryTypeController extends Controller
{
    /**
     * 添加咨询类型
     */
    public function actionIndex()
    {
        $this->view->title = '咨询类型设置';
        $advisoryList      = Api::getAdvisoryTypes()->run();
        if ($advisoryList === false) {
            die('数据格式或网络错误');
        }
        $advisoryList = json_decode($advisoryList, true);

        return $this->render('index', ['advisoryList' => $advisoryList]);
    }
    /**
     * 添加咨询类型
     */
    public function actionAdd()
    {
        $this->view->title = '咨询类型添加';
        $model             = new AdvisoryType();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $handler = Api::addAdvisoryType(json_encode($model));
            $res     = $handler->run();
            if ($res === false) {
                Yii::$app->session->setFlash('error', '网络错误');
            } else {
                $res = json_decode($res, true);
                if ($res['error_code'] == 0) {
                    ManagerLog::saveLog(Yii::$app->user->id, "客服设置-咨询类型设置", ManagerLog::CREATE, "添加咨询类型");
                    Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect('index');
                } else {
                    Yii::$app->session->setFlash('error', $res['msg']);
                }
            }
        }

        $model->is_show = 1;
        return $this->render('add', ['model' => $model]);
    }

    public function actionUpdate()
    {
        $this->view->title = '咨询类型修改';
        $id                = Yii::$app->request->get('id', 0);
        $handler           = Api::getAdvisoryType($id);
        $handler->run();

        if (!$handler->isSuccess()) {
            die($handler->getHttpCode());
            Yii::$app->session->setFlash('error', '网络错误');
        }

        $res = json_decode($handler->getContents(), true);
        if ($res['error_code'] != 0) {
            Yii::$app->session->setFlash('error', $res['msg']);
        }

        $model = new AdvisoryType();
        $model->load($res['data'], '');
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $handler = Api::updateAdvisoryType(json_encode($model));
            $res     = $handler->run();
            if ($res === false) {
                die($handler->getHttpCode());
                Yii::$app->session->setFlash('error', '网络错误');
            } else {
                $res = json_decode($res, true);
                if ($res['error_code'] == 0) {
                    ManagerLog::saveLog(Yii::$app->user->id, "客服设置-咨询类型设置", ManagerLog::UPDATE, "编辑咨询类型");
                    Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect('index');
                } else {
                    Yii::$app->session->setFlash('error', $res['msg']);
                }
            }
        }

        return $this->render('update', ['model' => $model]);
    }
}
