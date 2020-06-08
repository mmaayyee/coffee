<?php
namespace console\controllers;

use backend\models\DistributionDailyTask;
use backend\models\EquipConsumeMaterial;
use backend\models\EquipMaterialStockAssoc;
use backend\models\EquipSurplusMaterial;
use backend\models\EquipWarn;
use backend\models\MaterialSafeValue;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\dailyTask\Tasks;
use common\dailyTask\DailyTaskInit;
use Yii;

//日常任务计划任务
class DailyController extends \yii\console\Controller
{
    /**
     * 设备和料仓关联表(1点)
     * @return [type] [description]
     */
    public function actionStockLimit()
    {
        $data = Api::stockLimit();
        $data = json_decode($data, true);
        if (EquipMaterialStockAssoc::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 设备消耗物料接口(需要走定时任务，每天凌晨3点)
     * @return [type] [description]
     */
    public function actionConsumeMaterial()
    {
        $data = Api::consume();
        //$data = '{"1702000000":{"1":10,"2":19,"5":13,"15":0,"11":2,"8":9.4}}';
        $data = json_decode($data, true);
        if (EquipConsumeMaterial::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 设备剩余物料接口（需要走定时任务，每天18点）
     * @return [type] [description]
     */
    public function actionSurplusMaterial()
    {
        //获取所有的设备编号
        /*$equipCodeArr = EquipConsumeMaterial::getColumn();
        $equipCodes   = implode(',', $equipCodeArr);
        $data         = Api::surplus($equipCodes);
        $data         = json_decode($data, true);
        if (EquipSurplusMaterial::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }*/
    }

    /**
     * 日常任务数据生成（18点10分）
     * @author  zgw
     * @version 2016-08-12
     * @return  [type]     [description]
     */
    /*public function actionCreateDailyTask()
    {
        if (DistributionDailyTask::addAll()) {
            echo 1;
        } else {
            echo 0;
        }
    }*/

    /**
     * 首杯免费策略更新接口
     * @author  zgw
     * @version 2016-12-09
     * @return  [type]     [description]
     */
    public function actionUpdateFirst()
    {
        echo date("Y-m-d H:i:s") . "begin update Strategy:\n";
        Building::updateFirst();
        echo date("Y-m-d H:i:s") . "end  update Strategy:\n";
    }

    /**
     * 检查设备状态(超过20分钟没有上传日志)
     * @author  zgw
     * @version 2016-12-09
     * @return  [type]     [description]
     */
    public function actionCheckEquipment()
    {
        echo date("Y-m-d H:i:s") . "begin check Equipment:\n";
        Equipments::check();
        echo date("Y-m-d H:i:s") . "end check Equipment:\n";
    }

    /**
     * 每天早上八点检测是否存在待分配任务存在发送通知
     */
    public function actionWaitForTaskMessage(){
        echo date("Y-m-d H:i:s") . "begin check EquipTask:\n";
        EquipTask::checkWaitForTask();
        echo date("Y-m-d H:i:s") . "end check EquipTask:\n";
    }

    /**
     * 生成日常任务
     * @author wangxl
     */
    public function actionCreateDailyTask(){
        echo date("Y-m-d H:i:s") . "begin generate daily task:\n";
        $model = new Tasks();
        $model->distributionTask();
        echo date("Y-m-d H:i:s")."end generate daily task:\n";
    }

}
