<?php

namespace backend\models;

use backend\models\Organization;
use common\models\Api;
use Yii;

/**
 * This is the model class for table "grind".
 *
 * @property integer $grind_id
 * @property integer $grind_type
 * @property integer $grind_time
 * @property integer $interval_time
 * @property string $org_id
 *
 * @property GrindEquipAssoc[] $grindEquipAssocs
 */
class Grind extends \yii\db\ActiveRecord
{
    public $grind_id;
    public $grind_type;
    public $grind_time;
    public $interval_time;
    public $org_id;
    public $equipTypeId;
    public $is_all;
    public $where_string;
    public $buildingList;
    public $buildName;
    public $equipmentCode;
    public $orgName;
    public $isNewRecord;
    public $grind_switch;
    public $grind_remark;
    public $grind_number;
    public $searchUpdateBuild = '';

    const ALL_TYPE   = 0; // 全国
    const ORG_TYPE   = 1; // 分公司
    const BUILD_TYPE = 2; // 楼宇

    const GRIND_SWITCH_OPEN  = 1; // 开启
    const CLOSE_SWITCH_CLOSE = 0; // 关闭

    //开关状态
    public static $switchType = ['' => '请选择', self::CLOSE_SWITCH_CLOSE => '关闭', self::GRIND_SWITCH_OPEN => '开启'];
    //设置范围
    public static $type = ['' => '请选择', self::ALL_TYPE => '全国', self::ORG_TYPE => '分公司', self::BUILD_TYPE => '楼宇'];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interval_time', 'grind_time', 'grind_switch'], 'required'],
            [['grind_type', 'grind_switch', 'grind_id', 'grind_time', 'org_id', 'interval_time'], 'integer'],
            [['equipTypeId', 'is_all', 'buildingList', 'orgName', 'equipmentCode', 'buildName', 'grind_number', 'grind_remark'], 'safe'],
            ['grind_time', 'compare', 'compareValue' => 5, 'operator' => '<=', 'message' => '预磨豆时间不能大于5'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grind_id'      => '磨豆id',
            'grind_type'    => '范围类型',
            'grind_time'    => '磨豆时间',
            'interval_time' => '间隔时间',
            'org_id'        => '分公司',
            'equipTypeId'   => '设备类型',
            'is_all'        => '是否全部',
            'orgName'       => '分公司',
            'grind_remark'  => '备注',
            'equipmentCode' => '设备编号',
            'buildName'     => '楼宇名称',
            'grind_switch'  => '是否开启',
            'grind_number'  => '楼宇数量',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrindEquipAssocs()
    {
        return $this->hasMany(GrindEquipAssoc::className(), ['grind_id' => 'grind_id']);
    }

    public static function findModel($id)
    {
        $info  = Api::getGrindInfo($id);
        $model = new Grind();
        if ($info) {
            $model->load(array('Grind' => $info));
            return $model;
        }

    }

    public static function getTypeName($type)
    {
        return isset(self::$type[$type]) ? self::$type[$type] : '';
    }
    public static function countTime($unixEndTime = 0)
    {
        //计算天数
        $timediff = $unixEndTime;
        $days     = intval($timediff / 1440);
        //计算小时数
        $remain = $timediff % 1440;
        $hours  = intval($remain / 60);
        //计算分钟数
        $mins = $remain % 60;
        return $days . '天' . $hours . '时' . $mins . '分';
    }
    public static function getGrindBuilding($model)
    {
        if ($model->grind_type == 0) {
            return '全部楼宇';
        } elseif ($model->grind_type == 1) {
            return Organization::getOrgNameByID($model->org_id) . "楼宇";
        } else {
            return '<a href=/grind/index-building?id=' . $model->grind_id . '>局部楼宇</a>';
        }
    }

    public static function getEquipTypeList()
    {
        $equipTypeList = Api::getEquipTypeList();
        if ($equipTypeList) {
            unset($equipTypeList['']);
            $equipTypeList += array(0 => '全选');
            return $equipTypeList;
        }
    }

    public static function getOrgIdNameArray()
    {
        $orgIdArr = Api::getOrgIdNameArray();
        if ($orgIdArr) {
            return $orgIdArr;
        }
    }

    public static function createGrindInfo($params)
    {
        return Api::createGrindInfo($params);
    }

    public static function updateGrindInfo($params)
    {
        return Api::updateGrindInfo($params);
    }

    public static function getOrgNameList()
    {
        $arr = Organization::getOrgIdNameArr();
        unset($arr[1]);
        return $arr;
    }

    public static function getGrindTypeList($grindtype)
    {
        $typeList = self::$type;
        if ($grindtype == 1) {
            return $typeList;
        } else {
            unset($typeList['']);
            return $typeList;
        }
    }

    /**
     * 获取预磨豆设置的楼宇数量
     * @return [type] [description]
     */
    public function getBuildingNumber()
    {
        if ($this->grind_type == self::ALL_TYPE) {
            return "全国全部楼宇";
        } else if ($this->grind_type == self::ORG_TYPE) {
            return "分公司全部楼宇";
        } else {
            return $this->grind_number;
        }
    }
}
