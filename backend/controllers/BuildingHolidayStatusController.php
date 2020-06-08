<?php

namespace backend\controllers;

use backend\models\BuildingHolidayStatus;
use backend\models\BuildingHolidayStatusSearch;
use backend\models\Manager;
use backend\models\Organization;
use common\models\Api;
use common\models\Building;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildingHolidayStatusController implements the CRUD actions for BuildingHolidayStatus model.
 */
class BuildingHolidayStatusController extends Controller
{

    public function behaviors()
    {
        return [
        ];
    }

    /**
     * 批量增加楼宇不运维
     * @author wxl
     * @return string|\yii\web\Response
     */
    public function actionAddBuildingStop()
    {
        if (!Yii::$app->user->can('批量添加楼宇节假日不运维')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new BuildingHolidayStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $flag         = 'add';
        return $this->render('building_stop', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'flag'         => $flag,
        ]);
    }

    /**
     * 批量删除楼宇不运维
     * @author wxl
     * @return string|\yii\web\Response
     */
    public function actionRemoveBuildingStop()
    {
        if (!Yii::$app->user->can('批量删除楼宇节假日不运维')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new BuildingHolidayStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $flag         = 'delete';
        return $this->render('building_stop', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'flag'         => $flag,
        ]);
    }

    /**
     * Finds the BuildingHolidayStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BuildingHolidayStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BuildingHolidayStatus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 获取已投放的楼宇信息
     * @author wxl
     */
    public function actionGetBuilding()
    {
        $param    = Yii::$app->request->post();
        $page     = Yii::$app->request->post('page', 1);
        $pageSize = Yii::$app->request->post("pageSize", 20);
        //判断添加不运维还是删除不运维标示
        $flag  = Yii::$app->request->post("flag");
        $query = Building::find()
            ->select('building.id,name buildingName')
            ->where(['build_status' => Building::SERVED])
            ->joinWith('equip e');
        $list = BuildingHolidayStatus::getSettingStopBuildingID();
        if ($flag == 'delete') {
            $query->andWhere(['in', 'building.id', $list]);
        } else {
            $query->andWhere(['not in', 'building.id', $list]);
        }
        if (!empty($param['buildingName'])) {
            $query->andWhere(['like', 'name', $param['buildingName']]);
        }
        if (!empty($param['buildingType'])) {
            $query->andWhere(['build_type' => $param['buildingType']]);
        }
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->andWhere(['building.org_id' => $orgId]);
        } else {
            if (!empty($param['branch'])) {
                $query->andWhere(['building.org_id' => $param['branch']]);
            }
        }
        if (!empty($param['equipmentType'])) {
            $query->andWhere(['e.equip_type_id' => $param['equipmentType']]);
        }
        $offset             = ($page - 1) * $pageSize;
        $query              = $query->offset($offset)->limit($pageSize);
        $data['buildArr']   = $query->asArray()->all();
        $data['totalCount'] = $query->count();
        echo Json::encode($data);
    }

    /**
     * 修改楼宇运维数据
     * @author wxl
     * @return \yii\web\Response
     */
    public function actionModifyBuildingStatus()
    {
        $flag  = Yii::$app->request->get('flag', 'add');
        $param = Yii::$app->request->post();

        if ($param) {

            $result = $flag == 'add' ? BuildingHolidayStatus::addBuildingStop($param['buildingIdArr']) : BuildingHolidayStatus::removeBuildingStop($param['buildingIdArr']);
            if ($result) {
                $url = $flag == 'add' ? '/building-holiday-status/add-building-stop' : '/building-holiday-status/remove-building-stop';
                return $this->redirect($url);
            }
        }
    }

}
