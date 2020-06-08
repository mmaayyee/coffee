<?php
namespace frontend\controllers;

use Yii;
use common\models\Building;
use common\models\Equipments;
use common\models\Api;

/**
 * 取得配送楼宇列表
 */
class MaterielConsumController extends BaseController
{

    /**
     * 获取符合条件的楼宇列表
     * @author  tuqiang
     * @version 2017-11-28
     * @return  array       building
     */
    public function actionIndex()
    {   
        $userId = $this->userinfo['userid'];
        $buildingList = Building::getBuildingListByUserId($userId);
        return $this->render('index', [
            'buildingList' => $buildingList
        ]);
    }

    public function actionGetBuildingInfo(){
        $buildId = Yii::$app->request->get('id');
        $equipInfo = Equipments::findOne(['build_id' => $buildId]);
        if($equipInfo && !empty($equipInfo->equip_code)){
            return Api::getMaterielInfo($equipInfo->equip_code);
        }
    }
}
