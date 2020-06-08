<?php
namespace common\dailyTask;

use backend\models\Holiday;
use yii\helpers\ArrayHelper;
use common\models\Equipments;
use backend\models\BuildingTaskSetting;
use yii\base\InvalidConfigException;


/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/7/4
 * Time: 下午3:13
 */
class DailyTaskInit implements DailyTaskInterface
{
    //设备信息
    protected $equipments;
    //设备日常任务设置信息
    protected $equipmentDailyTaskSetting;
    //配送员
    protected $distributionList;
    //设备状态
    protected $operationStatus = [
        Equipments::COMMERCIAL_OPERATION,//商业运营
        Equipments::TEMPORARY_OPERATIONS //临时运营
    ];

    /**
     * 获取设备信息(设备ID+设备类型)
     */
    function getEquipments()
    {
        $this->equipments = Equipments::getEquipmentByStatus($this->operationStatus);
        return $this->equipments;
    }


    /**
     * 获取节假日设置
     * @return string
     */
    function getHoliday()
    {
        $holidays = Holiday::getFiled('date_day', ['is_holiday' => Holiday::IS_HOLIDAY]);
        return ArrayHelper::getColumn($holidays, 'date_day');
    }

    /**
     * 获取日常任务设置
     * @return mixed
     * @throws InvalidConfigException
     */
    function getDailyTaskSetting()
    {
        if ($this->equipments === null) {
            throw new InvalidConfigException('The "equipments" property must be set.');
        }
        foreach ($this->equipments as $equipmentId => $equipTypeId) {
            $model = Equipments::findOne($equipmentId);
            $equipmentModel = BuildingTaskSetting::getEquipmentTaskSetting($model);
            if ($equipmentModel->cleaningCycle > 0 || $equipmentModel->refuelCycle > 0 || $equipmentModel->dayNum > 0) {
                $this->equipmentDailyTaskSetting[$equipmentId]['cleaningCycle'] = $equipmentModel->cleaningCycle;
                $this->equipmentDailyTaskSetting[$equipmentId]['refuelCycle'] = $equipmentModel->refuelCycle;
                $this->equipmentDailyTaskSetting[$equipmentId]['dayNum'] = $equipmentModel->dayNum;
                $this->equipmentDailyTaskSetting[$equipmentId]['equip_type_id'] = $equipmentModel->equip_type_id;
                $this->equipmentDailyTaskSetting[$equipmentId]['equip_code'] = $equipmentModel->equip_code;
            }
        }
        return $this->equipmentDailyTaskSetting;
    }


    /**
     * 获取配送员ID
     * @return mixed
     * @throws InvalidConfigException
     */
    function getDistributionIds()
    {
        if ($this->equipmentDailyTaskSetting === null) {
            throw new InvalidConfigException('The "equipmentDailyTaskSetting" property must be set.');
        }
        foreach ($this->equipmentDailyTaskSetting as $equipmentId => $item) {
            $this->distributionList[$equipmentId]['distributionId'] = Equipments::getDistributionUserid($item['equip_code']);
        }
        return $this->distributionList;
    }

}