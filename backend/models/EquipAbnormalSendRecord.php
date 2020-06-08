<?php

namespace backend\models;

use common\models\Building;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_abnormal_send_record".
 *
 * @property integer $id
 * @property integer $abnormal_id
 * @property integer $equip_code
 * @property string $send_users
 * @property integer $is_process_success
 * @property integer $send_time
 * @property integer $process_time
 */
class EquipAbnormalSendRecord extends \yii\db\ActiveRecord
{
    const PROCESS_SUCCESS = 1;
    const PROCESS_FAIL    = 2;

    public static $processResult = [
        '' => '请选择',
        1  => '已处理',
        2  => '未处理',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_abnormal_send_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['abnormal_id', 'report_num', 'is_process_success', 'send_time', 'process_time', 'org_id', 'build_id'], 'integer'],
            [['send_users'], 'string', 'max' => 300],
            [['equip_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'abnormal_id'        => '故障内容',
            'equip_code'         => '设备编号',
            'send_users'         => '发送用户',
            'report_num'         => '上报的等级',
            'is_process_success' => '是否处理成功',
            'send_time'          => '发送时间',
            'process_time'       => '故障处理时间',
            'build_id'           => '楼宇',
        ];
    }

    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * 查看发送记录详情
     * @param  [type] $field [description]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public static function getSendRecordDetail($field = '*', $where = array())
    {
        return self::find()->select($field)->where($where)->asArray()->orderby('send_time desc')->one();
    }

    /**
     * 获取发送记录
     * @author  zgw
     * @version 2016-08-27
     * @return  array     发送记录
     */
    public static function getSendList()
    {
        $where = [];
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $where = ['equipments.org_id' => $orgId];
        }
        return self::find()->joinWith('equip')->where($where)->all();
    }
}
