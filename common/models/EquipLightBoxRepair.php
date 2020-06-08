<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equip_light_box_repair".
 *
 * @property integer $id
 * @property integer $build_id
 * @property integer $supplier_id
 * @property string $remark
 * @property integer $process_result
 * @property integer $process_time
 * @property integer $create_time
 *
 * @property Building $build
 */
class EquipLightBoxRepair extends \yii\db\ActiveRecord
{
    /** 处理结果常量 */
    const PROCESS_NO = 1;   // 未处理
    const PROCESS_LOOK = 2; // 已查看
    const PROCESS_BACK = 3; // 拉回工厂
    const PROCESS_ACCEPTANCE = 4; // 验收中
    const PROCESS_ACCEPTANCE_FAIL = 5; // 验收未通过
    const PROCESS_SUCCESS = 8; // 维修成功
    const PROCESS_ACCEPTANCE_SUCCESS = 9; // 验收通过

    public static $process_result = [
        self::PROCESS_NO => '未处理',
        self::PROCESS_LOOK => '已查看',
        self::PROCESS_BACK => '拉回工厂',
        self::PROCESS_ACCEPTANCE => '验收中',
        self::PROCESS_ACCEPTANCE_FAIL => '验收未通过',
        self::PROCESS_SUCCESS => '维修成功',
        self::PROCESS_ACCEPTANCE_SUCCESS => '验收通过'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_light_box_repair';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_id'], 'required'],
            [['build_id', 'process_result', 'process_time', 'create_time', 'equip_id'], 'integer'],
            [['remark'], 'string', 'max' => 500],
            [['supplier_id'], 'string', 'max'=>64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'build_id' => '楼宇名称',
            'supplier_id' => '选择厂商',
            'remark' => '备注',
            'process_result' => '维修结果',
            'process_time' => '维修时间',
            'create_time' => '任务创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['build_id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWxMember()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'supplier_id']);
    }

    /**
     * 获取灯箱维修任务列表（手机端使用）
     * @param  string  $field 要查询的字段
     * @param  array   $where 查询条件
     * @param  integer $limit 分页大小
     * @param  integer $page  当前页
     * @return array         灯箱列表
     */
    public static function getList($field="*",$where=[],$limit=10,$page=0)
    {
        return self::find()->select($field)->where($where)->offset($page*$limit)->limit($limit)->orderby('id desc')->all();
    }
    
    /**
     * 获取灯箱维修认为详情
     * @param  string $field 要查询的字段
     * @param  array  $where 查询条件
     * @return [type]        [description]
     */
    public static function getDetail($field="*",$where=[])
    {
        return self::find()->select($field)->where($where)->one();
    }

    /**
     * 获取符合查询条件的数据条数
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getCount($where=[])
    {
        return self::find()->where($where)->count();
    }
}
