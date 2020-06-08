<?php

namespace backend\models;

use backend\models\Manager;
use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "scm_warehouse".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $use
 * @property string $ctime
 */
class ScmWarehouse extends \yii\db\ActiveRecord
{
    /** 物料用途常量定义 */
    const EQUIP_USE    = 1; //设备
    const MATERIAL_USE = 0; //物料
    public $orgArr;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'use', 'address', 'organization_id'], 'required'],
            [['ctime'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'name'            => '库名称',
            'address'         => '库地址',
            'use'             => '库用途',
            'ctime'           => '入库时间',
            'organization_id' => '分公司',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'organization_id']);
    }

    /**
     * 库信息类型数组
     * @return array
     **/
    public function wareHouseUse()
    {
        $wareHouseUse = array(
            ''                                     => '请选择',
            \backend\models\ScmSupplier::MATERIAL  => '物料',
            \backend\models\ScmSupplier::EQUIPMENT => '设备',

        );
        return $wareHouseUse;
    }

    /**
     * 获取库信息类型
     * @return 库信息类型
     */
    public function getWarehouseUse()
    {
        $wareHouseUse = $this->wareHouseUse();
        return $wareHouseUse[$this->use];
    }

    /**
     * 获取库名称 ID list
     * @param
     * @return array
     */
    public static function getWarehouseIdList($filed = "*", $where = array())
    {
        return self::find()->select($filed)->where($where)->asArray()->all();
    }

    /**
     * 获取库名称数组
     * @return array $wareArray
     */
    public static function getWarehouseNameArray($where = [])
    {
        $warehouses = ScmWarehouse::find()->select(['id', 'name'])->orderBy('id')->where($where)->asArray()->all();
        $wareArray  = array();
        foreach ($warehouses as $key => $value) {
            $wareArray[$value['id']] = $value['name'];
        }
        return $wareArray;
    }

    /**
     * 根据分公司获取库信息数组
     * @author  zgw
     * @version 2016-08-18
     * @return  array     库id和name的对应数组
     *
     */
    public static function getWarehouseIdNameArr($where = [])
    {
        // 获取分公司id
        $org_id = Manager::getManagerBranchID();
        // 查询条件
        if ($org_id > 1) {
            $where = ['and', ['organization_id' => $org_id], $where];
        }
        $idNameArr = Tools::map(self::find()->where($where)->all(), 'id', 'name');
        unset($idNameArr['']);
        return $idNameArr;
    }

    /**
     * 获取分库列表
     * @param  string $filed 要查寻的字段 如：'id,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        分库列表
     */
    public static function getWarehouseList($filed = "*", $where = array())
    {
        $arr   = [];
        $query = self::find()->select($filed);
        $query->andFilterWhere($where);
        // 获取当前用户的分公司id
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $query->andFilterWhere(['organization_id' => $managerOrgId]);
        }
        $organization = $query->asArray()->all();
        foreach ($organization as $key => $value) {
            $arr[$value['id']] = $value['name'];
        }
        return $arr;
    }

    /**
     * 获取指定字段
     * @author  zgw
     * @version 2016-11-15
     * @param   [type]     $field [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getField($field, $where)
    {
        $warehouseObj = self::find()->select($field)->where($where)->one();
        return $warehouseObj ? $warehouseObj->$field : '';
    }

    /**
     * 添加代理商仓库
     * @author  zgw
     * @version 2016-11-17
     * @param   [type]     $data [description]
     */
    public function addAgentsWarehouse($data)
    {
        // 验证代理商设备仓库是否存在，存在不做任何操作，不存在添加
        $agentWarehouseModel = self::find()->where(['organization_id' => $data['organization_id']])->one();
        if ($agentWarehouseModel) {
            return true;
        }
        return $this->load(['ScmWarehouse' => $data]) && $this->save();
    }

    /**
     * 删除代理商仓库
     * @author  zgw
     * @version 2016-11-17
     * @param   [type]     $organizationId [description]
     * @return  [type]                     [description]
     */
    public static function delAgentsWarehouse($organizationId)
    {
        $agentWareModel = self::find()->where(['organization_id' => $organizationId, 'use' => self::EQUIP_USE])->one();
        if ($agentWareModel && $agentWareModel->delete() === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取分公司下的分库
     * @author  zgw
     * @version 2017-02-28
     * @param   [type]     $orgId [description]
     * @return  [type]            [description]
     */
    public static function getOrgWarehouse($orgId)
    {
        $where = ['organization_id' => $orgId, 'use' => self::EQUIP_USE];
        return Tools::map(self::findAll($where), 'id', 'name', null, 2);
    }

    /**
     * 根据库ID获取库所在分公司
     * @author zhenggangwei
     * @date   2019-01-29
     * @param  integer     $id 库ID
     * @return integer         库所在分公司
     */
    public static function getOrgIdById($id)
    {
        $warehouse = self::findOne($id);
        return $warehouse ? $warehouse->organization_id : 1;
    }

}
