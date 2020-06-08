<?php

namespace common\models;

use backend\models\Manager;
use common\helpers\Tools;
use common\models\EquipTraffickingOrgAssoc;
use Yii;
use Yii\helpers\ArrayHelper;

/**
 * This is the model class for table "equip_trafficking_suppliers".
 *
 * @property integer $id
 * @property string $name
 * @property string $userid
 * @property string $mobile
 * @property string $email
 * @property integer $create_time
 *
 * @property WxMember $user
 */
class EquipTraffickingSuppliers extends \yii\db\ActiveRecord
{
    public $org_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_trafficking_suppliers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id', 'name', 'userid'], 'required'],
            [['create_time'], 'integer'],
            [['name', 'userid', 'email'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 20],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => WxMember::className(), 'targetAttribute' => ['userid' => 'userid']],
            [['name', 'mobile'], 'unique'],
            [['userid'], 'unique', 'message' => '通讯录中对应成员已经被占用。'],
            [['org_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '投放商名称',
            'userid'      => '通讯录中对应成员',
            'mobile'      => '联系方式',
            'email'       => '邮箱',
            'create_time' => '添加时间',
            'org_id'      => '请选择分公司',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'userid']);
    }

    /**
     * 返回供应商用户id和供应商名称
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public static function getIdNameList($where)
    {
        return Tools::map(self::find()->where($where)->all(), 'userid', 'name');
    }

    /**
     * 返回当前登录用户所在分公司的供应商用户id和供应商名称
     * @return [type] [description]
     */
    public static function getIdNameArr()
    {
        $where        = [];
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $where['org_id'] = $managerOrgId;
        }
        // 根据当前登录用户的分公司id获取投放放上id
        $tra_sup_id_arr = EquipTraffickingOrgAssoc::getColumn('trafficking_suppliers_id', $where);
        $tra_sup_id_arr = array_unique($tra_sup_id_arr);

        return self::getIdNameList(['id' => $tra_sup_id_arr]);
    }

    /**
     * 获取符合条件某一列的值
     * @param  string $filed 列名
     * @param  array  $where 查询条件
     * @return array
     */
    public static function getColumn($filed = 'name', $where = [])
    {
        $traffickingList = self::find()->where($where)->all();
        if (!$traffickingList) {
            return [];
        }

        return ArrayHelper::getColumn($traffickingList, $filed);
    }
}
