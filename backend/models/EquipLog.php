<?php

namespace backend\models;

use backend\models\EquipWarn;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_log".
 *
 * @property integer $id
 * @property string $content
 * @property integer $log_type
 * @property integer $equip_code
 * @property integer $equip_status
 * @property integer $create_time
 */
class EquipLog extends \yii\db\ActiveRecord
{
    /**
     * 设备状态
     * 0--正常
     * 1--不正常
     * 3--缺料
     * 4--警告
     */
    const EQUIP_STATE_NORMAL    = 1;
    const EQUIP_STATE_NO_NORMAL = 2;
    const EQUIP_STATE_LACK      = 3;
    const EQUIP_STATE_FALUT     = 4;

    //日志类型
    public static $log_type = [
        self::EQUIP_STATE_NORMAL    => '正常',
        self::EQUIP_STATE_NO_NORMAL => '不正常',
        self::EQUIP_STATE_LACK      => '缺料',
        self::EQUIP_STATE_FALUT     => '警告',
    ];
    //设备状态
    public static $equip_status = [
        self::EQUIP_STATE_NORMAL    => '正常',
        self::EQUIP_STATE_NO_NORMAL => '不正常',
        self::EQUIP_STATE_LACK      => '缺料',
        self::EQUIP_STATE_FALUT     => '警告',
    ];
    public static $errorCode = [
        '0000',
        '00000',
        '000000',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_type', 'equip_status', 'create_time'], 'integer'],
            [['content'], 'string', 'max' => 1000],
            [['equip_code', 'error_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'content'      => '日志内容',
            'log_type'     => '日志类型',
            'equip_code'   => '设备编号',
            'equip_status' => '设备状态',
            'create_time'  => '上传时间',
            'error_code'   => '错误编码',
        ];
    }

    /**
     * 添加数据
     * @author  zgw
     * @version 2016-08-26
     * @param   array     $data 要添加的数据
     */
    public static function addLog($equipDetail, $data)
    {
        if ($data['content']) {
            $isLackMaterial = 1;
            $status         = $data['equip_status'];
            $isStatus       = 0;
            foreach ($data['content'] as $errorCode => $abnormal) {
                if (!in_array($errorCode, EquipWarn::$lackMaterialErrorCode)) {
                    $isLackMaterial = 0;
                    $status         = $data['equip_status'];
                    $isStatus       = 0;
                    if (!in_array($errorCode, self::$errorCode) && $data['equip_status'] == '1') {
                        $status   = Equipments::WORK_STATUS_WARNING;
                        $isStatus = 1;
                    }
                } else {
                    $isStatus = 0;
                    $status   = 3;
                }
                $data['last_log'] = $abnormal;
                //硬币和纸币故障默认为正常
                if ($errorCode == '01080100' || $errorCode == '01080200' || $errorCode == '01080300') {
                    $abnormal             = '设备正常';
                    $errorCode            = 0;
                    $status               = 1;
                    $isStatus             = 0;
                    $data['last_log']     = $abnormal;
                    $data['equip_status'] = Equipments::NORMAL;
                }
                $logData['content']      = $abnormal;
                $logData['log_type']     = $status;
                $logData['equip_code']   = $data['equip_code'];
                $logData['equip_status'] = $status;
                $logData['error_code']   = (string) ($errorCode == 0 ? '000000' : $errorCode);
                $logData['create_time']  = time();
                $model                   = new EquipLog();
                $model->load(['EquipLog' => $logData]);
                $model->save();
            }
            if ($isLackMaterial && $data['equip_status'] == 2) {
                $data['equip_status'] = 3;
            }
            if ($isStatus && $data['equip_status'] == 1) {
                $data['equip_status'] = Equipments::WORK_STATUS_WARNING;
            }
            if (!isset($data['content']['01090400'])) {
                Equipments::updateEquipments($equipDetail, $data);
            }

        }

        return true;
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-09-13
     * @param   [type]     $field [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getField($field, $where)
    {
        $logInfo = self::find()->select($field)->where($where)->orderBy('create_time desc')->one();
        return $logInfo ? $logInfo->$field : '';
    }
}
