<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equip_trafficking_org_assoc".
 *
 * @property integer $trafficking_suppliers_id
 * @property integer $org_id
 *
 * @property Organization $org
 * @property EquipTraffickingSuppliers $traffickingSuppliers
 */
class EquipTraffickingOrgAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_trafficking_org_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trafficking_suppliers_id', 'org_id'], 'required'],
            [['trafficking_suppliers_id', 'org_id'], 'integer'],
            [['trafficking_suppliers_id', 'org_id'], 'unique', 'targetAttribute' => ['trafficking_suppliers_id', 'org_id'], 'message' => 'The combination of Trafficking Suppliers ID and Org ID has already been taken.'],
            [['org_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['org_id' => 'org_id']],
            [['trafficking_suppliers_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipTraffickingSuppliers::className(), 'targetAttribute' => ['trafficking_suppliers_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'trafficking_suppliers_id' => 'Trafficking Suppliers ID',
            'org_id' => 'Org ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTraffickingSuppliers()
    {
        return $this->hasOne(EquipTraffickingSuppliers::className(), ['id' => 'trafficking_suppliers_id']);
    }

    /**
     * 获取某一列值的数组
     * @param  string $filed [description]
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getColumn($filed='org_id',$where=[])
    {
        return \yii\helpers\ArrayHelper::getColumn(self::find()->where($where)->all(),$filed);
    }
}
