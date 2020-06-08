<?php

namespace frontend\controllers;

use Yii;
use backend\models\DistributionNoticeRead;
use backend\models\DistributionNoticeReadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\DistributionNotice;

/**
 * DistributionNoticeReadController implements the CRUD actions for DistributionNoticeRead model.
 */
class DistributionNoticeReadController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionNoticeRead models.
     * @return mixed
     */
    public function actionIndex()
    {
        //查询用户为：leizhiliang 的所有数据  , 'read_status'=>0
        $list = DistributionNoticeRead::getDistributionNoticeList('',['userId'=>$this->userinfo['userid']]);
        return $this->render("index", [
            'list'=>$list,
        ]);
    }

    /**
     *  加载详情页
     *  @param $notice_id
     **/
    public function actionDetial($notice_id, $notice_read_id){
        $noticeList     =   DistributionNotice::find()->where(['Id'=>$notice_id])->asArray()->one();
        $noticeReadList =   DistributionNoticeRead::find()->where(['Id'=>$notice_read_id])->asArray()->one();
        return $this->render('detial', [
            'noticeList' => $noticeList,
            'noticeReadList' => $noticeReadList,
        ]);
    }

    /**
     *  处理反馈的逻辑
     **/
    public function actionNoticeReadSuccess(){
        $param  =   Yii::$app->request->get();
        $model  =   DistributionNoticeRead::findOne(['userId'=>$this->userinfo['userid'], 'notice_id'=>$param['notice_id'] ]);
        $model->read_status     =   1;
        $model->read_time       =   time();
        $model->read_feedback   =   $param['read_feedback'];
        if($model->save()){
            return $this->redirect(['index']);
        }else{
            echo "添加配送阅读表失败。";exit();
        }
    }

}
