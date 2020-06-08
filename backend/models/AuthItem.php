<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class AuthItem extends \yii\db\ActiveRecord
{
    public $role;

    /**
     * 超级管理员
     */
    const SUPER_MASTER = "超级管理员";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            ['name', 'unique'],
            [['name', 'rule_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'        => '角色名称',
            'type'        => 'Type',
            'description' => '角色简介',
            'rule_name'   => 'Rule Name',
            'data'        => 'Data',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**Auth
     * 获取权限数组
     */
    public function getRightsArray()
    {
        return array(
            '1'      => array('id' => 1, 'pId' => 0, 'name' => '首页管理', 'open' => true, 'defaultCheck' => false),

            '2'      => array('id' => 2, 'pId' => 0, 'name' => '供应链基础信息', 'open' => true, 'defaultCheck' => false),

            '201'    => array('id' => 201, 'pId' => 2, 'name' => '供应商管理', 'defaultCheck' => false),
            '20101'  => array('id' => 20101, 'pId' => 201, 'name' => '编辑供应商', 'defaultCheck' => false),
            '20102'  => array('id' => 20102, 'pId' => 201, 'name' => '删除供应商', 'defaultCheck' => false),
            '20103'  => array('id' => 20103, 'pId' => 201, 'name' => '查看供应商', 'defaultCheck' => false),
            '20104'  => array('id' => 20104, 'pId' => 201, 'name' => '创建供应商', 'defaultCheck' => false),

            '208'    => array('id' => 208, 'pId' => 2, 'name' => '物料分类管理', 'defaultCheck' => false),
            '20801'  => array('id' => 20801, 'pId' => 208, 'name' => '编辑物料分类', 'defaultCheck' => false),
            '20802'  => array('id' => 20802, 'pId' => 208, 'name' => '删除物料分类', 'defaultCheck' => false),
            '20803'  => array('id' => 20803, 'pId' => 208, 'name' => '查看物料分类', 'defaultCheck' => false),
            '20804'  => array('id' => 20804, 'pId' => 208, 'name' => '添加物料分类', 'defaultCheck' => false),

            '202'    => array('id' => 202, 'pId' => 2, 'name' => '物料信息管理', 'defaultCheck' => false),
            '20201'  => array('id' => 20201, 'pId' => 202, 'name' => '编辑物料', 'defaultCheck' => false),
            '20202'  => array('id' => 20202, 'pId' => 202, 'name' => '删除物料', 'defaultCheck' => false),
            '20203'  => array('id' => 20203, 'pId' => 202, 'name' => '查看物料', 'defaultCheck' => false),
            '20204'  => array('id' => 20204, 'pId' => 202, 'name' => '添加物料', 'defaultCheck' => false),

            '205'    => array('id' => 205, 'pId' => 2, 'name' => '库信息管理', 'defaultCheck' => false),
            '20501'  => array('id' => 20501, 'pId' => 205, 'name' => '编辑库信息', 'defaultCheck' => false),
            '20502'  => array('id' => 20502, 'pId' => 205, 'name' => '删除库信息', 'defaultCheck' => false),
            '20503'  => array('id' => 20503, 'pId' => 205, 'name' => '查看库信息', 'defaultCheck' => false),
            '20504'  => array('id' => 20504, 'pId' => 205, 'name' => '添加库信息', 'defaultCheck' => false),

            '3'      => array('id' => 3, 'pId' => 0, 'name' => '设备管理', 'open' => true, 'defaultCheck' => false),

            '301'    => array('id' => 301, 'pId' => 3, 'name' => '故障原因管理', 'defaultCheck' => false),
            '30101'  => array('id' => 30101, 'pId' => 301, 'name' => '编辑故障原因', 'defaultCheck' => false),
            '30102'  => array('id' => 30102, 'pId' => 301, 'name' => '删除故障原因', 'defaultCheck' => false),
            '30103'  => array('id' => 30103, 'pId' => 301, 'name' => '查看故障原因', 'defaultCheck' => false),
            '30104'  => array('id' => 30104, 'pId' => 301, 'name' => '添加故障原因', 'defaultCheck' => false),

            '302'    => array('id' => 302, 'pId' => 3, 'name' => '设备信息管理', 'defaultCheck' => false),

            '30201'  => array('id' => 30201, 'pId' => 302, 'name' => '编辑设备信息', 'defaultCheck' => false),
            '30202'  => array('id' => 30202, 'pId' => 302, 'name' => '删除设备信息', 'defaultCheck' => false),
            '30203'  => array('id' => 30203, 'pId' => 302, 'name' => '查看设备信息', 'defaultCheck' => false),
            '30204'  => array('id' => 30204, 'pId' => 302, 'name' => '添加设备信息', 'defaultCheck' => false),
            '30205'  => array('id' => 30205, 'pId' => 302, 'name' => '设备数据统计', 'defaultCheck' => false),
            '30206'  => array('id' => 30206, 'pId' => 302, 'name' => '锁定设备', 'defaultCheck' => false),
            '30207'  => array('id' => 30207, 'pId' => 302, 'name' => '置顶设备', 'defaultCheck' => false),
            '30208'  => array('id' => 30208, 'pId' => 302, 'name' => '发起灯箱报修', 'defaultCheck' => false),
            '30209'  => array('id' => 30209, 'pId' => 302, 'name' => '解绑', 'defaultCheck' => false),
            '302010' => array('id' => 302010, 'pId' => 302, 'name' => '修改运营状态', 'defaultCheck' => false),
            '302011' => array('id' => 302011, 'pId' => 302, 'name' => '选择灯箱', 'defaultCheck' => false),
            '302012' => array('id' => 302012, 'pId' => 302, 'name' => '报废', 'defaultCheck' => false),
            '302013' => array('id' => 302013, 'pId' => 302, 'name' => '配送任务记录', 'defaultCheck' => false),
            '302014' => array('id' => 302014, 'pId' => 302, 'name' => '日志记录', 'defaultCheck' => false),
            '302015' => array('id' => 302015, 'pId' => 302, 'name' => '设备报修记录', 'defaultCheck' => false),
            '302016' => array('id' => 302016, 'pId' => 302, 'name' => '设备维修记录', 'defaultCheck' => false),
            '302017' => array('id' => 302017, 'pId' => 302, 'name' => '验收记录', 'defaultCheck' => false),
            '302018' => array('id' => 302018, 'pId' => 302, 'name' => '配送维修记录', 'defaultCheck' => false),
            '302019' => array('id' => 302019, 'pId' => 302, 'name' => '灯箱验收记录', 'defaultCheck' => false),
            '302020' => array('id' => 302020, 'pId' => 302, 'name' => '灯箱报修记录', 'defaultCheck' => false),
            '302021' => array('id' => 302021, 'pId' => 302, 'name' => '绑定', 'defaultCheck' => false),
            '302022' => array('id' => 302022, 'pId' => 302, 'name' => '设备附件记录', 'defaultCheck' => false),
            '302023' => array('id' => 302023, 'pId' => 302, 'name' => '附件任务记录', 'defaultCheck' => false),
            '302024' => array('id' => 302024, 'pId' => 302, 'name' => '导出设备信息Excel', 'defaultCheck' => false),
            '302025' => array('id' => 302025, 'pId' => 302, 'name' => '设备参数配置', 'defaultCheck' => false),
            '302026' => array('id' => 302026, 'pId' => 302, 'name' => '同步设备参数数据', 'defaultCheck' => false),
            '302027' => array('id' => 302027, 'pId' => 302, 'name' => '配方调整', 'defaultCheck' => false),
            '302028' => array('id' => 302028, 'pId' => 302, 'name' => '远程开门', 'defaultCheck' => false),
            '302029' => array('id' => 302029, 'pId' => 302, 'name' => '远程调试', 'defaultCheck' => false),
            '302030' => array('id' => 302030, 'pId' => 302, 'name' => '查看饼状图', 'defaultCheck' => false),

            '303'    => array('id' => 303, 'pId' => 3, 'name' => '设备调试项管理', 'defaultCheck' => false),

            '30301'  => array('id' => 30301, 'pId' => 303, 'name' => '编辑设备调试项', 'defaultCheck' => false),
            '30302'  => array('id' => 30302, 'pId' => 303, 'name' => '删除设备调试项', 'defaultCheck' => false),
            '30303'  => array('id' => 30303, 'pId' => 303, 'name' => '查看设备调试项', 'defaultCheck' => false),
            '30304'  => array('id' => 30304, 'pId' => 303, 'name' => '添加设备调试项', 'defaultCheck' => false),

            '304'    => array('id' => 304, 'pId' => 3, 'name' => '灯箱管理', 'defaultCheck' => false),

            '30401'  => array('id' => 30401, 'pId' => 304, 'name' => '编辑灯箱', 'defaultCheck' => false),
            '30402'  => array('id' => 30402, 'pId' => 304, 'name' => '删除灯箱', 'defaultCheck' => false),
            '30403'  => array('id' => 30403, 'pId' => 304, 'name' => '查看灯箱', 'defaultCheck' => false),
            '30404'  => array('id' => 30404, 'pId' => 304, 'name' => '添加灯箱', 'defaultCheck' => false),

            '3015'   => array('id' => 3015, 'pId' => 3, 'name' => '灯箱调试项管理', 'defaultCheck' => false),

            '301501' => array('id' => 301501, 'pId' => 3015, 'name' => '编辑灯箱调试项', 'defaultCheck' => false),
            '301502' => array('id' => 301502, 'pId' => 3015, 'name' => '删除灯箱调试项', 'defaultCheck' => false),
            '301503' => array('id' => 301503, 'pId' => 3015, 'name' => '查看灯箱调试项', 'defaultCheck' => false),
            '301504' => array('id' => 301504, 'pId' => 3015, 'name' => '添加灯箱调试项', 'defaultCheck' => false),

            '305'    => array('id' => 305, 'pId' => 3, 'name' => '设备任务管理', 'defaultCheck' => false),

            '30501'  => array('id' => 30501, 'pId' => 305, 'name' => '编辑设备任务', 'defaultCheck' => false),
            '30502'  => array('id' => 30502, 'pId' => 305, 'name' => '删除设备任务', 'defaultCheck' => false),
            '30503'  => array('id' => 30503, 'pId' => 305, 'name' => '查看设备任务', 'defaultCheck' => false),
            '30504'  => array('id' => 30504, 'pId' => 305, 'name' => '添加设备任务', 'defaultCheck' => false),
            '30505'  => array('id' => 30505, 'pId' => 305, 'name' => '设备任务列表', 'defaultCheck' => false),

            '306'    => array('id' => 306, 'pId' => 3, 'name' => '销售投放管理', 'defaultCheck' => false),

            '30601'  => array('id' => 30601, 'pId' => 306, 'name' => '查看销售投放', 'defaultCheck' => false),
            '30602'  => array('id' => 30602, 'pId' => 306, 'name' => '添加销售投放', 'defaultCheck' => false),
            '30603'  => array('id' => 30603, 'pId' => 306, 'name' => '编辑销售投放', 'defaultCheck' => false),
            '30604'  => array('id' => 30604, 'pId' => 306, 'name' => '删除销售投放', 'defaultCheck' => false),
            '30605'  => array('id' => 30605, 'pId' => 306, 'name' => '审核销售投放', 'defaultCheck' => false),

            '308'    => array('id' => 308, 'pId' => 3, 'name' => '点位管理', 'defaultCheck' => false),

            '30801'  => array('id' => 30801, 'pId' => 308, 'name' => '查看点位', 'defaultCheck' => false),
            '30802'  => array('id' => 30802, 'pId' => 308, 'name' => '添加点位', 'defaultCheck' => false),
            '30803'  => array('id' => 30803, 'pId' => 308, 'name' => '编辑点位', 'defaultCheck' => false),
            '30804'  => array('id' => 30804, 'pId' => 308, 'name' => '删除点位', 'defaultCheck' => false),
            '30805'  => array('id' => 30805, 'pId' => 308, 'name' => '点位列表', 'defaultCheck' => false),
            '30806'  => array('id' => 30806, 'pId' => 308, 'name' => '更新优惠策略', 'defaultCheck' => false),
            '30807'  => array('id' => 30807, 'pId' => 308, 'name' => '点位导出', 'defaultCheck' => false),

            '309'    => array('id' => 309, 'pId' => 3, 'name' => '异常报警设置', 'defaultCheck' => false),

            '30901'  => array('id' => 30901, 'pId' => 309, 'name' => '查看异常报警设置', 'defaultCheck' => false),
            '30902'  => array('id' => 30902, 'pId' => 309, 'name' => '添加异常报警设置', 'defaultCheck' => false),
            '30903'  => array('id' => 30903, 'pId' => 309, 'name' => '编辑异常报警设置', 'defaultCheck' => false),
            '30904'  => array('id' => 30904, 'pId' => 309, 'name' => '删除异常报警设置', 'defaultCheck' => false),
            '30905'  => array('id' => 30905, 'pId' => 309, 'name' => '异常报警设置列表', 'defaultCheck' => false),

            '310'    => array('id' => 310, 'pId' => 3, 'name' => '客服上报管理', 'defaultCheck' => false),

            '31001'  => array('id' => 31001, 'pId' => 310, 'name' => '上报新故障', 'defaultCheck' => false),
            '31002'  => array('id' => 31002, 'pId' => 310, 'name' => '查看上报记录', 'defaultCheck' => false),

            '311'    => array('id' => 311, 'pId' => 3, 'name' => '投放商管理', 'defaultCheck' => false),

            '31101'  => array('id' => 31101, 'pId' => 311, 'name' => '查看投放商', 'defaultCheck' => false),
            '31102'  => array('id' => 31102, 'pId' => 311, 'name' => '添加投放商', 'defaultCheck' => false),
            '31103'  => array('id' => 31103, 'pId' => 311, 'name' => '编辑投放商', 'defaultCheck' => false),
            '31104'  => array('id' => 31104, 'pId' => 311, 'name' => '删除投放商', 'defaultCheck' => false),
            '31105'  => array('id' => 31105, 'pId' => 311, 'name' => '投放商列表', 'defaultCheck' => false),

            '312'    => array('id' => 312, 'pId' => 3, 'name' => '投放记录', 'defaultCheck' => false),

            '31201'  => array('id' => 31201, 'pId' => 312, 'name' => '楼宇投放记录', 'defaultCheck' => false),
            '31202'  => array('id' => 31202, 'pId' => 312, 'name' => '设备投放记录', 'defaultCheck' => false),

            '313'    => array('id' => 313, 'pId' => 3, 'name' => '异常报警发送记录', 'defaultCheck' => false),

            '31301'  => array('id' => 31301, 'pId' => 313, 'name' => '异常报警发送列表', 'defaultCheck' => false),

            '314'    => array('id' => 314, 'pId' => 3, 'name' => '故障现象管理', 'defaultCheck' => false),
            '31401'  => array('id' => 31401, 'pId' => 314, 'name' => '编辑故障现象', 'defaultCheck' => false),
            '31402'  => array('id' => 31402, 'pId' => 314, 'name' => '删除故障现象', 'defaultCheck' => false),
            '31403'  => array('id' => 31403, 'pId' => 314, 'name' => '查看故障现象', 'defaultCheck' => false),
            '31404'  => array('id' => 31404, 'pId' => 314, 'name' => '添加故障现象', 'defaultCheck' => false),

            '315'    => array('id' => 315, 'pId' => 3, 'name' => 'App版本号管理', 'defaultCheck' => false),

            '31501'  => array('id' => 31501, 'pId' => 315, 'name' => '编辑App版本号', 'defaultCheck' => false),
            '31502'  => array('id' => 31502, 'pId' => 315, 'name' => '删除App版本号', 'defaultCheck' => false),
            '31503'  => array('id' => 31503, 'pId' => 315, 'name' => '查看App版本号', 'defaultCheck' => false),
            '31504'  => array('id' => 31504, 'pId' => 315, 'name' => '添加App版本号', 'defaultCheck' => false),

            '316'    => array('id' => 316, 'pId' => 3, 'name' => '设备类型管理', 'defaultCheck' => false),

            '31601'  => array('id' => 31601, 'pId' => 316, 'name' => '编辑设备类型', 'defaultCheck' => false),
            '31602'  => array('id' => 31602, 'pId' => 316, 'name' => '删除设备类型', 'defaultCheck' => false),
            '31603'  => array('id' => 31603, 'pId' => 316, 'name' => '查看设备类型', 'defaultCheck' => false),
            '31604'  => array('id' => 31604, 'pId' => 316, 'name' => '添加设备类型', 'defaultCheck' => false),
            '31605'  => array('id' => 31605, 'pId' => 316, 'name' => '配置设备分类参数', 'defaultCheck' => false),
            '31606'  => array('id' => 31606, 'pId' => 316, 'name' => '配置设备分类参数同步', 'defaultCheck' => false),

            '317'    => array('id' => 317, 'pId' => 3, 'name' => '设备版本信息管理', 'defaultCheck' => false),

            '318'    => array('id' => 318, 'pId' => 3, 'name' => '设备冲泡器时间管理', 'defaultCheck' => false),
            '319'    => array('id' => 319, 'pId' => 3, 'name' => '渠道类型', 'defaultCheck' => false),

            '31901'  => array('id' => 31901, 'pId' => 319, 'name' => '查看渠道类型', 'defaultCheck' => false),
            '31902'  => array('id' => 31902, 'pId' => 319, 'name' => '添加渠道类型', 'defaultCheck' => false),
            '31903'  => array('id' => 31903, 'pId' => 319, 'name' => '编辑渠道类型', 'defaultCheck' => false),
            '31904'  => array('id' => 31904, 'pId' => 319, 'name' => '删除渠道类型', 'defaultCheck' => false),
            '31905'  => array('id' => 31905, 'pId' => 319, 'name' => '渠道类型列表查看', 'defaultCheck' => false),

            '320'    => array('id' => 320, 'pId' => 3, 'name' => '设备附件管理', 'defaultCheck' => false),
            '32001'  => array('id' => 32001, 'pId' => 320, 'name' => '编辑设备附件', 'defaultCheck' => false),
            '32002'  => array('id' => 32002, 'pId' => 320, 'name' => '删除设备附件', 'defaultCheck' => false),
            '32003'  => array('id' => 32003, 'pId' => 320, 'name' => '查看设备附件', 'defaultCheck' => false),
            '32004'  => array('id' => 32004, 'pId' => 320, 'name' => '添加设备附件', 'defaultCheck' => false),

            '321'    => array('id' => 321, 'pId' => 3, 'name' => '设备类型参数管理', 'defaultCheck' => false),
            '32101'  => array('id' => 32101, 'pId' => 321, 'name' => '编辑类型参数', 'defaultCheck' => false),
            '32102'  => array('id' => 32102, 'pId' => 321, 'name' => '删除类型参数', 'defaultCheck' => false),
            '32103'  => array('id' => 32103, 'pId' => 321, 'name' => '查看类型参数', 'defaultCheck' => false),
            '32104'  => array('id' => 32104, 'pId' => 321, 'name' => '添加设类型参数', 'defaultCheck' => false),

            '322'    => array('id' => 322, 'pId' => 3, 'name' => '语音控制管理', 'defaultCheck' => false),
            '32201'  => array('id' => 32201, 'pId' => 322, 'name' => '添加语音控制', 'defaultCheck' => false),
            '32202'  => array('id' => 32202, 'pId' => 322, 'name' => '编辑语音控制', 'defaultCheck' => false),
            '32203'  => array('id' => 32203, 'pId' => 322, 'name' => '审核语音控制', 'defaultCheck' => false),
            '32204'  => array('id' => 32204, 'pId' => 322, 'name' => '查看语音控制', 'defaultCheck' => false),

            '323'    => array('id' => 323, 'pId' => 3, 'name' => '楼宇管理', 'defaultCheck' => false),

            '32301'  => array('id' => 32301, 'pId' => 323, 'name' => '楼宇列表查看', 'defaultCheck' => false),
            '32302'  => array('id' => 32302, 'pId' => 323, 'name' => '楼宇创建', 'defaultCheck' => false),
            '32303'  => array('id' => 32303, 'pId' => 323, 'name' => '楼宇修改', 'defaultCheck' => false),
            '32304'  => array('id' => 32304, 'pId' => 323, 'name' => '楼宇转交', 'defaultCheck' => false),
            '32305'  => array('id' => 32305, 'pId' => 323, 'name' => '楼宇初评', 'defaultCheck' => false),
            '32306'  => array('id' => 32306, 'pId' => 323, 'name' => '楼宇详情查看', 'defaultCheck' => false),

            '324'    => array('id' => 324, 'pId' => 3, 'name' => '点位评估', 'defaultCheck' => false),

            '32401'  => array('id' => 32401, 'pId' => 324, 'name' => '点位评估列表查看', 'defaultCheck' => false),
            '32402'  => array('id' => 32402, 'pId' => 324, 'name' => '点位评估详情查看', 'defaultCheck' => false),
            '32403'  => array('id' => 32403, 'pId' => 324, 'name' => '点位评估修改', 'defaultCheck' => false),
            '32404'  => array('id' => 32404, 'pId' => 324, 'name' => '点位评估转交', 'defaultCheck' => false),
            '32405'  => array('id' => 32405, 'pId' => 324, 'name' => '点位评估审核', 'defaultCheck' => false),
            '32406'  => array('id' => 32406, 'pId' => 324, 'name' => '点位评估创建', 'defaultCheck' => false),
            '32407'  => array('id' => 32407, 'pId' => 324, 'name' => '点位评估导出', 'defaultCheck' => false),

            '325'    => array('id' => 325, 'pId' => 3, 'name' => '点位助手', 'defaultCheck' => false),

            '32501'  => array('id' => 32501, 'pId' => 325, 'name' => '点位助手查看', 'defaultCheck' => false),
            '32502'  => array('id' => 32502, 'pId' => 325, 'name' => '点位助手创建', 'defaultCheck' => false),
            '32503'  => array('id' => 32503, 'pId' => 325, 'name' => '点位助手修改', 'defaultCheck' => false),
            '32504'  => array('id' => 32504, 'pId' => 325, 'name' => '点位申请', 'defaultCheck' => false),
            '32505'  => array('id' => 32505, 'pId' => 325, 'name' => '点位申请导出', 'defaultCheck' => false),

            '4'      => array('id' => 4, 'pId' => 0, 'name' => '通讯录管理', 'open' => true, 'defaultCheck' => false),

            '401'    => array('id' => 401, 'pId' => 4, 'name' => '部门管理', 'defaultCheck' => false),

            '40101'  => array('id' => 40101, 'pId' => 401, 'name' => '查看部门', 'defaultCheck' => false),
            '40102'  => array('id' => 40102, 'pId' => 401, 'name' => '添加部门', 'defaultCheck' => false),
            '40103'  => array('id' => 40103, 'pId' => 401, 'name' => '编辑部门', 'defaultCheck' => false),
            '40104'  => array('id' => 40104, 'pId' => 401, 'name' => '删除部门', 'defaultCheck' => false),
            '40105'  => array('id' => 40105, 'pId' => 401, 'name' => '同步部门', 'defaultCheck' => false),

            '402'    => array('id' => 402, 'pId' => 4, 'name' => '成员管理', 'defaultCheck' => false),

            '40201'  => array('id' => 40201, 'pId' => 402, 'name' => '成员列表', 'defaultCheck' => false),
            '40202'  => array('id' => 40202, 'pId' => 402, 'name' => '添加成员', 'defaultCheck' => false),
            '40203'  => array('id' => 40203, 'pId' => 402, 'name' => '编辑成员', 'defaultCheck' => false),
            '40204'  => array('id' => 40204, 'pId' => 402, 'name' => '查看成员', 'defaultCheck' => false),
            '40205'  => array('id' => 40205, 'pId' => 402, 'name' => '删除成员', 'defaultCheck' => false),
            '40206'  => array('id' => 40206, 'pId' => 402, 'name' => '同步成员', 'defaultCheck' => false),

            '403'    => array('id' => 403, 'pId' => 4, 'name' => '标签管理', 'defaultCheck' => false),

            '40301'  => array('id' => 40301, 'pId' => 403, 'name' => '同步标签', 'defaultCheck' => false),
            '40302'  => array('id' => 40302, 'pId' => 403, 'name' => '添加标签', 'defaultCheck' => false),
            '40303'  => array('id' => 40303, 'pId' => 403, 'name' => '编辑标签', 'defaultCheck' => false),
            '40304'  => array('id' => 40304, 'pId' => 403, 'name' => '查看标签', 'defaultCheck' => false),
            '40305'  => array('id' => 40305, 'pId' => 403, 'name' => '删除标签', 'defaultCheck' => false),
            '40306'  => array('id' => 40306, 'pId' => 403, 'name' => '编辑标签成员', 'defaultCheck' => false),
            '40307'  => array('id' => 40307, 'pId' => 403, 'name' => '删除标签成员', 'defaultCheck' => false),

            '404'    => array('id' => 404, 'pId' => 4, 'name' => '成员角色管理', 'defaultCheck' => false),

            '40401'  => array('id' => 40401, 'pId' => 404, 'name' => '成员角色列表', 'defaultCheck' => false),
            '40402'  => array('id' => 40402, 'pId' => 404, 'name' => '添加成员角色', 'defaultCheck' => false),
            '40403'  => array('id' => 40403, 'pId' => 404, 'name' => '编辑成员角色', 'defaultCheck' => false),
            '40404'  => array('id' => 40404, 'pId' => 404, 'name' => '查看成员角色', 'defaultCheck' => false),
            '40405'  => array('id' => 40405, 'pId' => 404, 'name' => '删除成员角色', 'defaultCheck' => false),

            '405'    => array('id' => 405, 'pId' => 4, 'name' => '机构管理', 'defaultCheck' => false),

            '40501'  => array('id' => 40501, 'pId' => 405, 'name' => '机构列表', 'defaultCheck' => false),
            '40502'  => array('id' => 40502, 'pId' => 405, 'name' => '添加机构', 'defaultCheck' => false),
            '40503'  => array('id' => 40503, 'pId' => 405, 'name' => '编辑机构', 'defaultCheck' => false),
            '40504'  => array('id' => 40504, 'pId' => 405, 'name' => '查看机构', 'defaultCheck' => false),
            '40505'  => array('id' => 40505, 'pId' => 405, 'name' => '删除机构', 'defaultCheck' => false),

            '5'      => array('id' => 5, 'pId' => 0, 'name' => '门禁卡管理', 'open' => true, 'defaultCheck' => false),
            '501'    => array('id' => 501, 'pId' => 5, 'name' => 'RFID门禁卡管理', 'defaultCheck' => false),

            '50101'  => array('id' => 50101, 'pId' => 501, 'name' => '查看RFID门禁卡', 'defaultCheck' => false),
            '50102'  => array('id' => 50102, 'pId' => 501, 'name' => '添加RFID门禁卡', 'defaultCheck' => false),
            '50103'  => array('id' => 50103, 'pId' => 501, 'name' => '编辑RFID门禁卡', 'defaultCheck' => false),
            '50104'  => array('id' => 50104, 'pId' => 501, 'name' => '删除RFID门禁卡', 'defaultCheck' => false),
            '50105'  => array('id' => 50105, 'pId' => 501, 'name' => '批量添加门禁卡', 'defaultCheck' => false),

            '502'    => array('id' => 502, 'pId' => 5, 'name' => '门禁卡开门记录', 'defaultCheck' => false),
            '50201'  => array('id' => 50201, 'pId' => 502, 'name' => '导出门禁卡开门记录', 'defaultCheck' => false),
            '503'    => array('id' => 503, 'pId' => 5, 'name' => '门禁卡特殊开门', 'defaultCheck' => false),
            '504'    => array('id' => 504, 'pId' => 5, 'name' => '检测门禁卡开门', 'defaultCheck' => false),
            '505'    => array('id' => 505, 'pId' => 5, 'name' => '申请临时开门记录', 'defaultCheck' => false),
            '50501'  => array('id' => 50501, 'pId' => 505, 'name' => '编辑蓝牙锁', 'defaultCheck' => false),
            '50502'  => array('id' => 50502, 'pId' => 505, 'name' => '查看蓝牙锁', 'defaultCheck' => false),
            '50503'  => array('id' => 50503, 'pId' => 505, 'name' => '导出门禁卡临时开门记录', 'defaultCheck' => false),

            '6'      => array('id' => 6, 'pId' => 0, 'name' => '产品管理', 'open' => true, 'defaultCheck' => false),
            '601'    => array('id' => 601, 'pId' => 6, 'name' => '产品上下架管理', 'defaultCheck' => false),
            '602'    => array('id' => 602, 'pId' => 6, 'name' => '产品下架列表管理', 'defaultCheck' => false),
            '603'    => array('id' => 603, 'pId' => 6, 'name' => '产品上下架记录管理', 'defaultCheck' => false),
            '604'    => array('id' => 604, 'pId' => 6, 'name' => '产品上架处理', 'defaultCheck' => false),
            '605'    => array('id' => 605, 'pId' => 6, 'name' => '产品组料仓信息管理', 'defaultCheck' => false),
            '60501'  => array('id' => 60501, 'pId' => 605, 'name' => '查看产品组料仓', 'defaultCheck' => false),
            '60502'  => array('id' => 60502, 'pId' => 605, 'name' => '添加产品组料仓', 'defaultCheck' => false),
            '60503'  => array('id' => 60503, 'pId' => 605, 'name' => '编辑产品组料仓', 'defaultCheck' => false),
            '60504'  => array('id' => 60504, 'pId' => 605, 'name' => '删除产品组料仓', 'defaultCheck' => false),
            '60505'  => array('id' => 60505, 'pId' => 605, 'name' => '发布产品组料仓', 'defaultCheck' => false),
            '606'    => array('id' => 606, 'pId' => 6, 'name' => '单品管理', 'defaultCheck' => false),
            '60601'  => array('id' => 60601, 'pId' => 606, 'name' => '查看单品', 'defaultCheck' => false),
            '60602'  => array('id' => 60602, 'pId' => 606, 'name' => '添加单品', 'defaultCheck' => false),
            '60603'  => array('id' => 60603, 'pId' => 606, 'name' => '编辑单品', 'defaultCheck' => false),
            '60604'  => array('id' => 60604, 'pId' => 606, 'name' => '删除单品', 'defaultCheck' => false),
            '60605'  => array('id' => 60605, 'pId' => 606, 'name' => '发布单品', 'defaultCheck' => false),

            '607'    => array('id' => 607, 'pId' => 6, 'name' => '产品组管理', 'defaultCheck' => false),
            '60701'  => array('id' => 60701, 'pId' => 607, 'name' => '查看产品组', 'defaultCheck' => false),
            '60702'  => array('id' => 60702, 'pId' => 607, 'name' => '添加产品组', 'defaultCheck' => false),
            '60703'  => array('id' => 60703, 'pId' => 607, 'name' => '编辑产品组', 'defaultCheck' => false),
            '60704'  => array('id' => 60704, 'pId' => 607, 'name' => '产品组发布', 'defaultCheck' => false),
            '60705'  => array('id' => 60705, 'pId' => 607, 'name' => '删除产品组', 'defaultCheck' => false),
            '60706'  => array('id' => 60706, 'pId' => 607, 'name' => '查看产品组单品', 'defaultCheck' => false),
            '60707'  => array('id' => 60707, 'pId' => 607, 'name' => '查看产品组楼宇', 'defaultCheck' => false),

            '608'    => array('id' => 608, 'pId' => 6, 'name' => '设备工序管理', 'defaultCheck' => false),
            '60801'  => array('id' => 60801, 'pId' => 608, 'name' => '查看设备工序', 'defaultCheck' => false),
            '60802'  => array('id' => 60802, 'pId' => 608, 'name' => '添加设备工序', 'defaultCheck' => false),
            '60803'  => array('id' => 60803, 'pId' => 608, 'name' => '编辑设备工序', 'defaultCheck' => false),
            '60804'  => array('id' => 60804, 'pId' => 608, 'name' => '删除设备工序', 'defaultCheck' => false),

            '609'    => array('id' => 609, 'pId' => 6, 'name' => '进度条管理', 'defaultCheck' => false),
            '60901'  => array('id' => 60901, 'pId' => 609, 'name' => '查看进度条', 'defaultCheck' => false),
            '60902'  => array('id' => 60902, 'pId' => 609, 'name' => '添加进度条', 'defaultCheck' => false),
            '60903'  => array('id' => 60903, 'pId' => 609, 'name' => '编辑进度条', 'defaultCheck' => false),
            '60904'  => array('id' => 60904, 'pId' => 609, 'name' => '删除进度条', 'defaultCheck' => false),
            '60905'  => array('id' => 60905, 'pId' => 609, 'name' => '发布进度条', 'defaultCheck' => false),

            '610'    => array('id' => 610, 'pId' => 6, 'name' => '设备端活动管理', 'defaultCheck' => false),
            '61001'  => array('id' => 61001, 'pId' => 610, 'name' => '查看设备端活动', 'defaultCheck' => false),
            '61002'  => array('id' => 61002, 'pId' => 610, 'name' => '添加设备端活动', 'defaultCheck' => false),
            '61003'  => array('id' => 61003, 'pId' => 610, 'name' => '编辑设备端活动', 'defaultCheck' => false),
            '61004'  => array('id' => 61004, 'pId' => 610, 'name' => '删除设备端活动', 'defaultCheck' => false),
            '61005'  => array('id' => 61005, 'pId' => 610, 'name' => '发布设备端活动', 'defaultCheck' => false),

            '611'    => array('id' => 611, 'pId' => 6, 'name' => '产品标签管理', 'defaultCheck' => false),
            '61101'  => array('id' => 61101, 'pId' => 611, 'name' => '查看产品标签', 'defaultCheck' => false),
            '61102'  => array('id' => 61102, 'pId' => 611, 'name' => '添加产品标签', 'defaultCheck' => false),
            '61103'  => array('id' => 61103, 'pId' => 611, 'name' => '编辑产品标签', 'defaultCheck' => false),
            '61104'  => array('id' => 61104, 'pId' => 611, 'name' => '删除产品标签', 'defaultCheck' => false),
            '61105'  => array('id' => 61105, 'pId' => 611, 'name' => '上线产品标签', 'defaultCheck' => false),

            '612'    => array('id' => 612, 'pId' => 6, 'name' => '咖语管理', 'defaultCheck' => false),
            '61201'  => array('id' => 61201, 'pId' => 612, 'name' => '查看咖语列表', 'defaultCheck' => false),
            '61202'  => array('id' => 61202, 'pId' => 612, 'name' => '添加咖语信息', 'defaultCheck' => false),
            '61203'  => array('id' => 61203, 'pId' => 612, 'name' => '编辑咖语信息', 'defaultCheck' => false),
            '61204'  => array('id' => 61204, 'pId' => 612, 'name' => '检索咖语信息', 'defaultCheck' => false),
            '61205'  => array('id' => 61205, 'pId' => 612, 'name' => '查看咖语详细信息', 'defaultCheck' => false),
            '61206'  => array('id' => 61206, 'pId' => 612, 'name' => '删除指定咖语信息', 'defaultCheck' => false),

            '613'    => array('id' => 613, 'pId' => 6, 'name' => '成份管理', 'defaultCheck' => false),
            '61301'  => array('id' => 61301, 'pId' => 613, 'name' => '查看单品成份', 'defaultCheck' => false),
            '61302'  => array('id' => 61302, 'pId' => 613, 'name' => '添加单品成份', 'defaultCheck' => false),
            '61303'  => array('id' => 61303, 'pId' => 613, 'name' => '修改单品成份', 'defaultCheck' => false),
            '61304'  => array('id' => 61304, 'pId' => 613, 'name' => '删除单品成份', 'defaultCheck' => false),

            '614'    => array('id' => 614, 'pId' => 6, 'name' => '轻食管理', 'defaultCheck' => false),
            '61401'  => array('id' => 61401, 'pId' => 614, 'name' => '轻食产品上下架', 'defaultCheck' => false),

            '7'      => array('id' => 7, 'pId' => 0, 'name' => '运维管理', 'open' => true, 'defaultCheck' => false),

            '701'    => array('id' => 701, 'pId' => 7, 'name' => '运维任务管理', 'defaultCheck' => false),

            '70101'  => array('id' => 70101, 'pId' => 701, 'name' => '查看运维任务', 'defaultCheck' => false),
            '70102'  => array('id' => 70102, 'pId' => 701, 'name' => '添加运维任务', 'defaultCheck' => false),
            '70103'  => array('id' => 70103, 'pId' => 701, 'name' => '编辑运维任务', 'defaultCheck' => false),
            '70104'  => array('id' => 70104, 'pId' => 701, 'name' => '删除运维任务', 'defaultCheck' => false),
            '70105'  => array('id' => 70105, 'pId' => 701, 'name' => '日常任务管理', 'defaultCheck' => false),
            '70106'  => array('id' => 70106, 'pId' => 701, 'name' => '设置备用料包', 'defaultCheck' => false),
            '70107'  => array('id' => 70107, 'pId' => 701, 'name' => '作废运维任务', 'defaultCheck' => false),
            '729'    => array('id' => 729, 'pId' => 7, 'name' => '运维任务统计管理', 'defaultCheck' => false),
            '730'    => array('id' => 730, 'pId' => 7, 'name' => '设备故障任务管理', 'defaultCheck' => false),
            '73001'  => array('id' => 73001, 'pId' => 730, 'name' => '删除故障任务记录', 'defaultCheck' => false),
            '73002'  => array('id' => 73002, 'pId' => 730, 'name' => '下发故障任务记录', 'defaultCheck' => false),
            '73003'  => array('id' => 73003, 'pId' => 730, 'name' => '故障任务记录转次日', 'defaultCheck' => false),

            '702'    => array('id' => 702, 'pId' => 7, 'name' => '运维人员管理', 'defaultCheck' => false),

            '70201'  => array('id' => 70201, 'pId' => 702, 'name' => '查看运维人员', 'defaultCheck' => false),
            '70202'  => array('id' => 70202, 'pId' => 702, 'name' => '添加运维人员', 'defaultCheck' => false),
            '70204'  => array('id' => 70204, 'pId' => 702, 'name' => '重置运维人员状态', 'defaultCheck' => false),
            '70205'  => array('id' => 70205, 'pId' => 702, 'name' => '运维人员列表', 'defaultCheck' => false),
            '70206'  => array('id' => 70206, 'pId' => 702, 'name' => '配送分工', 'defaultCheck' => false),
            '70207'  => array('id' => 70207, 'pId' => 702, 'name' => '个人数据统计', 'defaultCheck' => false),
            '70208'  => array('id' => 70208, 'pId' => 702, 'name' => '任务记录', 'defaultCheck' => false),
            '70209'  => array('id' => 70209, 'pId' => 702, 'name' => '配送记录', 'defaultCheck' => false),
            '702010' => array('id' => 702010, 'pId' => 702, 'name' => '领料记录', 'defaultCheck' => false),
            '702011' => array('id' => 702011, 'pId' => 702, 'name' => '换料记录', 'defaultCheck' => false),
            '702012' => array('id' => 702012, 'pId' => 702, 'name' => '剩余物料', 'defaultCheck' => false),
            '702013' => array('id' => 702013, 'pId' => 702, 'name' => '剩余物料修改申请', 'defaultCheck' => false),
            '702014' => array('id' => 702014, 'pId' => 702, 'name' => '剩余物料修改确认', 'defaultCheck' => false),
            '702015' => array('id' => 702015, 'pId' => 702, 'name' => '剩余物料申请记录', 'defaultCheck' => false),
            '702016' => array('id' => 702016, 'pId' => 702, 'name' => '人员管理', 'defaultCheck' => false),

            '703'    => array('id' => 703, 'pId' => 7, 'name' => '配送通知管理', 'defaultCheck' => false),

            '70301'  => array('id' => 70301, 'pId' => 703, 'name' => '查看配送通知', 'defaultCheck' => false),
            '70302'  => array('id' => 70302, 'pId' => 703, 'name' => '添加配送通知', 'defaultCheck' => false),
            '70303'  => array('id' => 70303, 'pId' => 703, 'name' => '删除配送通知', 'defaultCheck' => false),

            '705'    => array('id' => 705, 'pId' => 7, 'name' => '水单管理', 'defaultCheck' => false),
            '70501'  => array('id' => 70501, 'pId' => 705, 'name' => '查看水单', 'defaultCheck' => false),
            '70502'  => array('id' => 70502, 'pId' => 705, 'name' => '添加水单', 'defaultCheck' => false),
            '70503'  => array('id' => 70503, 'pId' => 705, 'name' => '编辑水单', 'defaultCheck' => false),
            '70504'  => array('id' => 70504, 'pId' => 705, 'name' => '删除水单', 'defaultCheck' => false),
            '70505'  => array('id' => 70505, 'pId' => 705, 'name' => '下单操作', 'defaultCheck' => false),

            '718'    => array('id' => 718, 'pId' => 7, 'name' => '料仓预警值管理', 'defaultCheck' => false),
            '71801'  => array('id' => 71801, 'pId' => 718, 'name' => '添加料仓预警值', 'defaultCheck' => false),
            '71802'  => array('id' => 71802, 'pId' => 718, 'name' => '编辑料仓预警值', 'defaultCheck' => false),
            '71803'  => array('id' => 71803, 'pId' => 718, 'name' => '删除料仓预警值', 'defaultCheck' => false),
            '71804'  => array('id' => 71804, 'pId' => 718, 'name' => '查看料仓预警值', 'defaultCheck' => false),

            '719'    => array('id' => 719, 'pId' => 7, 'name' => '楼宇日常任务管理', 'defaultCheck' => false),
            '71901'  => array('id' => 71901, 'pId' => 719, 'name' => '添加楼宇日常任务', 'defaultCheck' => false),
            '71902'  => array('id' => 71902, 'pId' => 719, 'name' => '编辑楼宇日常任务', 'defaultCheck' => false),
            '71903'  => array('id' => 71903, 'pId' => 719, 'name' => '删除楼宇日常任务', 'defaultCheck' => false),
            '71904'  => array('id' => 71904, 'pId' => 719, 'name' => '查看楼宇日常任务', 'defaultCheck' => false),

            '720'    => array('id' => 720, 'pId' => 7, 'name' => '公司设备类型日常任务管理', 'defaultCheck' => false),
            '72001'  => array('id' => 72001, 'pId' => 720, 'name' => '添加公司设备类型日常任务', 'defaultCheck' => false),
            '72002'  => array('id' => 72002, 'pId' => 720, 'name' => '编辑公司设备类型日常任务', 'defaultCheck' => false),
            '72003'  => array('id' => 72003, 'pId' => 720, 'name' => '删除公司设备类型日常任务', 'defaultCheck' => false),
            '72004'  => array('id' => 72004, 'pId' => 720, 'name' => '查看公司设备类型日常任务', 'defaultCheck' => false),

            '706'    => array('id' => 706, 'pId' => 7, 'name' => '配送数据统计管理', 'defaultCheck' => false),

            '707'    => array('id' => 707, 'pId' => 7, 'name' => '水单记录管理', 'defaultCheck' => false),

            '708'    => array('id' => 708, 'pId' => 7, 'name' => '设备月用水量统计', 'defaultCheck' => false),
            '709'    => array('id' => 709, 'pId' => 7, 'name' => '开箱签到记录', 'defaultCheck' => false),

            '710'    => array('id' => 710, 'pId' => 7, 'name' => '物料记录统计', 'defaultCheck' => false),

            '711'    => array('id' => 711, 'pId' => 7, 'name' => '物料对比统计', 'defaultCheck' => false),

            '712'    => array('id' => 712, 'pId' => 7, 'name' => '运维工作数据统计', 'defaultCheck' => false),
            '713'    => array('id' => 713, 'pId' => 7, 'name' => '出库明细统计', 'defaultCheck' => false),
            '714'    => array('id' => 714, 'pId' => 7, 'name' => '入库明细统计', 'defaultCheck' => false),
            '715'    => array('id' => 715, 'pId' => 7, 'name' => '故障记录统计', 'defaultCheck' => false),
            '717'    => array('id' => 717, 'pId' => 7, 'name' => '节假日不运维管理', 'defaultCheck' => false),
            '721'    => array('id' => 721, 'pId' => 7, 'name' => '批量添加楼宇节假日不运维', 'defaultCheck' => false),
            '722'    => array('id' => 722, 'pId' => 7, 'name' => '批量删除楼宇节假日不运维', 'defaultCheck' => false),

            '723'    => array('id' => 723, 'pId' => 7, 'name' => '零售活动人员二维码管理', 'defaultCheck' => false),
            '72301'  => array('id' => 72301, 'pId' => 723, 'name' => '生成零售活动人员二维码', 'defaultCheck' => false),
            '72302'  => array('id' => 72302, 'pId' => 723, 'name' => '查看零售活动人员二维码', 'defaultCheck' => false),
            '72303'  => array('id' => 72303, 'pId' => 723, 'name' => '删除零售活动人员二维码', 'defaultCheck' => false),

            '724'    => array('id' => 724, 'pId' => 7, 'name' => '零售活动人员管理', 'defaultCheck' => false),
            '72401'  => array('id' => 72401, 'pId' => 724, 'name' => '零售活动人员列表', 'defaultCheck' => false),
            '72402'  => array('id' => 72402, 'pId' => 724, 'name' => '零售活动人员添加', 'defaultCheck' => false),
            '72403'  => array('id' => 72403, 'pId' => 724, 'name' => '零售活动人员修改', 'defaultCheck' => false),
            '72404'  => array('id' => 72404, 'pId' => 724, 'name' => '零售活动人员删除', 'defaultCheck' => false),

            '725'    => array('id' => 725, 'pId' => 7, 'name' => '节假日管理', 'defaultCheck' => false),
            '72501'  => array('id' => 72501, 'pId' => 725, 'name' => '节假日管理查看', 'defaultCheck' => false),
            '72502'  => array('id' => 72502, 'pId' => 725, 'name' => '节假日管理编辑', 'defaultCheck' => false),

            '726'    => array('id' => 726, 'pId' => 7, 'name' => '预磨豆设置', 'defaultCheck' => false),
            '72601'  => array('id' => 72601, 'pId' => 726, 'name' => '预磨豆设置编辑', 'defaultCheck' => false),
            '72602'  => array('id' => 72602, 'pId' => 726, 'name' => '预磨豆设置删除', 'defaultCheck' => false),
            '72603'  => array('id' => 72603, 'pId' => 726, 'name' => '预磨豆设置列表', 'defaultCheck' => false),
            '72604'  => array('id' => 72604, 'pId' => 726, 'name' => '预磨豆设置添加', 'defaultCheck' => false),

            '727'    => array('id' => 727, 'pId' => 7, 'name' => '料盒速度列表', 'defaultCheck' => false),
            '72701'  => array('id' => 72701, 'pId' => 727, 'name' => '料盒速度添加', 'defaultCheck' => false),
            '72702'  => array('id' => 72702, 'pId' => 727, 'name' => '料盒速度编辑', 'defaultCheck' => false),
            '72703'  => array('id' => 72703, 'pId' => 727, 'name' => '料盒速度删除', 'defaultCheck' => false),

            '728'    => array('id' => 728, 'pId' => 7, 'name' => '清洗设备类型列表', 'defaultCheck' => false),
            '72801'  => array('id' => 72801, 'pId' => 728, 'name' => '清洗设备类型添加', 'defaultCheck' => false),
            '72802'  => array('id' => 72802, 'pId' => 728, 'name' => '清洗设备类型编辑', 'defaultCheck' => false),
            '72803'  => array('id' => 72803, 'pId' => 728, 'name' => '清洗设备类型删除', 'defaultCheck' => false),
            '731'    => array('id' => 731, 'pId' => 7, 'name' => '楼宇点位统计管理', 'defaultCheck' => false),

            '8'      => array('id' => 8, 'pId' => 0, 'name' => '灯带管理', 'open' => true, 'defaultCheck' => false),

            '801'    => array('id' => 801, 'pId' => 8, 'name' => '饮品组管理', 'defaultCheck' => false),
            '80101'  => array('id' => 80101, 'pId' => 801, 'name' => '添加饮品组', 'defaultCheck' => false),
            '80102'  => array('id' => 80102, 'pId' => 801, 'name' => '编辑饮品组', 'defaultCheck' => false),
            '80103'  => array('id' => 80103, 'pId' => 801, 'name' => '删除饮品组', 'defaultCheck' => false),
            '80104'  => array('id' => 80104, 'pId' => 801, 'name' => '饮品组使用详情', 'defaultCheck' => false),

            '802'    => array('id' => 802, 'pId' => 8, 'name' => '灯带策略管理', 'defaultCheck' => false),
            '80201'  => array('id' => 80201, 'pId' => 802, 'name' => '添加灯带策略', 'defaultCheck' => false),
            '80202'  => array('id' => 80202, 'pId' => 802, 'name' => '编辑灯带策略', 'defaultCheck' => false),
            '80203'  => array('id' => 80203, 'pId' => 802, 'name' => '删除灯带策略', 'defaultCheck' => false),
            '80204'  => array('id' => 80204, 'pId' => 802, 'name' => '查看灯带策略', 'defaultCheck' => false),

            '803'    => array('id' => 803, 'pId' => 8, 'name' => '灯带场景管理', 'defaultCheck' => false),
            '80301'  => array('id' => 80301, 'pId' => 803, 'name' => '添加灯带场景', 'defaultCheck' => false),
            '80302'  => array('id' => 80302, 'pId' => 803, 'name' => '编辑灯带场景', 'defaultCheck' => false),
            '80303'  => array('id' => 80303, 'pId' => 803, 'name' => '删除灯带场景', 'defaultCheck' => false),
            '80304'  => array('id' => 80304, 'pId' => 803, 'name' => '查看灯带场景', 'defaultCheck' => false),

            '804'    => array('id' => 804, 'pId' => 8, 'name' => '灯带方案管理', 'defaultCheck' => false),
            '80401'  => array('id' => 80401, 'pId' => 804, 'name' => '添加灯带方案', 'defaultCheck' => false),
            '80402'  => array('id' => 80402, 'pId' => 804, 'name' => '编辑灯带方案', 'defaultCheck' => false),
            '80403'  => array('id' => 80403, 'pId' => 804, 'name' => '删除灯带方案', 'defaultCheck' => false),
            '80404'  => array('id' => 80404, 'pId' => 804, 'name' => '查看灯带方案', 'defaultCheck' => false),
            '80405'  => array('id' => 80405, 'pId' => 804, 'name' => '查看灯带楼宇方案', 'defaultCheck' => false),
            '80406'  => array('id' => 80406, 'pId' => 804, 'name' => '批量添加楼宇', 'defaultCheck' => false),
            '80407'  => array('id' => 80407, 'pId' => 804, 'name' => '批量移除楼宇', 'defaultCheck' => false),

            '80408'  => array('id' => 80408, 'pId' => 804, 'name' => '发布方案', 'defaultCheck' => false),
            '80409'  => array('id' => 80409, 'pId' => 804, 'name' => '设置默认方案', 'defaultCheck' => false),

            '9'      => array('id' => 9, 'pId' => 0, 'name' => '系统设置', 'open' => true, 'defaultCheck' => false),

            '901'    => array('id' => 901, 'pId' => 9, 'name' => '系统设置列表', 'defaultCheck' => false),
            '90101'  => array('id' => 90101, 'pId' => 901, 'name' => '编辑系统设置', 'defaultCheck' => false),
            '90102'  => array('id' => 90102, 'pId' => 901, 'name' => '查看系统设置', 'defaultCheck' => false),

            '902'    => array('id' => 902, 'pId' => 9, 'name' => '修改密码', 'defaultCheck' => false),

            '903'    => array('id' => 903, 'pId' => 9, 'name' => '角色管理', 'defaultCheck' => false),

            '90301'  => array('id' => 90301, 'pId' => 903, 'name' => '添加角色', 'defaultCheck' => false),
            '90302'  => array('id' => 90302, 'pId' => 903, 'name' => '编辑角色', 'defaultCheck' => false),
            '90303'  => array('id' => 90303, 'pId' => 903, 'name' => '删除角色', 'defaultCheck' => false),
            '90304'  => array('id' => 90304, 'pId' => 903, 'name' => '查看角色', 'defaultCheck' => false),

            '904'    => array('id' => 904, 'pId' => 9, 'name' => '管理员管理', 'defaultCheck' => false),

            '90401'  => array('id' => 90401, 'pId' => 904, 'name' => '添加管理员', 'defaultCheck' => false),
            '90402'  => array('id' => 90402, 'pId' => 904, 'name' => '编辑管理员', 'defaultCheck' => false),
            '90403'  => array('id' => 90403, 'pId' => 904, 'name' => '删除管理员', 'defaultCheck' => false),
            '90404'  => array('id' => 90404, 'pId' => 904, 'name' => '查看管理员', 'defaultCheck' => false),
            '905'    => array('id' => 905, 'pId' => 9, 'name' => '操作日志', 'defaultCheck' => false),
            '906'    => array('id' => 906, 'pId' => 9, 'name' => '下载远程文件', 'defaultCheck' => false),

            '10'     => array('id' => 10, 'pId' => 0, 'name' => '活动管理', 'open' => true, 'defaultCheck' => false),

            '1001'   => array('id' => 1001, 'pId' => 10, 'name' => '城市优惠策略', 'defaultCheck' => false),
            '100101' => array('id' => 100101, 'pId' => 1001, 'name' => '添加城市优惠策略', 'defaultCheck' => false),
            '100102' => array('id' => 100102, 'pId' => 1001, 'name' => '编辑城市优惠策略', 'defaultCheck' => false),
            '100103' => array('id' => 100103, 'pId' => 1001, 'name' => '删除城市优惠策略', 'defaultCheck' => false),
            '100104' => array('id' => 100104, 'pId' => 1001, 'name' => '查看城市优惠策略', 'defaultCheck' => false),

            '1002'   => array('id' => 1002, 'pId' => 10, 'name' => '支付方式管理', 'defaultCheck' => false),
            '100201' => array('id' => 100201, 'pId' => 1002, 'name' => '编辑支付方式', 'defaultCheck' => false),
            '100202' => array('id' => 100202, 'pId' => 1002, 'name' => '查看支付方式', 'defaultCheck' => false),

            '1004'   => array('id' => 1004, 'pId' => 10, 'name' => '楼宇支付策略', 'defaultCheck' => false),
            '100401' => array('id' => 100401, 'pId' => 1004, 'name' => '添加优惠楼宇', 'defaultCheck' => false),
            '100402' => array('id' => 100402, 'pId' => 1004, 'name' => '优惠楼宇修改', 'defaultCheck' => false),
            '100403' => array('id' => 100403, 'pId' => 1004, 'name' => '优惠楼宇删除', 'defaultCheck' => false),
            '100404' => array('id' => 100404, 'pId' => 1004, 'name' => '优惠楼宇查看', 'defaultCheck' => false),

            '1005'   => array('id' => 1005, 'pId' => 10, 'name' => '支付方式优惠策略', 'defaultCheck' => false),
            '100501' => array('id' => 100501, 'pId' => 1005, 'name' => '优惠策略添加', 'defaultCheck' => false),
            '100502' => array('id' => 100502, 'pId' => 1005, 'name' => '优惠策略修改', 'defaultCheck' => false),
            '100503' => array('id' => 100503, 'pId' => 1005, 'name' => '优惠策略删除', 'defaultCheck' => false),
            '100504' => array('id' => 100504, 'pId' => 1005, 'name' => '优惠策略发布', 'defaultCheck' => false),
            '100505' => array('id' => 100505, 'pId' => 1005, 'name' => '优惠策略查看', 'defaultCheck' => false),

            // 活动管理 ->周边商品-周边商品
            '11'     => array('id' => 11, 'pId' => 0, 'name' => '周边商城', 'open' => true, 'defaultCheck' => false),
            '1101'   => array('id' => 1101, 'pId' => 11, 'name' => '商品管理', 'defaultCheck' => false),
            '110101' => array('id' => 110101, 'pId' => 1101, 'name' => '添加商品', 'defaultCheck' => false),
            '110102' => array('id' => 110102, 'pId' => 1101, 'name' => '编辑商品', 'defaultCheck' => false),
            '110103' => array('id' => 110103, 'pId' => 1101, 'name' => '删除商品', 'defaultCheck' => false),
            '110104' => array('id' => 110104, 'pId' => 1101, 'name' => '查看商品', 'defaultCheck' => false),
            '110105' => array('id' => 110105, 'pId' => 1101, 'name' => '审核商品', 'defaultCheck' => false),
            '110106' => array('id' => 110106, 'pId' => 1101, 'name' => '邮费设置', 'defaultCheck' => false),

            '1102'   => array('id' => 1102, 'pId' => 11, 'name' => '商品订单', 'defaultCheck' => false),
            '110201' => array('id' => 110201, 'pId' => 1102, 'name' => '退款审核', 'defaultCheck' => false),
            '110202' => array('id' => 110202, 'pId' => 1102, 'name' => '发货', 'defaultCheck' => false),
            '110203' => array('id' => 110203, 'pId' => 1102, 'name' => '订单退款', 'defaultCheck' => false),
            '110204' => array('id' => 110204, 'pId' => 1102, 'name' => '查看订单', 'defaultCheck' => false),

            // 智能客服
            '12'     => array('id' => 12, 'pId' => 0, 'name' => '自动回复', 'open' => true, 'defaultCheck' => false),

            '1203'   => array('id' => 1203, 'pId' => 12, 'name' => '类别管理', 'defaultCheck' => false),
            '120301' => array('id' => 120301, 'pId' => 1203, 'name' => '添加类别', 'defaultCheck' => false),
            '120302' => array('id' => 120302, 'pId' => 1203, 'name' => '修改类别', 'defaultCheck' => false),
            '120303' => array('id' => 120303, 'pId' => 1203, 'name' => '查看类别', 'defaultCheck' => false),

            '1202'   => array('id' => 1202, 'pId' => 12, 'name' => '问题管理', 'defaultCheck' => false),
            '120201' => array('id' => 120201, 'pId' => 1202, 'name' => '添加问题', 'defaultCheck' => false),
            '120202' => array('id' => 120202, 'pId' => 1202, 'name' => '查看问题', 'defaultCheck' => false),
            '120203' => array('id' => 120203, 'pId' => 1202, 'name' => '修改问题', 'defaultCheck' => false),
            '120204' => array('id' => 120204, 'pId' => 1202, 'name' => '删除问题', 'defaultCheck' => false),
            '120205' => array('id' => 120205, 'pId' => 1202, 'name' => '问题详情', 'defaultCheck' => false),

            '1204'   => array('id' => 1204, 'pId' => 12, 'name' => '统计管理', 'defaultCheck' => false),
            '120401' => array('id' => 120401, 'pId' => 1204, 'name' => 'Excel导出', 'defaultCheck' => false),
            '120402' => array('id' => 120402, 'pId' => 1204, 'name' => '统计查看', 'defaultCheck' => false),

            // 智能零售 -> 订单系统
            '13'     => array('id' => 13, 'pId' => 0, 'name' => '订单系统', 'open' => true, 'defaultCheck' => false),
            '1301'   => array('id' => 1301, 'pId' => 13, 'name' => '订单管理', 'defaultCheck' => false),
            '130101' => array('id' => 130101, 'pId' => 1301, 'name' => '订单管理详情查看', 'defaultCheck' => false),
            '130102' => array('id' => 130102, 'pId' => 1301, 'name' => '订单支付信息汇总查看', 'defaultCheck' => false),
            '130103' => array('id' => 130103, 'pId' => 1301, 'name' => '订单支付信息汇总导出', 'defaultCheck' => false),
            '1302'   => array('id' => 1302, 'pId' => 13, 'name' => '订单商品', 'defaultCheck' => false),
            '130201' => array('id' => 130201, 'pId' => 1302, 'name' => '订单商品导出', 'defaultCheck' => false),
            '130202' => array('id' => 130202, 'pId' => 1302, 'name' => '订单商品查看', 'defaultCheck' => false),
            '1303'   => array('id' => 1303, 'pId' => 13, 'name' => '退款记录', 'defaultCheck' => false),
            '130301' => array('id' => 130301, 'pId' => 1303, 'name' => '退款详情', 'defaultCheck' => false),
            '130302' => array('id' => 130302, 'pId' => 1303, 'name' => '退款记录列表查看', 'defaultCheck' => false),
            '1304'   => array('id' => 1304, 'pId' => 13, 'name' => '消费记录', 'defaultCheck' => false),
            '130401' => array('id' => 130401, 'pId' => 1304, 'name' => '消费记录详情查看', 'defaultCheck' => false),
            '130402' => array('id' => 130402, 'pId' => 1304, 'name' => '消费记录列表导出', 'defaultCheck' => false),
            '130403' => array('id' => 130403, 'pId' => 1304, 'name' => '消费记录更新退还状态', 'defaultCheck' => false),
            '1305'   => array('id' => 1305, 'pId' => 13, 'name' => '商品汇总', 'defaultCheck' => false),
            '130501' => array('id' => 130501, 'pId' => 1305, 'name' => '商品汇总列表查看', 'defaultCheck' => false),
            // '1306'   => array('id' => 1306, 'pId' => 13, 'name' => '失败记录', 'defaultCheck' => false),
            // '130601' => array('id' => 130601, 'pId' => 1306, 'name' => '失败详情', 'defaultCheck' => false),
            // '1307'   => array('id' => 1307, 'pId' => 13, 'name' => '咖豆充值', 'defaultCheck' => false),
            // '130701' => array('id' => 130701, 'pId' => 1307, 'name' => '咖豆充值记录', 'defaultCheck' => false),
            // '1308'   => array('id' => 1308, 'pId' => 13, 'name' => '咖豆消费', 'defaultCheck' => false),
            // '130801' => array('id' => 130801, 'pId' => 1308, 'name' => '咖豆消费记录', 'defaultCheck' => false),

            '14'     => array('id' => 14, 'pId' => 0, 'name' => '供应链出库管理', 'open' => true, 'defaultCheck' => false),
            '1401'   => array('id' => 1401, 'pId' => 14, 'name' => '出库单管理', 'defaultCheck' => false),
            '140101' => array('id' => 140101, 'pId' => 1401, 'name' => '复审出库单', 'defaultCheck' => false),
            '140102' => array('id' => 140102, 'pId' => 1401, 'name' => '审核出库单', 'defaultCheck' => false),
            '140103' => array('id' => 140103, 'pId' => 1401, 'name' => '查看出库单', 'defaultCheck' => false),

            '1402'   => array('id' => 1402, 'pId' => 14, 'name' => '预估单管理', 'defaultCheck' => false),
            '140201' => array('id' => 140201, 'pId' => 1402, 'name' => '预估单配货', 'defaultCheck' => false),
            '140202' => array('id' => 140202, 'pId' => 1402, 'name' => '修改预估单', 'defaultCheck' => false),

            '15'     => array('id' => 15, 'pId' => 0, 'name' => '供应链入库管理', 'open' => true, 'defaultCheck' => false),
            '1501'   => array('id' => 1501, 'pId' => 15, 'name' => '入库信息管理', 'defaultCheck' => false),

            '150101' => array('id' => 150101, 'pId' => 1501, 'name' => '编辑入库信息', 'defaultCheck' => false),
            '150102' => array('id' => 150102, 'pId' => 1501, 'name' => '删除入库信息', 'defaultCheck' => false),
            '150103' => array('id' => 150103, 'pId' => 1501, 'name' => '查看入库信息', 'defaultCheck' => false),
            '150104' => array('id' => 150104, 'pId' => 1501, 'name' => '添加入库信息', 'defaultCheck' => false),
            '150105' => array('id' => 150105, 'pId' => 1501, 'name' => '配送员归还', 'defaultCheck' => false),
            '150106' => array('id' => 150106, 'pId' => 1501, 'name' => '入库审核', 'defaultCheck' => false),
            '150107' => array('id' => 150107, 'pId' => 1501, 'name' => '供应链采购', 'defaultCheck' => false),
            '150108' => array('id' => 150108, 'pId' => 1501, 'name' => '其它原因', 'defaultCheck' => false),

            '16'     => array('id' => 16, 'pId' => 0, 'name' => '供应链库存核算', 'open' => true, 'defaultCheck' => false),
            '1601'   => array('id' => 1601, 'pId' => 16, 'name' => '库存信息管理', 'defaultCheck' => false),

            '160101' => array('id' => 160101, 'pId' => 1601, 'name' => '编辑库存信息', 'defaultCheck' => false),
            '160102' => array('id' => 160102, 'pId' => 1601, 'name' => '删除库存信息', 'defaultCheck' => false),
            '160103' => array('id' => 160103, 'pId' => 1601, 'name' => '查看库存信息', 'defaultCheck' => false),
            '160104' => array('id' => 160104, 'pId' => 1601, 'name' => '添加库存信息', 'defaultCheck' => false),

            '17'     => array('id' => 17, 'pId' => 0, 'name' => '供应链报表信息', 'open' => true, 'defaultCheck' => false),

            '1701'   => array('id' => 1701, 'pId' => 17, 'name' => '物料消耗预测', 'defaultCheck' => false),

            '1702'   => array('id' => 1702, 'pId' => 17, 'name' => '物料分类消耗统计', 'defaultCheck' => false),

            '170201' => array('id' => 170201, 'pId' => 1702, 'name' => '物料分类消耗统计列表', 'defaultCheck' => false),
            '170202' => array('id' => 170202, 'pId' => 1702, 'name' => '物料分类消耗统计详情', 'defaultCheck' => false),
            '170203' => array('id' => 170203, 'pId' => 1702, 'name' => '物料分类消耗统计运维导出', 'defaultCheck' => false),
            '170204' => array('id' => 170204, 'pId' => 1702, 'name' => '物料分类消耗统计财务导出', 'defaultCheck' => false),

            '1703'   => array('id' => 1703, 'pId' => 17, 'name' => '物料楼宇消耗统计', 'defaultCheck' => false),

            '170301' => array('id' => 170301, 'pId' => 1703, 'name' => '物料楼宇消耗统计列表', 'defaultCheck' => false),
            '170302' => array('id' => 170302, 'pId' => 1703, 'name' => '物料楼宇消耗统计运维导出', 'defaultCheck' => false),
            '170303' => array('id' => 170303, 'pId' => 1703, 'name' => '物料楼宇消耗统计财务导出', 'defaultCheck' => false),

            '1704'   => array('id' => 1704, 'pId' => 17, 'name' => '物料消耗差异值管理', 'defaultCheck' => false),

            '170401' => array('id' => 170401, 'pId' => 1704, 'name' => '物料消耗差异值列表', 'defaultCheck' => false),
            '170402' => array('id' => 170402, 'pId' => 1704, 'name' => '物料消耗差异值编辑', 'defaultCheck' => false),

            '1705'   => array('id' => 1705, 'pId' => 17, 'name' => '物料消耗记录', 'defaultCheck' => false),
            '170501' => array('id' => 170501, 'pId' => 1705, 'name' => '物料消耗记录列表', 'defaultCheck' => false),

            '1706'   => array('id' => 1706, 'pId' => 17, 'name' => '工厂模式操作日志', 'defaultCheck' => false),
            '170601' => array('id' => 170601, 'pId' => 1706, 'name' => '查看工厂模式操作日志', 'defaultCheck' => false),
            '170602' => array('id' => 170602, 'pId' => 1706, 'name' => '导出工厂模式操作日志', 'defaultCheck' => false),

            '1707'   => array('id' => 1707, 'pId' => 17, 'name' => '工厂模式物料消耗设置', 'defaultCheck' => false),
            '170701' => array('id' => 170701, 'pId' => 1707, 'name' => '查看工厂模式物料消耗设置', 'defaultCheck' => false),
            '170702' => array('id' => 170702, 'pId' => 1707, 'name' => '添加工厂模式物料消耗设置', 'defaultCheck' => false),
            '170703' => array('id' => 170703, 'pId' => 1707, 'name' => '编辑工厂模式物料消耗设置', 'defaultCheck' => false),
            '170704' => array('id' => 170704, 'pId' => 1707, 'name' => '删除工厂模式物料消耗设置', 'defaultCheck' => false),

            '18'     => array('id' => 18, 'pId' => 0, 'name' => '营销工具', 'open' => true, 'defaultCheck' => false),

            '1801'   => array('id' => 1801, 'pId' => 18, 'name' => '营销游戏管理', 'defaultCheck' => false),

            '180101' => array('id' => 180101, 'pId' => 1801, 'name' => '营销游戏添加', 'defaultCheck' => false),
            '180102' => array('id' => 180102, 'pId' => 1801, 'name' => '营销游戏编辑', 'defaultCheck' => false),
            '180103' => array('id' => 180103, 'pId' => 1801, 'name' => '营销游戏删除', 'defaultCheck' => false),
            '180104' => array('id' => 180104, 'pId' => 1801, 'name' => '中奖信息查看', 'defaultCheck' => false),
            '180105' => array('id' => 180105, 'pId' => 1801, 'name' => '营销游戏复制', 'defaultCheck' => false),
            '180106' => array('id' => 180106, 'pId' => 1801, 'name' => '营销游戏查看', 'defaultCheck' => false),

            // 提示语权限管理
            '1802'   => array('id' => 1802, 'pId' => 18, 'name' => '活动提示语信息管理', 'defaultCheck' => false),

            '180201' => array('id' => 180201, 'pId' => 1802, 'name' => '活动提示语信息添加', 'defaultCheck' => false),
            '180203' => array('id' => 180203, 'pId' => 1802, 'name' => '活动提示语信息编辑', 'defaultCheck' => false),
            '180204' => array('id' => 180204, 'pId' => 1802, 'name' => '活动提示语信息删除', 'defaultCheck' => false),
            '180205' => array('id' => 180205, 'pId' => 1802, 'name' => '活动提示语信息查看', 'defaultCheck' => false),

            // 参与活动记录管理
            '1803'   => array('id' => 1803, 'pId' => 18, 'name' => '参与活动记录管理', 'defaultCheck' => false),

            '1804'   => array('id' => 1804, 'pId' => 18, 'name' => '拉新活动设置', 'defaultCheck' => false),

            '180401' => array('id' => 180401, 'pId' => 1804, 'name' => '编辑拉新活动', 'defaultCheck' => false),
            '180403' => array('id' => 180403, 'pId' => 1804, 'name' => '查看拉新活动', 'defaultCheck' => false),

            '1805'   => array('id' => 1805, 'pId' => 18, 'name' => '拉新活动奖励列表', 'defaultCheck' => false),

            '1806'   => array('id' => 1806, 'pId' => 18, 'name' => '拉新活动绑定用户列表', 'defaultCheck' => false),

            '1807'   => array('id' => 1807, 'pId' => 18, 'name' => '自组合用户发货管理', 'defaultCheck' => false),
            '180701' => array('id' => 180701, 'pId' => 1807, 'name' => '自组合用户发货', 'defaultCheck' => false),

            // 自组合套餐
            '1808'   => array('id' => 1808, 'pId' => 18, 'name' => '自组合套餐活动管理', 'defaultCheck' => false),

            '180801' => array('id' => 180801, 'pId' => 1808, 'name' => '自组合套餐活动添加', 'defaultCheck' => false),
            '180802' => array('id' => 180802, 'pId' => 1808, 'name' => '自组合套餐活动编辑', 'defaultCheck' => false),
            '180803' => array('id' => 180803, 'pId' => 1808, 'name' => '自组合套餐活动查看', 'defaultCheck' => false),

            // 拼团活动
            '1809'   => array('id' => 1809, 'pId' => 18, 'name' => '拼团活动管理', 'defaultCheck' => false),

            '180901' => array('id' => 180901, 'pId' => 1809, 'name' => '拼团活动列表展示', 'defaultCheck' => false),
            '180902' => array('id' => 180902, 'pId' => 1809, 'name' => '拼团活动添加/编辑', 'defaultCheck' => false),
            '180903' => array('id' => 180903, 'pId' => 1809, 'name' => '拼团活动设置展示', 'defaultCheck' => false),
            '180904' => array('id' => 180904, 'pId' => 1809, 'name' => '拼团活动统计', 'defaultCheck' => false),
            '180905' => array('id' => 180905, 'pId' => 1809, 'name' => '拼团活动设置修改', 'defaultCheck' => false),
            '180906' => array('id' => 180906, 'pId' => 1809, 'name' => '拼团活动线上排序', 'defaultCheck' => false),
            '180907' => array('id' => 180907, 'pId' => 1809, 'name' => '拼团活动客服查询', 'defaultCheck' => false),
            // 红包分享统计
            '1810'   => array('id' => 1810, 'pId' => 18, 'name' => '分享红包统计管理', 'defaultCheck' => false),
            // 领券活动
            '1811'   => array('id' => 1811, 'pId' => 18, 'name' => '领券活动', 'defaultCheck' => false),
            '181101' => array('id' => 181101, 'pId' => 1811, 'name' => '查看领券活动', 'defaultCheck' => false),
            '181102' => array('id' => 181102, 'pId' => 1811, 'name' => '编辑领券活动', 'defaultCheck' => false),
            '181103' => array('id' => 181103, 'pId' => 1811, 'name' => '添加领券活动', 'defaultCheck' => false),

            '19'     => array('id' => 19, 'pId' => 0, 'name' => '发券系统', 'open' => true, 'defaultCheck' => false),
            '1901'   => array('id' => 1901, 'pId' => 19, 'name' => '黑白名单管理', 'defaultCheck' => false),
            '190101' => array('id' => 190101, 'pId' => 1901, 'name' => '查看黑白名单', 'defaultCheck' => false),
            '190102' => array('id' => 190102, 'pId' => 1901, 'name' => '编辑黑白名单', 'defaultCheck' => false),
            '190103' => array('id' => 190103, 'pId' => 1901, 'name' => '删除黑白名单', 'defaultCheck' => false),
            '190104' => array('id' => 190104, 'pId' => 1901, 'name' => '添加黑白名单', 'defaultCheck' => false),

            '1902'   => array('id' => 1902, 'pId' => 19, 'name' => '快速发券', 'defaultCheck' => false),
            '190201' => array('id' => 190201, 'pId' => 1902, 'name' => '快速发券添加', 'defaultCheck' => false),
            '190202' => array('id' => 190202, 'pId' => 1902, 'name' => '快速发券列表', 'defaultCheck' => false),

            // 发券管理
            '1903'   => array('id' => 1903, 'pId' => 19, 'name' => '发券管理', 'defaultCheck' => false),
            '190301' => array('id' => 190301, 'pId' => 1903, 'name' => '添加发券任务', 'defaultCheck' => false),
            '190302' => array('id' => 190302, 'pId' => 1903, 'name' => '查看发券任务', 'defaultCheck' => false),
            '190303' => array('id' => 190303, 'pId' => 1903, 'name' => '发券任务', 'defaultCheck' => false),
            '190304' => array('id' => 190304, 'pId' => 1903, 'name' => '编辑发券任务', 'defaultCheck' => false),
            '190307' => array('id' => 190307, 'pId' => 1903, 'name' => '审核发券任务', 'defaultCheck' => false),

            // 用户筛选任务管理
            '1904'   => array('id' => 1904, 'pId' => 19, 'name' => '用户筛选任务管理', 'defaultCheck' => false),
            '190401' => array('id' => 190401, 'pId' => 1904, 'name' => '用户筛选任务添加', 'defaultCheck' => false),
            '190402' => array('id' => 190402, 'pId' => 1904, 'name' => '用户筛选任务编辑', 'defaultCheck' => false),
            '190403' => array('id' => 190403, 'pId' => 1904, 'name' => '用户筛选任务查看', 'defaultCheck' => false),
            '190404' => array('id' => 190404, 'pId' => 1904, 'name' => '用户筛选任务删除', 'defaultCheck' => false),
            '190405' => array('id' => 190405, 'pId' => 1904, 'name' => '用户筛选号码导出', 'defaultCheck' => false),

            // 发券任务统计
            '1905'   => array('id' => 1905, 'pId' => 19, 'name' => '发券任务统计', 'defaultCheck' => false),
            '190501' => array('id' => 190501, 'pId' => 1905, 'name' => '发券任务统计查看', 'defaultCheck' => false),
            '190502' => array('id' => 190502, 'pId' => 1905, 'name' => '发券任务统计导出', 'defaultCheck' => false),
            //  日报报表
            '20'     => array('id' => 20, 'pId' => 0, 'name' => '日报报表', 'open' => true, 'defaultCheck' => false),
            '2000'   => array('id' => 2000, 'pId' => 20, 'name' => '渠道日报', 'defaultCheck' => false),
            '200001' => array('id' => 200001, 'pId' => 2000, 'name' => '渠道日报导出', 'defaultCheck' => false),
            '200002' => array('id' => 200002, 'pId' => 2000, 'name' => '渠道日报检索', 'defaultCheck' => false),
            '200003' => array('id' => 200003, 'pId' => 2000, 'name' => '渠道日报查看', 'defaultCheck' => false),
            '2001'   => array('id' => 2001, 'pId' => 20, 'name' => '日报总表', 'defaultCheck' => false),
            '200101' => array('id' => 200101, 'pId' => 2001, 'name' => '日报总表导出', 'defaultCheck' => false),
            '200102' => array('id' => 200102, 'pId' => 2001, 'name' => '日报总表检索', 'defaultCheck' => false),
            '200103' => array('id' => 200103, 'pId' => 2001, 'name' => '日报总表查看', 'defaultCheck' => false),

            //  周报报表
            '21'     => array('id' => 21, 'pId' => 0, 'name' => '周报报表', 'open' => true, 'defaultCheck' => false),
            '2100'   => array('id' => 2100, 'pId' => 21, 'name' => '周报营收数据', 'defaultCheck' => false),
            '210001' => array('id' => 210001, 'pId' => 2100, 'name' => '周报营收数据导出', 'defaultCheck' => false),
            '210002' => array('id' => 210002, 'pId' => 2100, 'name' => '周报营收数据检索', 'defaultCheck' => false),
            '210003' => array('id' => 210003, 'pId' => 2100, 'name' => '周报营收数据查看', 'defaultCheck' => false),
            '2101'   => array('id' => 2101, 'pId' => 21, 'name' => '周报用户数据', 'defaultCheck' => false),
            '210101' => array('id' => 210101, 'pId' => 2101, 'name' => '周报用户数据检索', 'defaultCheck' => false),
            '210102' => array('id' => 210102, 'pId' => 2101, 'name' => '周报用户数据导出', 'defaultCheck' => false),
            '210103' => array('id' => 210103, 'pId' => 2101, 'name' => '周报用户数据查看', 'defaultCheck' => false),
            '2102'   => array('id' => 2102, 'pId' => 21, 'name' => '周报复购数据', 'defaultCheck' => false),
            '210201' => array('id' => 210201, 'pId' => 2102, 'name' => '周报复购数据导出', 'defaultCheck' => false),
            '210202' => array('id' => 210202, 'pId' => 2102, 'name' => '周报复购数据检索', 'defaultCheck' => false),
            '210203' => array('id' => 210203, 'pId' => 2102, 'name' => '周报复购数据查看', 'defaultCheck' => false),

            //  月报报表
            '22'     => array('id' => 22, 'pId' => 0, 'name' => '月报报表', 'open' => true, 'defaultCheck' => false),
            '2200'   => array('id' => 2200, 'pId' => 22, 'name' => '月报营收数据', 'defaultCheck' => false),
            '220001' => array('id' => 220001, 'pId' => 2200, 'name' => '月报营收数据导出', 'defaultCheck' => false),
            '220002' => array('id' => 220002, 'pId' => 2200, 'name' => '月报营收数据检索', 'defaultCheck' => false),
            '220003' => array('id' => 220003, 'pId' => 2200, 'name' => '月报营收数据查看', 'defaultCheck' => false),
            '2201'   => array('id' => 2201, 'pId' => 22, 'name' => '月报用户数据', 'defaultCheck' => false),
            '220101' => array('id' => 220101, 'pId' => 2201, 'name' => '月报用户数据检索', 'defaultCheck' => false),
            '220102' => array('id' => 220102, 'pId' => 2201, 'name' => '月报用户数据导出', 'defaultCheck' => false),
            '220103' => array('id' => 220103, 'pId' => 2201, 'name' => '月报用户数据查看', 'defaultCheck' => false),

            //  外卖
            '23'     => array('id' => 23, 'pId' => 0, 'name' => '外卖管理', 'open' => true, 'defaultCheck' => false),
            '2300'   => array('id' => 2300, 'pId' => 23, 'name' => '外卖日报', 'defaultCheck' => false),

            '2301'   => array('id' => 2301, 'pId' => 23, 'name' => '外卖订单', 'defaultCheck' => false),
            '230101' => array('id' => 230101, 'pId' => 2301, 'name' => '取消外卖订单', 'defaultCheck' => false),
            '230102' => array('id' => 230102, 'pId' => 2301, 'name' => '查看外卖订单详情', 'defaultCheck' => false),
            '230103' => array('id' => 230103, 'pId' => 2301, 'name' => '查看外卖订单', 'defaultCheck' => false),
            '230104' => array('id' => 230104, 'pId' => 2301, 'name' => '转移外卖订单', 'defaultCheck' => false),
            '230105' => array('id' => 230105, 'pId' => 2301, 'name' => '导出外卖订单', 'defaultCheck' => false),
            '2302'   => array('id' => 2302, 'pId' => 23, 'name' => '配送人员管理', 'defaultCheck' => false),
            '230201' => array('id' => 230201, 'pId' => 2302, 'name' => '新增配送人员', 'defaultCheck' => false),
            '230202' => array('id' => 230202, 'pId' => 2302, 'name' => '编辑配送人员', 'defaultCheck' => false),
            '230203' => array('id' => 230203, 'pId' => 2302, 'name' => '删除配送人员', 'defaultCheck' => false),
            '230204' => array('id' => 230204, 'pId' => 2302, 'name' => '查看配送人员', 'defaultCheck' => false),
            '2303'   => array('id' => 2303, 'pId' => 23, 'name' => '配送区域管理', 'defaultCheck' => false),
            '230301' => array('id' => 230301, 'pId' => 2303, 'name' => '新增配送区域', 'defaultCheck' => false),
            '230302' => array('id' => 230302, 'pId' => 2303, 'name' => '编辑配送区域', 'defaultCheck' => false),
            '230303' => array('id' => 230303, 'pId' => 2303, 'name' => '删除配送区域', 'defaultCheck' => false),
            '230304' => array('id' => 230304, 'pId' => 2303, 'name' => '查看配送区域', 'defaultCheck' => false),
            // 客服系统
            '24'     => array('id' => 24, 'pId' => 0, 'name' => '客服系统', 'open' => true, 'defaultCheck' => false),
            '2400'   => array('id' => 2400, 'pId' => 24, 'name' => '电话搜索', 'defaultCheck' => false),
            '2401'   => array('id' => 2401, 'pId' => 24, 'name' => '点位搜索', 'defaultCheck' => false),
            '2402'   => array('id' => 2402, 'pId' => 24, 'name' => '客诉记录', 'defaultCheck' => false),
            '240201' => array('id' => 240201, 'pId' => 2402, 'name' => '客诉记录导出', 'defaultCheck' => false),

            '25'     => array('id' => 25, 'pId' => 0, 'name' => '客服设置', 'open' => true, 'defaultCheck' => false),
            '2500'   => array('id' => 2500, 'pId' => 25, 'name' => '咨询类型设置', 'defaultCheck' => false),
            '2501'   => array('id' => 2501, 'pId' => 25, 'name' => '问题类型设置', 'defaultCheck' => false),
            '2502'   => array('id' => 2502, 'pId' => 25, 'name' => '协商方案设置', 'defaultCheck' => false),

        );
    }

    /**
     * 获取权限树字串
     * @return string
     */
    public function getRightsString()
    {
        $rightsList   = $this->getRightsArray();
        $rightStrings = "";
        foreach ($rightsList as $rights) {
            $open  = isset($rights['open']) ? "open:true," : '';
            $check = isset($rights['defaultCheck']) && $rights['defaultCheck'] ? "checked:true," : '';
            $rightStrings .= "{ id:{$rights['id']}, pId:{$rights['pId']}, name:'" . $rights['name'] . "',"
                . $open
                . $check
                . "},";
        }
        //echo $rightStrings;exit;
        return $rightStrings;
    }

    /**
     * 获取已有权限树字串
     * @return string
     */
    public function getExistRightsString()
    {
        $rightsList = $this->getRightsArray();
        $auth       = Yii::$app->authManager;

        $permissonList = $auth->getPermissionsByRole($this->name); //已有权限
        $rightStrings  = "";
        foreach ($rightsList as $rights) {
            $open  = isset($rights['open']) ? "open:true," : '';
            $check = array_key_exists($rights['name'], $permissonList) ? "checked:true," : '';
            $rightStrings .= "{ id:{$rights['id']}, pId:{$rights['pId']}, name:'" . $rights['name'] . "',"
                . $open
                . $check
                . "},";
        }
        //echo $rightStrings;exit;
        return $rightStrings;
    }

    /**
     *权限初始化
     */
    public function init()
    {
        $rightsList = $this->getRightsArray();

        $auth = Yii::$app->authManager;
        foreach ($rightsList as $rights) {
            $rightsName = $rights['name'];

            $rightsExist = $auth->getPermission($rightsName);
            if (!$rightsExist) {
                $rightsPermission = $auth->createPermission($rightsName);
                $auth->add($rightsPermission);
            }
        }
    }
    public function saveDecription($rightsList)
    {
        $rightsArray  = $this->getRightsArray();
        $selectRights = explode("|", $rightsList);
        $description  = '';
        foreach ($selectRights as $rightID) {
            $rightValue = $rightsArray[$rightID]['name'];
            $description .= $rightValue . '：';
        }
        $this->description = $description;
    }

    /**
     * 创建用户权限
     * @param string $rightsList 用户选中的权限ID字符串
     */
    public function createRights($rightsList)
    {
        $rightsArray  = $this->getRightsArray();
        $selectRights = explode("|", $rightsList);
        $auth         = Yii::$app->authManager;

        foreach ($selectRights as $rightID) {
            $rightValue       = $rightsArray[$rightID]['name'];
            $rightsPermission = $auth->getPermission($rightValue);
            $role             = $auth->getRole($this->name);
            $auth->addChild($role, $rightsPermission);
        }
    }

    /**
     * 更新角色权限
     * @param string $rightsList
     */
    public function updateRights($rightsList)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($this->name);
        $auth->removeChildren($role);
        $this->createRights($rightsList);
    }

    /**
     * 获取角色数组
     * @param Manager 管理员
     * @return array 角色数组
     */
    public static function getRoleArray($manager)
    {
        $roleList  = self::find()->select('name')->where(['type' => 1])->asArray()->all();
        $roleArray = array();
        if ($manager->role != self::SUPER_MASTER) {
            foreach ($roleList as $role) {
                if ($role['name'] == self::SUPER_MASTER) {
                    continue;
                }

                $roleArray[$role['name']] = $role['name'];
            }
        } else {

            $roleArray[$manager->role] = $manager->role;

        }
        return $roleArray;
    }
}
