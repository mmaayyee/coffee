<?php

namespace common\models;

use common\models\Equipments;
use yii\helpers\ArrayHelper;

class EquipmentsChild
{
    public static function getPutInEquipList()
    {
        return Equipments::find()
            ->select('build_id,equip_code,equip_operation_time')
            ->where(['>', 'build_id', 0])
            ->asArray()
            ->all();
    }
    /**
     * 获取点位ID对应的设备编号
     * @author zhenggangwei
     * @date   2020-01-09
     * @return array
     */
    public static function getBuildIdEquipCodeList($equipList = [])
    {
        if (!$equipList) {
            $equipList = self::getPutInEquipList();
        }
        return ArrayHelper::map($equipList, 'build_id', 'equip_code');
    }

    /**
     * 获取点位ID对应的开始运营时间
     * @author zhenggangwei
     * @date   2020-01-09
     * @param  array     $equipList 设备列表
     * @return array
     */
    public static function getBidOperaTimeList($equipList = [])
    {
        if (!$equipList) {
            $equipList = self::getPutInEquipList();
        }
        $bidOperaTimeList = [];
        foreach ($equipList as $equip) {
            $bidOperaTimeList[$equip['build_id']] = !empty($equip['equip_operation_time']) ? date('Y-m-d H:i:s', $equip['equip_operation_time']) : '';
        }
        return $bidOperaTimeList;
    }

}
