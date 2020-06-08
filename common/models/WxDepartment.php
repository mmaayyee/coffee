<?php

namespace common\models;

use backend\models\Manager;
use backend\models\Organization;
use Yii;

/**
 * This is the model class for table "wx_department".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentid
 * @property integer $sort
 * @property integer $ctime
 * @property integer $utime
 */
class WxDepartment extends \yii\db\ActiveRecord
{
    public $orgArr;
    /** 部门常量 **/
    // 设备部
    const EQUIP_DEPARTMENT = 1;
    // 配送部
    const DISTRIBUTION_DEPARTMENT = 2;
    // 投放商
    const TRAFFICKING_SUPPLIERS_DEPARTMENT = 3;
    // 供应商
    const SUPPLIERS_DEPARTMENT = 4;
    // 销售部
    const SALE_DEPARTMENT = 5;
    // 库管
    const KUGUAN = 6;
    // 客服部
    const CUSTOMER_SERVICE_DEPARTMENT = 7;
    // 总裁办
    const CHAIRMAN = 8;
    // 供应链
    const SUPPLYCHAIN = 9;
    //供水部
    const WATERMINISTRY = 10;
    //市场部
    const MARKET = 11;
    // 测试部
    const TEST = 12;
    // 运营部
    const OPERATIONS = 13;
    //产品部
    const PRODUCT = 14;

    public static $headquarter = [
        ''                                     => '请选择',
        self::EQUIP_DEPARTMENT                 => '设备部',
        self::DISTRIBUTION_DEPARTMENT          => '配送部',
        self::TRAFFICKING_SUPPLIERS_DEPARTMENT => '投放商',
        self::SUPPLIERS_DEPARTMENT             => '供应商',
        self::SALE_DEPARTMENT                  => '销售部',
        self::KUGUAN                           => '库管',
        self::CUSTOMER_SERVICE_DEPARTMENT      => '客服部',
        self::SUPPLYCHAIN                      => '供应链',
        self::WATERMINISTRY                    => '供水部',
        self::CHAIRMAN                         => '总裁办',
        self::MARKET                           => '市场部',
        self::TEST                             => '测试部',
        self::OPERATIONS                       => '运营部',
        self::PRODUCT                          => '产品部',
    ];

    /**
     * 各部门标识下面都有哪些职位
     * @var [type]
     */
    public static $partPostion = [
        self::EQUIP_DEPARTMENT                 => [
            WxMember::EQUIP_MANAGER     => '设备经理',
            WxMember::EQUIP_RESPONSIBLE => '设备主管',
            WxMember::EQUIP_MEMBER      => '设备人员',
        ],
        self::DISTRIBUTION_DEPARTMENT          => [
            WxMember::DISTRIBUTION_MANAGER     => '配送经理',
            WxMember::DISTRIBUTION_RESPONSIBLE => '配送主管',
            WxMember::DISTRIBUTION_MEMBER      => '配送人员',
        ],
        self::TRAFFICKING_SUPPLIERS_DEPARTMENT => [
            WxMember::TRAFFICKING_SUPPLIERS => '投放商',
        ],
        self::SALE_DEPARTMENT                  => [
            WxMember::SALE_ASSISTANT => '销售助理',
            WxMember::SALE_MEMBER    => '销售人员',
        ],
        self::KUGUAN                           => [
            WxMember::KUGUAN => '库管',
        ],
        self::CUSTOMER_SERVICE_DEPARTMENT      => [
            WxMember::CUSTOMER_SERVICE => '客服',
        ],
        self::SUPPLYCHAIN                      => [
            WxMember::SUPPLY_CHAIN_MANAGER => '供应链经理',
        ],
        self::WATERMINISTRY                    => [
            WxMember::WATERSUPPLIERS => '供水商',
        ],
        self::CHAIRMAN                         => [
            WxMember::CEO => 'CEO',
            WxMember::COO => 'COO',
        ],
        self::MARKET                           => [
            WxMember::MARKETING_DIRECTOR => '市场总监',
        ],
        self::TEST                             => [
            WxMember::TEST => '测试人员',
            WxMember::CTO  => 'CTO',
        ],
        self::OPERATIONS                       => [
            WxMember::OPERATION_DIRECTOR    => '运营总监',
            WxMember::OPERATION_MANAGER     => '运营经理',
            WxMember::OPERATION_RESPONSIBLE => '运营主管',
            WxMember::OPERATION_MEMBER      => '运营人员',
        ],
        self::PRODUCT                          => [
            WxMember::PRODUCT_DIRECTOR    => '产品总监',
            WxMember::PRODUCT_MANAGER     => '产品经理',
            WxMember::PRODUCT_RESPONSIBLE => '产品主管',
            WxMember::PRODUCT_MEMBER      => '产品人员',
        ],

    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'org_id'], 'required'],
            [['parentid', 'sort', 'org_id', 'headquarter'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['name'], 'unique'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => '部门编号',
            'name'        => '部门名称',
            'parentid'    => '上级部门',
            'sort'        => '在上级部门中的排序',
            'level'       => '部门等级',
            'org_id'      => '请选择分公司',
            'headquarter' => '部门标识',
        ];
    }

    /**
     * 获取部门列表
     * @param $type 类型 1-加请选择 2-不加请选择
     */
    public static function getDepartArray($type = 1)
    {
        $departlist = self::find()->select(['id', 'name'])->orderBy('id asc')->all();
        if ($type == 1) {
            $allArray = array(
                '' => '请选择',
            );
        }
        foreach ($departlist as $v) {
            $allArray[$v->id] = $v->name;
        }
        return $allArray;
    }

    /**
     * 根据部门id获取部门名称
     * @param $id 部门id
     */
    public static function getDepartName($id)
    {
        $deparname = self::find()->asArray()->select(['name'])->where(['id' => $id])->one();
        return $deparname ? $deparname['name'] : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * 获取部门列表
     * @param array $where 查询条件
     */
    public static function getDepartList($where = [])
    {
        $allArray   = [];
        $departlist = self::find()->select(['id', 'name'])->where($where)->orderBy('id asc')->all();
        foreach ($departlist as $v) {
            $allArray[$v->id] = $v->name;
        }
        return $allArray;
    }

    /**
     * 获取部门列表
     * @param  string $filed [description]
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getList($filed = '*', $where = [])
    {
        return self::find()->select($filed)->where($where)->all();
    }

    /**
     * 根据部门标识和分公司id返回部门id
     * @param  integer $headquarter [description]
     * @return [type]               [description]
     */
    public static function getDepartIds($headquarter = 0, $org_id = 0)
    {
        if (!$headquarter) {
            return [];
        }

        $where['headquarter'] = $headquarter;
        // 获取分公司id
        $org_id = $org_id ? $org_id : Manager::getManagerBranchID();
        if ($org_id > 1) {
            $where['org_id'] = $org_id;
        }

        $id_list = self::getList('id', $where);

        $id_arr = [];
        foreach ($id_list as $k => $v) {
            $id_arr[] = $v->id;
        }
        return $id_arr;
    }

    /**
     * 获取部门详情
     * @param  string $field 要查询的字段
     * @param  array  $where 查询条件
     * @return array
     */
    public static function getDepartDetail($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->one();
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-08-25
     * @param   string|int      $field 字段
     * @param   array           $where 查询条件
     * @return  string|int      查询结果
     */
    public static function getDepartId($field, $where)
    {
        $departDetail = self::getDepartDetail($field, $where);
        return $departDetail ? $departDetail->$field : '';
    }
}
