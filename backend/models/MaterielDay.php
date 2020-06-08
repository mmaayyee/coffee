<?php

namespace backend\models;

use backend\models\DistributionUser;
use backend\models\Organization;
use common\helpers\Tools;
use common\models\Api;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "materiel_day".
 *
 * @property integer $materiel_id
 * @property string $equipment_code
 * @property integer $build_id
 * @property integer $material_type_id
 * @property integer $create_at
 * @property double $consume_total
 */
class MaterielDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $build_id;
    public $material_type_id;
    public $create_at;
    public $consume_total;
    public $equipment_code;
    public $material_type_name;
    public $consume_total_all;
    public $online;
    public $orgId;
    public $build_name;
    public $build_type;
    public $equip_type_id;
    public $startTime;
    public $endTime;
    public $userId;
    public $countByMaterialType;
    public $payment_state;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'material_type_id', 'create_at'], 'integer'],
            [['consume_total'], 'number'],
            [['consume_total_all', 'material_type_name', 'orgId', 'online', 'build_type', 'build_name', 'equip_type_id', 'startTime', 'endTime', 'userId'], 'safe'],
            [['equipment_code'], 'string', 'max' => 32],
            [['payment_state'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'materiel_id'      => 'Materiel ID',
            'equipment_code'   => 'Equipment Code',
            'build_id'         => 'Build ID',
            'material_type_id' => '物料分类',
            'create_at'        => 'Create At',
            'consume_total'    => 'Consume Total',
            'orgId'            => '地区',
            'online'           => '运营状态',
            'build_name'       => '楼宇名称',
            'build_type'       => '渠道类型',
            'equip_type_id'    => '设备类型',
            'startTime'        => '开始时间',
            'endTime'          => '结束时间',
            'userId'           => '运维人员',
            'payment_state'    => '付费状态',
        ];
    }

    public static $paymentState = [
        '' => '请选择',
        1  => '已支付',
        2  => '未支付',
    ];
    /**
     * 获取运营状态
     * @author  tuqiang
     * @version 2017-11-20
     * @param   string      $equipmentCode 设备编号
     * @return  string      运营状态
     */
    public static function getOperateName($id)
    {
        return isset(Equipments::$operationStatusArray[$id]) ? Equipments::$operationStatusArray[$id] : '';
    }
    /**
     * 获取机构地区
     * @author  tuqiang
     * @version 2017-11-20
     * @param   string      $id     机构id
     * @return  string      机构名称
     */
    public static function getOrgName($id)
    {
        return Organization::getField('org_name', ['org_id' => $id]);
    }

    /**
     * 物料导出excel
     * @author  tuqiang
     * @version 2017-11-22
     * @param   array      $param    查询条件
     * @return  array      符合条件的物料数据
     */
    public static function getMaintainExcelMaterielDay($param)
    {
        return Api::getMaintainExcelMaterielDay($param);
    }

    /**
     * 楼宇导出excel
     * @author  tuqiang
     * @version 2017-11-22
     * @param   array      $param    查询条件
     * @return  array      符合条件的物料数据
     */
    public static function getBuildExcelMaterielDay($param)
    {
        return Api::getBuildExcelMaterielDay($param);
    }

    /**
     * 按天查询物料消耗数据
     * @param   string $createAt 时间
     * @return  array            以时间维度查询的楼宇物料消耗
     */
    public static function getMaterielDayInfoByDate($params)
    {
        return Api::getMaterielDayInfoByDate($params);
    }

    /**
     * 获取符合条件的数据接口
     * @author    tuqiang
     * @version   2017-11-23
     * @return    array      用户id  => 名称
     */
    public static function getDistributionUserName($orgId)
    {
        if ($orgId == 1 || $orgId == '') {
            $where = ' 1 = 1 ';
        } else {
            $where = ['wx_member.org_id' => $orgId];
        }
        $distributionUserList = DistributionUser::find()->leftJoin("wx_member", 'wx_member.userid = distribution_user.userid')->where($where)->select('wx_member.name,distribution_user.userid')->asArray()->all();
        return Tools::map($distributionUserList, 'userid', 'name', null, 1);
    }
}
