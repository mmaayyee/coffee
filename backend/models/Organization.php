<?php

namespace backend\models;

use backend\models\ScmWarehouse;
use common\helpers\Tools;
use common\models\AgentsApi;
use common\models\Api;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "organization".
 *
 * @property integer $org_id
 * @property string $org_name
 * @property integer $parent_id
 * @property string $parentPath
 * @property string $org_pass
 */
class Organization extends \yii\db\ActiveRecord
{
    //机构ID
    public $org_id;
    //机构名称
    public $org_name;
    //机构父ID
    public $parent_id;
    //机构路径
    public $parent_path;
    //机构设备密码
    public $org_pass;
    //机构类型 0公司直属，1合作代理， 2合作商
    public $organization_type;
    //代理商编号e
    public $org_number;
    //城市
    public $org_city;

    public $is_replace_maintain;

    public $isNewRecord;

    const HEAD_OFFICE = 1; //北京总部

    /** 机构类型常量 */
    const TYPE_ORG         = 0; // 分公司
    const TYPE_AGENTS      = 1; // 代理加盟
    const TYPE_CHANNEL     = 2; // 商业发展
    const TYPE_JOIN        = 3; // 求包养
    const TYPE_ENTRUST_PUT = 4; // 代理委托投放
    const TYPE_INTERVENING = 5; // 居间
    const TYPE_PARTNER     = 6; // 联盈方

    /** 是否代维护 */
    const INSTEAD_NO  = 1; // 自维护
    const INSTEAD_YES = 2; // 代维护
    /** @var array 机构类型 */
    public static $organizationType = [
        ''                     => '请选择',
        self::TYPE_ORG         => '分公司',
        self::TYPE_AGENTS      => '代理加盟',
        self::TYPE_CHANNEL     => '商业发展',
        self::TYPE_JOIN        => '求包养',
        self::TYPE_ENTRUST_PUT => '代理委托投放',
        self::TYPE_INTERVENING => '居间服务',
        self::TYPE_PARTNER     => '联营方',
    ];

    /**
     * 是否代维护数组
     * @var [type]
     */
    public $instead = [
        ''                => '请选择',
        self::INSTEAD_NO  => '否',
        self::INSTEAD_YES => '是',
    ];

    /**
     * @inheritdoc
     */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_name', 'parent_id', 'org_city', 'organization_type', 'is_replace_maintain'], 'required'],
            [['parent_id'], 'integer', 'min' => 1],
            ['organization_type', 'integer'],
            [['org_name', 'org_city'], 'string', 'max' => 255],
            [['parent_path', 'org_pass'], 'string', 'max' => 50],
            [['org_id', 'org_number', 'is_replace_maintain'], 'safe'],
            ['org_name', "requiredByASpecialCreate", 'on' => 'create'],
            ['org_name', "requiredByASpecialUpdate", 'on' => 'update'],
        ];
    }

    /**
     * 获取是否代维护名称
     * @author  zgw
     * @version 2017-11-28
     * @return  [type]     [description]
     */
    public function getInstead()
    {
        return !empty($this->instead[$this->is_replace_maintain]) ? $this->instead[$this->is_replace_maintain] : '';
    }

    /**
     *  自定义验证org_name
     */
    public function requiredByASpecialCreate($attribute, $params)
    {
        $params = array('org_name' => $this->org_name);

        if (Api::verifyOrgCreate($params)) {
            $this->addError($attribute, "机构名称已存在");
        }

    }
    /**
     *  自定义修改验证org_name
     */
    public function requiredByASpecialUpdate($attribute, $params)
    {
        $params = array('org_name' => $this->org_name, 'org_id' => $this->org_id);
        if (Api::verifyOrgUpdate($params)) {
            $this->addError($attribute, "机构名称已存在");
        }

    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'org_id'              => '机构ID',
            'org_name'            => '机构名称',
            'parent_id'           => '上级名称',
            'parent_path'         => '父级路径',
            'organization_type'   => '机构类型',
            'province'            => '省份',
            'org_city'            => '城市',
            'org_type'            => '机构类型',
            'is_replace_maintain' => '是否代维护',
        ];
    }

    /**
     * 获取organization分级
     * @author  zmy
     * @version 2017-10-20
     * @return  [type]     [description]
     */
    public static function getOrgRange()
    {
        return ['全部', '当前分公司', '下级代理商'];
    }
    /**
     * 获取分公司列表,格式 分公司ID=>分公司名称
     * @author wangxiwen
     * @version 2018-06-13
     * @return array
     */
    public static function getOrganizationList()
    {
        $organizationArr = Api::getBase('get-org-name');
        if (!$organizationArr) {
            return [];
        }
        $organizationList = Json::decode($organizationArr);
        return Tools::map($organizationList, 'org_id', 'org_name', null, null);
    }

    /**
     * 获取分公司
     * @return array 分公司数据
     */
    public static function getBranchArray($type = 1)
    {
        $allArray = [];
        $parentId = Manager::getManagerBranchID();
        $allArray = Api::getOrgIdNameArray(array('parent_path' => $parentId));
        if ($type == 1) {
            $allArray = array('' => '请选择') + $allArray;
        }
        return $allArray;
    }

    /**
     * 获取登录用户权限路径
     * @param int $orgID 机构ID
     */
    public static function getManagerBranchPath($orgID = '')
    {
        $orgID = $orgID ? $orgID : Manager::getManagerBranchID();
        if (($model = self::findModel($orgID)) !== null) {
            return $model->parent_path;
        } else {
            return false;
        }

    }
    /**
     * 获取分公司的数组
     * @author  zmy
     * @update  tuqiang
     * @version 2017-09-29
     * @param   [type]     $orgID [分公司ID]
     * @return  [type]            [分公司数组]
     */
    public static function getParentPathArr($orgID)
    {
        return Api::getOrgIdArray(array('parent_path' => $orgID));
    }

    /**
     * 获取分公司信息列表
     * @update tuqiang
     * @param  string $field 要查询的字段
     * @param  array  $where 查询条件
     * @return array        返回结果
     */
    public static function getOrgNameList($field = '*', $where = [])
    {
        return Api::getOrgIdNameListReturnErp(array('select' => $field, 'where' => $where));
    }

    /**
     * 获取分公司信息列表One
     * @update tuqiang
     * @param  string $field 要查询的字段
     * @param  array  $where 查询条件
     * @return array        返回结果
     */
    public static function getOrgName($field = '*', $where = [])
    {
        return Api::getOrgNameReturnErp(array('select' => $field, 'where' => $where));
    }

    /**
     * 根据条件查询分公司id和分公司名称列表
     * @update tuqiang
     * @param  arra $where 查询条件
     * @return [type]        [description]
     */
    public static function getOrgIdNameArr($where = [], $type = 1)
    {
        return Tools::map(self::getOrgNameList('org_id, org_name', $where), 'org_id', 'org_name', null, $type);
    }

    /**
     * 根据分公司id获取分公司名称
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public static function getOrgNameArr($ids)
    {
        return Api::getOrgNameList(['org_id' => $ids]);
    }

    /**
     * 获取指定分公司所在城市
     * @update tuqiang
     * @param  [type] $org_id [description]
     * @return [type]         [description]
     */
    public static function getOrgCity($org_id)
    {
        $orgModel = self::findModel($org_id);
        return $orgModel ? $orgModel->org_city : '';
    }

    /**
     * 获取机构名称
     * @update tuqiang
     * @param int $org_id
     * @return string
     */
    public static function getOrgNameByID($org_id = 0)
    {
        $orgModel = self::findModel($org_id);
        return $orgModel ? $orgModel->org_name : '';
    }

    /**
     * 获取指定字段数据
     * @update tuqiang
     * @param  array   $where 查询条件
     * @param  string  $field 查询字段以逗号拼接。
     * @return array
     */
    public static function getField($field, $where)
    {
        $orgModel = self::findModelByWhereArray($where);
        return $orgModel ? $orgModel->$field : '';
    }

    /**
     * 获取当前登录用户所在的分公司列表
     * @author  zgw
     * @update tuqiang
     * @version 2016-11-05
     * @param   array      $where [description]
     * @return  [type]            [description]
     */
    public static function getManagerOrgIdNameArr($where = [])
    {
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {
            $orgID = Api::getOrgIdArray(['parent_path' => $orgID, 'is_replace_maintain' => self::INSTEAD_YES]);
            return self::getOrgIdNameArr(['org_id' => $orgID]);
        } else {
            return self::getOrgIdNameArr($where);
        }
    }

    /**
     * 获取所有分公司名称
     * @author  zgw
     * @update  tuqiang
     * @version 2016-11-05
     * @return  [type]     [description]
     */
    public static function getAllOrgName()
    {
        $userId       = Yii::$app->user->identity->id;
        $managerModel = Manager::find()->where(['id' => $userId])->one();

        if ($managerModel->branch != 1) {
            //  分公司
            $list = Api::getOrgNameList(['org_id' => $managerModel->branch]);
        } else {
            $list = Api::getOrgNameList();
        }
        return $list;
    }

    /**
     *  添加代理商仓库 来源代理商系统
     *  @param
     *  @author     zmy
     *  @update     tuqinag
     *  @version    2017-10-11
     */
    public static function addOrg($orgInfo, $transaction)
    {
        // 添加代理商厂库
        $warehouseData = [
            'name'            => $orgInfo->org_name,
            'use'             => ScmWarehouse::EQUIP_USE,
            'organization_id' => $orgInfo->org_id,
            'address'         => $orgInfo->org_name,
            'ctime'           => time(),
        ];
        $wareHouseModel = new ScmWarehouse();
        if ($wareHouseModel->addAgentsWarehouse($warehouseData)) {
            return true;
        } else {
            $transaction->rollBack();
            AgentsApi::returnData(1, '同步代理商仓库信息错误');
        }

    }

    /**
     * 修改代理商信息
     * @author  zgw
     * @version 2016-11-15
     * @param   [type]     $organizationModel [description]
     * @param   [type]     $transaction       [description]
     * @return  [type]                        [description]
     */
    public static function updateOrg($orgInfo, $transaction)
    {
        $scmWarehouseModel          = ScmWarehouse::findOne(['organization_id' => $orgInfo->org_id]);
        $scmWarehouseModel->address = $orgInfo->org_name;
        $scmWarehouseModel->name    = $orgInfo->org_name;
        if (!$scmWarehouseModel->save()) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '同步代理商仓库信息错误');
        }

    }

    /**
     *  添加代理商接口方法
     *  @param zmy
     */
    public static function createOrganization($data)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        // 获取上级代理商信息
        $agentsInfo = self::findModel($data['org_id']);
        $parentObj  = self::getOrgName('*', ['org_id' => $agentsInfo->parent_id]);
        // 验证上级代理商是否存在
        if (!$parentObj) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '上级代理商不存在');
        }
        // 存在修改不存在新增
        if (ScmWarehouse::findOne(['organization_id' => $data['org_id']])) {
            // 更新代理商仓库
            self::updateOrg($agentsInfo, $transaction);
        } else {
            self::addOrg($agentsInfo, $transaction);
        }
        //事务通过
        $transaction->commit();
        AgentsApi::returnData(0, '同步成功');
    }

    /**
     * 删除代理商仓库
     * @author  zgw
     * @version 2016-11-17
     * @param   string     $agentsNumber 代理商编号
     * @return  json
     */
    public static function delAgents($orgId)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        // 删除代理商仓库
        $agentsWarehoseRes = ScmWarehouse::delAgentsWarehouse($orgId);
        if ($agentsWarehoseRes === false) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '删除代理商仓库失败');
        }
        $transaction->commit();
        AgentsApi::returnData();
    }
    /**
     * 根据id查询model详情
     * @param  array   $where   查询条件
     * @return array            返回机构详情对象
     */
    public static function findModel($id)
    {
        $model     = new Organization();
        $modelInfo = Api::getOrgDetailsModel(array('org_id' => $id));
        $model->load(['Organization' => $modelInfo]);
        return $modelInfo ? $model : $modelInfo;
    }
    /**
     * 根据数组条件进行查询单条model详情
     * @param  array   $where   查询条件
     * @return array            返回机构详情对象
     */
    public static function findModelByWhereArray($where)
    {
        $model = Api::getOrgDetailsModel($where);
        return $model ? (object) $model : $model;
    }
    /**
     * 同步添加到智能平台
     * @author tuqiang
     * @version 2017-10-09
     * @param  array   $data        添加详情
     * @return boolean true/false   添加成功/添加失败
     */
    public function syncErpAddOrg($params)
    {
        return Api::syncErpAddOrg($params);
    }
    /**
     * 同步添加到智能平台
     * @author tuqiang
     * @version 2017-10-09
     * @param  array   $data        添加详情
     * @return boolean true/false   添加成功/添加失败
     */
    public function syncErpUpdateOrg($params)
    {
        Api::syncErpUpdateOrg($params);
        return true;
    }
    /**
     * 根据条件获得机构id列表
     * @author   tuqiang
     * @version  2017-10-10
     * @param    array      $where  查询条件
     * @return   array      $data   返回数据
     */
    public static function getOrgByWhereIdList($params = array())
    {
        return Api::getOrgByWhereIdList($params);
    }

    /**
     * 获取所有机构数据
     * @author zhenggangwei
     * @date   2019-12-29
     * @return array
     */
    public static function getOrgList()
    {
        $orgList = Api::postBaseGetOrgData("get-org-list", []);
        if ($orgList) {
            return Json::decode($orgList);
        }
        return [];
    }
    /**
     * 获取所有机构ID对应机构类型名称
     * @author zhenggangwei
     * @date   2019-12-29
     * @return array
     */
    public static function getOrgIdTypeList()
    {
        $orgList     = self::getOrgList();
        $orgTypeList = [];
        foreach ($orgList as $org) {
            $orgTypeList[$org['org_id']] = self::$organizationType[$org['organization_type']] ?? '';
        }
        return $orgTypeList;
    }
}
