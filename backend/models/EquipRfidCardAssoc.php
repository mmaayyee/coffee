<?php

namespace backend\models;

use backend\models\EquipRfidCard;
use common\models\Building;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_rfid_card_assoc".
 *
 * @property string $id
 * @property string $equip_code
 * @property string $rfid_card_id
 */
class EquipRfidCardAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_rfid_card_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code', 'rfid_card_code'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'equip_code'     => 'Equip Code',
            'rfid_card_code' => 'Rfid Card ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    public static function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);

    }

    /**
     * RFID_Assoc表中的设备编号数组
     * @param   rfidCardObj 关联表对象
     * @author  zmy
     * @version 2016-12-12
     * @param   [type]     $rfidCardObj     [门禁卡obj]
     * @param   [type]     $equipmentCode   [设备编号]
     * @return  [type]                      [true/false]
     */
    public static function getAssocEquipArr($rfidCardObj, $equipmentCode)
    {
        $rfidCardAssocArr = self::find()->where(['rfid_card_code' => $rfidCardObj->rfid_card_code, 'equip_code' => $equipmentCode])->asArray()->all();
        return $rfidCardAssocArr;
    }

    /**
     * 获取是否指定离线开门人员
     * @author  zmy
     * @version 2017-07-31
     * @param   [type]     $rfidCardObj   [门禁卡obj]
     * @param   [type]     $equipmentCode [设备编号]
     * @return  [type]                    [obj]
     */
    public static function getIsOwnerEquipOpen($rfidCardObj, $equipmentCode)
    {
        // 判断是否为指定离线开门人员
        if ($rfidCardObj->area_type != EquipRfidCard::COUNTRY_ALL && $rfidCardObj->area_type != EquipRfidCard::BRANCH_ALL) {
            if (EquipRFidCardAssoc::getIsDesignatedPerson($rfidCardObj, $equipmentCode)) {
                $rfidCardObj->owner = true;
            }
        }
        return $rfidCardObj;
    }

    /**
     * 判断门禁卡权限，并返回rfidCardObj
     * @param 设备编号
     * @param rfid表中对象
     * @author  zmy
     * @version 2016-12-12
     * @return  [type]     [description] permissions
     */
    public static function judgePermissions($equipmentCode, $rfidCardObj)
    {
        if ($rfidCardObj->area_type == EquipRfidCard::COUNTRY_ALL) {
            $rfidCardObj->code = "00";
            $rfidCardObj->msg  = "";

        } else if ($rfidCardObj->area_type == EquipRfidCard::BRANCH_ALL) {
            //if是分公司所有设备 查询该设备编号分公司 与 org_id是否一样
            $equipObj = Equipments::getEquipmentsDetail('*', ['equip_code' => $equipmentCode]);
            $orgIdStr = trim($rfidCardObj['org_id'], ',');
            $orgIdArr = explode(',', $orgIdStr);

            if (!$equipObj || !in_array($equipObj['org_id'], $orgIdArr)) {
                $rfidCardObj->code = "1009";
                $rfidCardObj->msg  = "设备编号不符合条件（不在分公司下）。";
                $rfidCardObj->open = false;
            }
        } else {
            $assocArr = self::getAssocEquipArr($rfidCardObj, $equipmentCode);
            if (!$assocArr) {
                $rfidCardObj->code = "1008";
                $rfidCardObj->msg  = "设备编号不在关联表中。";
                $rfidCardObj->open = false;
            }
        }
        return $rfidCardObj;

    }

    /**
     * 获取RFID  Assoc关联表中设备编号的数据数组
     * [getRfidAssocArr description]
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $rfidCardCode [description]
     * @return  [type]                   [description]
     */
    public static function getRfidAssocArr($rfidCardCode)
    {
        $rfidCardAssocArr = EquipRfidCardAssoc::find()
            ->where(['rfid_card_code' => $rfidCardCode])
            ->asArray()
            ->all();
        $rfidAssocArr = [];
        foreach ($rfidCardAssocArr as $rfidCardAssoc) {
            $rfidAssocArr[] = $rfidCardAssoc['equip_code'];
        }
        return $rfidAssocArr;
    }

    /**
     * 获取RFID  Assoc关联表中离线开门设备的数据
     * [getRfidAssocArr description]
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $rfidCardCode [description]
     * @return  [type]                   [description]
     */
    public static function getRfidAssocEquipCodeOff($rfidCardCode)
    {
        $rfidCardAssocArr = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $rfidCardCode, 'is_designated_person' => 1])->asArray()->one();
        if (!$rfidCardAssocArr) {
            return '';
        }
        return $rfidCardAssocArr['equip_code'];
    }

    /**
     * 通过卡号，组合设备编号=》楼宇名称 数组
     * @author  zmy
     * @version 2016-12-27
     * @return  [type]     [description]
     */
    public static function getEquipCodeArrByCode($rfidCardCode)
    {
        $rfidCodeObj  = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $rfidCardCode])->all();
        $buildCodeArr = [];
        foreach ($rfidCodeObj as $val) {
            $buildCodeArr[$val->equip_code] = isset($val->equip->build->name) ? $val->equip->build->name : '';
        }
        return $buildCodeArr;
    }

    /**
     * 通过卡号，组合设备编号=》楼宇名称 数组（离线开门设备）
     * @author  zmy
     * @version 2016-12-27
     * @return  [type]     [description]
     */
    public static function getEquipCodeArrByCodeOff($rfidCardCode)
    {
        $rfidCodeObj  = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $rfidCardCode, 'is_designated_person' => 1])->all();
        $buildCodeArr = [];
        foreach ($rfidCodeObj as $val) {
            $buildCodeArr[$val->equip_code] = isset($val->equip->build->name) ? $val->equip->build->name : '';
        }
        return $buildCodeArr;
    }

    /**
     * 添加 离线开门设备到 关联表中
     * @author  zmy
     * @version 2016-12-27
     * @return  [type]     [description]
     */
    public static function saveEquipRfidAssoc($rfidCardCode, $offEquipCode)
    {
        $model                       = new EquipRfidCardAssoc();
        $model->is_designated_person = 1;
        $model->rfid_card_code       = $rfidCardCode;
        $model->equip_code           = $offEquipCode;
        return $model->save();
    }

    /**
     * 修改assoc表中符合条件的设备是否离线开门标识
     * @author  zmy
     * @version 2016-12-28
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function updateAccordEquipCode($param)
    {
        $offEquipCodeArr    = [];
        $retUpdateRfidAssoc = '';
        if ($param['offEquipCode']) {
            foreach ($param['offEquipCode'] as $key => $value) {
                $offEquipCodeArr[] = $value;
            }
            $retUpdateRfidAssoc = EquipRfidCardAssoc::updateAll(['is_designated_person' => 0],
                ['is_designated_person' => 1, 'equip_code' => $offEquipCodeArr]);
        }

        return $retUpdateRfidAssoc;
    }

    /**
     * 组合需要的数组插入到门禁卡关联表中
     * @author  zmy
     * @version 2016-12-28
     * @return  [type]     Arr
     */
    public static function getCombinationArr($param, $rfidCardCode)
    {
        // 循环成数组 相同
        $equipCodeArr = [];
        if ($param['ownedEquipCode'] && $param['offEquipCode']) {
            $equipCodeArr = self::getCombinationWhereDaoistMonkArr($param, $rfidCardCode);
        } else if ($param['ownedEquipCode'] && !$param['offEquipCode']) {
            foreach ($param['ownedEquipCode'] as $key => $value) {
                $equipCodeArr[$key]['rfidCardCode']       = $rfidCardCode;
                $equipCodeArr[$key]['isDesignatedPerson'] = 0;
                $equipCodeArr[$key]['equipCode']          = $value;
            }
        } else if (!$param['ownedEquipCode'] && $param['offEquipCode']) {
            foreach ($param['offEquipCode'] as $key => $value) {
                $equipCodeArr[$key]['rfidCardCode']       = $rfidCardCode;
                $equipCodeArr[$key]['isDesignatedPerson'] = 1;
                $equipCodeArr[$key]['equipCode']          = $value;
            }
        }
        return $equipCodeArr;
    }

    /**
     * 传输的全部为真
     * @author  zmy
     * @version 2016-12-29
     * @param   [type]     $param        [description]
     * @param   [type]     $rfidCardCode [description]
     * @return  [type]                   [description] Daoist monk
     */
    public static function getCombinationWhereDaoistMonkArr($param, $rfidCardCode)
    {
        $equipCodeIdenticalArr = [];
        // 不同
        $equipCodeDifferenceArr = [];
        foreach ($param['ownedEquipCode'] as $key => $ownedVal) {
            if (!in_array($ownedVal, $param['offEquipCode'])) {
                $equipCodeDifferenceArr[$key]['rfidCardCode']       = $rfidCardCode;
                $equipCodeDifferenceArr[$key]['isDesignatedPerson'] = 0;
                $equipCodeDifferenceArr[$key]['equipCode']          = $ownedVal;
            }
        }
        foreach ($param['offEquipCode'] as $key => $offVal) {
            $equipCodeIdenticalArr[$key]['rfidCardCode']       = $rfidCardCode;
            $equipCodeIdenticalArr[$key]['isDesignatedPerson'] = 1;
            $equipCodeIdenticalArr[$key]['equipCode']          = $offVal;
        }
        $equipCodeArr = array_merge($equipCodeDifferenceArr, $equipCodeIdenticalArr);
        return $equipCodeArr;
    }

    /**
     * 判断是否为指定离线开门设备
     * @author  zmy
     * @version 2017-01-03
     * @param   [type]     $rfidCardObj    [rfidCard对象]
     * @param   [type]     $equipmentCode  [设备编号]
     * @return  [type]                     [true/false]
     */
    public static function getIsDesignatedPerson($rfidCardObj, $equipmentCode)
    {
        return self::find()->where(['rfid_card_code' => $rfidCardObj->rfid_card_code, "equip_code" => $equipmentCode])->one();
    }

    /**
     * 解绑删除门禁卡管理中的设备
     * @author  zmy
     * @version 2017-01-05
     * @param   [type]     $equipCode 设备编号
     * @return  [type]     true/false
     */
    public static function unBindDeleteRfidAssoc($equipCode)
    {
        return EquipRFidCardAssoc::deleteAll(['equip_code' => $equipCode]);
    }

}
