<?php

namespace backend\models;

use backend\models\Organization;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "scm_supplier".
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $username
 * @property string $tel
 * @property string $email
 * @property string $create_time
 *
 * @property Device[] $devices
 * @property ScmMaterial[] $scmMaterials
 */
class ScmSupplier extends \yii\db\ActiveRecord
{

    /**
     *供货类型
     **/

    /*物料*/
    const MATERIAL = 0;

    /*设备*/
    const EQUIPMENT = 1;

    /*供水*/
    const WATER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['realname', 'name', 'type', 'username', 'tel', 'create_time', 'supplier_code'], 'required'],
            [['create_time'], 'integer'],
            [['supplier_code'], 'match', 'pattern' => '/^[0-9]{1}[0-9]{1}$/', 'message' => '{attribute}只能输入2位数字'],
            [['email'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 6],
            [['realname'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 2],
            ['email', 'email'],
            [['username'], 'string', 'max' => 8],
            [['tel'], 'match', 'pattern' => '/^1[3|4|5|7|8]\d{9}$/', 'message' => '请输入正确的手机号'],
            [['realname', 'name', 'supplier_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => '供应商简称',
            'type'          => '供货类型',
            'username'      => '联系人姓名',
            'tel'           => '联系方式',
            'email'         => '邮箱',
            'create_time'   => '添加时间',
            'org_id'        => '分公司',
            'supplier_code' => '供应商编号',
            'realname'      => '供应商名称',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['supplier_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScmMaterials()
    {
        return $this->hasMany(ScmMaterial::className(), ['supplier_id' => 'id']);
    }

    /**
     * 供货类型数组
     * @return array
     **/
    public function supplyTypeArray()
    {
        $supplyTypeArray = array(
            ''              => '请选择',
            self::MATERIAL  => '物料',
            self::EQUIPMENT => '设备',
            self::WATER     => '供水',
        );
        return $supplyTypeArray;
    }

    /**
     * 获取供货类型
     * @return 供货类型
     **/
    public function getSupplyType()
    {
        $supplyTypeArray = $this->supplyTypeArray();
        return $supplyTypeArray[$this->type];
    }

    /**
     * 获取所有供应商
     * @return array
     **/
    public static function getSupplierArray($where = "")
    {

        $suppliers     = self::find()->select(['id', 'name'])->where($where)->asArray()->all();
        $supplierArray = array('' => '请选择');
        foreach ($suppliers as $supplier) {
            $supplierArray[$supplier["id"]] = $supplier["name"];
        }
        return $supplierArray;

    }

    /**
     * 获取供应商详细信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getSurplierDetail($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

    /**
     *  获取org_name 字符串
     *  @param $orgIdStr
     *  @return string
     **/
    public static function getOrgNameStr($orgIdStr)
    {
        $orgIdArr   = explode('-', trim($orgIdStr, '-'));
        $orgNameStr = '';
        foreach ($orgIdArr as $key => $value) {
            $orgNameStr .= Organization::getField('org_name', ['org_id' => $value]) . '，';
        }
        $orgNameStr = trim($orgNameStr, '，');
        return $orgNameStr;
    }

    /**
     * 根据分公司获取供水商列表
     * @author  zgw
     * @version 2016-10-25
     * @param   string     $orgId [description]
     * @return  [type]            [description]
     */
    public static function getOrgWaterList($orgId = '')
    {
        if (!$orgId) {
            $orgId = Manager::getManagerBranchId();
        }
        $where = ['type' => self::WATER];
        if ($orgId > 1) {
            $where = ['and', ['like', 'org_id', '-' . $orgId . '-'], $where];
        }
        return self::getSupplierArray($where);
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-12-19
     * @param   string     $field 要获取的字段名
     * @param   array      $where 查询条件
     * @return  string|int        字段值
     */
    public static function getField($field, $where)
    {
        $obj = self::find()->select($field)->where($where)->one();
        return $obj ? $obj->$field : '';
    }

    /**
     * 获取供应商名称
     * @author wangxiwen
     * @version 2018-11-13
     * @return
     */
    public static function getSupplier()
    {
        $supplierArray = self::find()->select('id,name')->asArray()->all();
        return ArrayHelper::map($supplierArray, 'id', 'name', null);
    }
}
