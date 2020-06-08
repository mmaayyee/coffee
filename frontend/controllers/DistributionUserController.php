<?php
namespace frontend\controllers;

use backend\models\ScmMaterial;
use backend\models\ScmUserSurplusMaterialSearch;
use backend\models\ScmUserSurplusMaterialSureRecord;
use backend\models\ScmUserSurplusMaterialSureRecordSearch;
use backend\models\ScmUserSurplusMaterialSureRecordGramSearch;
use backend\models\ScmUserSurplusMaterialSureRecordGram;
use Yii;

/**
 * 配送人员相关数据
 */
class DistributionUserController extends BaseController
{

    /**
     * 配送员剩余物料列表
     * @author  zgw
     * @version 2016-10-20
     * @return  [type]     [description]
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'author' => $this->userinfo['userid']
        ]);
    }

    /**
     * 剩余物料修改申请记录
     * @author  zgw
     * @version 2016-10-21
     * @return  [type]     [description]
     */
    public function actionRecord()
    {
        $searchModel         = new ScmUserSurplusMaterialSureRecordSearch();
        $dataProvider        = $searchModel->search(['ScmUserSurplusMaterialSureRecordSearch' => ['author' => $this->userinfo['userid']]]);
        $gramSearchModel     = new ScmUserSurplusMaterialSureRecordGramSearch();
        $gramDataProvider    = $gramSearchModel->search(['ScmUserSurplusMaterialSureRecordGramSearch'  => ['author' => $this->userinfo['userid']]]);
        $searchModel         = new ScmUserSurplusMaterialSearch();
        $surplusMaterialList = $searchModel->userSearch($this->userinfo['userid']);

        return $this->render('record', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'gramDataProvider' => $gramDataProvider,
            'is_show'      => $surplusMaterialList ? 1 : 0,
        ]);
    }

    /**
     * 添加剩余物料修改申请
     * @author  zgw
     * @version 2016-10-21
     * @return  [type]     [description]
     */
    public function actionCreate()
    {
        // 获取剩余物料
        $searchModel = new ScmUserSurplusMaterialSearch();
        $surplusMaterialList = $searchModel->userSearch($this->userinfo['userid']);
        //获取剩余散料
        $gramList = $searchModel->getGramList($this->userinfo['userid']);
        // 声明剩余物料记录
        $model = new ScmUserSurplusMaterialSureRecord();
        $data = Yii::$app->request->post();
        $result = false;
        if(isset($data['material_gram']) && !empty($data['material_gram']) && $data['reason']){
            $result = false;
            //增加散料记录
            foreach($data['material_gram'] as $materialGram){
                $gramModel = new ScmUserSurplusMaterialSureRecordGram();
                if ($materialGram['material_gram']) {
                    $gramModel->material_type_id = $materialGram['material_type_id'];
                    $gramModel->supplier_id = $materialGram['supplier_id'];
                    $gramModel->material_gram = intval($materialGram['material_gram']);
                    $gramModel->add_reduce = $materialGram['add_reduce'];
                    $gramModel->reason = $data['reason'];
                    $gramModel->author = $this->userinfo['userid'];
                    $gramModel->date = date('Y-m-d');
                    $gramModel->createTime = time();
                    if ($gramModel->validate() && $gramModel->save()) {
                        $result = true;
                    }
                }
            }

        }
        if (isset($data['material']) && !empty($data['material']) && $data['reason']) {
            foreach ($data['material'] as $materialArr) {
                if ($materialArr['material_num']) {
                    $model = new ScmUserSurplusMaterialSureRecord();
                    $model->material_id = $materialArr['material_id'];
                    $model->add_reduce = $materialArr['add_reduce'];
                    $model->material_num = intval($materialArr['material_num']);
                    $model->reason = $data['reason'];
                    $model->author = $this->userinfo['userid'];
                    $model->date = date('Y-m-d');
                    $model->createTime = time();
                    if ($model->validate() && $model->save()) {
                        $result = true;
                    }
                }
            }
        }

        if($result === true){
            return $this->redirect(['record']);
        }

        if(!isset($data['material_gram']) && !isset($data['material'])){
            return $this->render('create', [
                'model' => $model,
                'surplusMaterialList' => $surplusMaterialList,
                'gramList'            => $gramList
            ]);
        }
    }

    /**
     * 修改申请
     * @author  zgw
     * @version 2016-10-21
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public function actionUpdate($id)
    {
        $model = ScmUserSurplusMaterialSureRecord::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['record']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 修改散料申请
     * @author wxl
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateGram($id = 0){
        $model = ScmUserSurplusMaterialSureRecordGram::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['record']);
        }
        return $this->render('update_gram', [
            'model' => $model,
        ]);
    }

}
