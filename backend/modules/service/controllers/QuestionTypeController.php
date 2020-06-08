<?php

namespace backend\modules\service\controllers;

use backend\models\ManagerLog;
use backend\modules\service\helpers\Api;
use backend\modules\service\models\QuestionType;
use yii;
use yii\web\Controller;

/**
 * 客服咨询类型管理
 * @author wlw
 * @date   2018-09-13
 *
 */
class QuestionTypeController extends Controller
{
    /**
     * 添加咨询类型
     */
    public function actionIndex()
    {
        $this->view->title = '问题类型列表';
        $handler           = Api::getQuestionTypes();
        $handler->run();

        if (!$handler->isSuccess()) {
            Yii::$app->session->setFlash('error', '网络错误');
        }
        $resp = $handler->getContents();

        $questionTypeList = json_decode($resp, true);
        $questionTypeList = $questionTypeList ?? [];
        return $this->render('index', ['questionTypeList' => $questionTypeList]);
    }

    /**
     * 添加咨询类型
     */
    public function actionAdd()
    {
        $this->view->title = '问题类型添加';
        $model             = new QuestionType();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $handler = Api::addQuestionType(json_encode($model));
            $res     = $handler->run();

            if ($res === false) {
                Yii::$app->session->setFlash('error', '网络错误 httpCode:' . $handler->getHttpCode());
            } else {
                $res = json_decode($res, true);
                if ($res['error_code'] == 0) {
                    ManagerLog::saveLog(Yii::$app->user->id, "客服设置-问题类型设置", ManagerLog::CREATE, "添加问题类型");
                    Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect('index');
                } else {
                    Yii::$app->session->setFlash('error', $res['msg']);
                }
            }
        }

        $model->is_show = 1;
        //获取咨询类型
        $questionTypes = Api::getAdvisoryTypes(1)->run();
        $questionTypes = json_decode($questionTypes, true);
        $questionTypes = array_combine(array_keys($questionTypes), array_column($questionTypes, 'advisory_type_name'));
        return $this->render('add', ['model' => $model, 'questionTypes' => $questionTypes]);
    }

    public function actionUpdate()
    {
        $this->view->title = '问题类型修改';
        $id                = Yii::$app->request->get('id', 0);
        $handler           = Api::getQuestionType($id);
        $handler->run();
        if (!$handler->isSuccess()) {
            die($handler->getHttpCode());
            Yii::$app->session->setFlash('error', '网络错误');
        }

        $res = json_decode($handler->getContents(), true);
        if ($res['error_code'] != 0) {
            Yii::$app->session->setFlash('error', $res['msg']);
        }

        $model = new QuestionType();
        $model->load($res['data'], '');
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $handler = Api::updateQuestionType(json_encode($model));
            $res     = $handler->run();
            if (!$handler->isSuccess()) {
                die($handler->getHttpCode());
                Yii::$app->session->setFlash('error', '网络错误');
            } else {
                $res = json_decode($res, true);
                if ($res['error_code'] == 0) {
                    ManagerLog::saveLog(Yii::$app->user->id, "客服设置-问题类型设置", ManagerLog::UPDATE, "编辑问题类型");
                    Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect('index');
                } else {
                    Yii::$app->session->setFlash('error', $res['msg']);
                }
            }
        }

        //获取咨询类型
        $questionTypes = Api::getAdvisoryTypes(1)->run();
        $questionTypes = json_decode($questionTypes, true);
        $questionTypes = array_combine(array_keys($questionTypes), array_column($questionTypes, 'advisory_type_name'));
        return $this->render('update', ['model' => $model, 'questionTypes' => $questionTypes]);
    }
}
