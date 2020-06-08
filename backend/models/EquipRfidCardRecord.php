<?php

namespace backend\models;

use common\models\Building;
use common\models\Equipments;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "equip_rfid_card_record".
 *
 * @property string $id
 * @property string $equip_code
 * @property string $rfid_card_code
 * @property string $open_people
 * @property string $create_time
 */
class EquipRfidCardRecord extends \yii\db\ActiveRecord
{
    public $startTime; // 开始时间
    public $endTime; // 结束时间
    public $orgId;
    public $orgType;

    // 开门类型 1 特殊开门 2-刷卡开门
    // 1 特殊开门
    const CARDOPNEDOOR = 1;
    // 2-刷卡开门
    const SPECIALDOOR = 2;
    // 3-蓝牙正常开门
    const BLUETOOTHNORMALDOOR = 3;
    // 4-蓝牙临时开门
    const BLUETOOTHTEMPORARYDOOR = 4;

    //是否成功开门
    // 1，开门失败
    const OPENERROR = 1;
    // 2，开门成功
    const OPENSUCCESS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_rfid_card_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rfid_card_code', 'build_id', 'create_time', 'open_type', 'is_open_success'], 'integer'],
            [['equip_code', 'open_people'], 'string', 'max' => 50],
            [['orgId', 'orgType'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'build_id'        => '楼宇名称',
            'equip_code'      => '设备编号',
            'rfid_card_code'  => '门禁卡号',
            'open_people'     => '开门人员',
            'create_time'     => '开门时间',
            'startTime'       => '开始查询时间',
            'endTime'         => '结束查询时间',
            'open_type'       => '开门类型',
            'is_open_success' => '开门结果',
            'orgType'         => '机构类型',
        ];
    }

    // 开门结果
    public static $isOpenSuccess = [
        ''                => '请选择',
        self::OPENSUCCESS => '成功',
        self::OPENERROR   => '失败',
    ];

    // 开门类型
    public static $openType = [
        ''                           => '请选择',
        self::CARDOPNEDOOR           => '特殊开门',
        self::SPECIALDOOR            => '刷卡开门',
        self::BLUETOOTHTEMPORARYDOOR => '蓝牙临时开门',
        self::BLUETOOTHNORMALDOOR    => '蓝牙正常开门',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'open_people']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * 添加门禁卡开门记录
     * @author  zmy
     * @version 2016-12-12
     * @param   [type]     $rfidCardObj   [门禁卡对象]
     * @param   [type]     $equipmentCode [设备编号]
     * @param   integer    $openType      [开门类型 1-特殊开门 2-刷卡开门]
     * @param   integer    $isOpenSuccess [1-开门失败 2-开门成功]
     * @return  [type]                    [true/fasle]
     */
    public static function saveRfidData($rfidCardObj, $equipmentCode, $openType = 0, $isOpenSuccess = 0)
    {
        $equipObj = Equipments::getEquipBuildDetail("*", ['equip_code' => $equipmentCode]);
//        if(!$equipObj){
        //            return false;
        //        }
        $model                  = new EquipRfidCardRecord();
        $model->equip_code      = $equipmentCode;
        $model->build_id        = isset($equipObj->build_id) ? $equipObj->build_id : 0;
        $model->rfid_card_code  = isset($rfidCardObj->rfid_card_code) ? $rfidCardObj->rfid_card_code : '';
        $model->create_time     = time();
        $model->open_people     = isset($rfidCardObj->member_id) ? $rfidCardObj->member_id : $rfidCardObj->userid;
        $model->open_type       = $openType;
        $model->is_open_success = $isOpenSuccess;

        return $model->save();
    }

    /**
     * 门禁卡开门记录
     * @author  zmy
     * @version 2016-12-12
     * @param   [type]     $rfidCardObj   [门禁卡对象]
     * @param   [type]     $equipmentCode [设备编号]
     * @return  [type]                    []
     */
    public static function retCreateRfidRecord($rfidCardObj, $equipmentCode)
    {
        // 写入record表中
        $retRecord = EquipRfidCardRecord::saveRfidData($rfidCardObj, $equipmentCode, self::SPECIALDOOR, self::OPENSUCCESS);
        if (!$retRecord) {
            $rfidCardObj->code = "1010";
            $rfidCardObj->msg  = "开门记录失败。";
            $rfidCardObj->open = false;
        }
        return $rfidCardObj;
    }
}
