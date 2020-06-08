<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "scm_material_stock".
 *
 * @property string $id
 * @property string $name
 * @property string $ctime
 *
 * @property ScmEquipTypeMatstock[] $scmEquipTypeMatstocks
 */
class ScmMaterialStock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_material_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stock_code', 'name'], 'required'],
            [['stock_code', 'name'], 'unique'],
            [['name'], 'string', 'max' => 20],
            [['stock_code'], 'string', 'max' => 10],
            [['type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipMaterialStockAssocs()
    {
        return $this->hasMany(EquipMaterialStockAssoc::className(), ['material_stock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquips()
    {
        return $this->hasMany(Equipments::className(), ['id' => 'equip_id'])->viaTable('equip_material_stock_assoc', ['material_stock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScmEquiptypeMatstockAssocs()
    {
        return $this->hasMany(ScmEquiptypeMatstockAssoc::className(), ['matstock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(ScmEquipType::className(), ['id' => 'equip_type_id'])->viaTable('scm_equiptype_matstock_assoc', ['matstock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScmEquipTypeMatstocks()
    {
        return $this->hasMany(ScmEquipTypeMatstockAssoc::className(), ['matstock_id' => 'id']);
    }

    /**
     * 获取物料料仓详细信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getMaterialStockDetail($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

    /**
     * 获取某个字段的值
     * @param  string       $field      要获取的字段名称
     * @param  array        $where      查询条件
     * @return string|int               要获取的字段值
     */
    public static function getField($field, $where)
    {
        $model = self::findOne($where);
        return $model ? $model->$field : false;
    }

    /**
     * 获取料仓id和名称数组
     * @author  zgw
     * @version 2016-09-22
     * @return  [type]     [description]
     */
    public static function getMaterialStockIdNameArr($where = [])
    {
        if (!$where) {
            return Tools::map(self::find()->all(), 'id', 'name');
        } else {
            return Tools::map(self::find()->where($where)->all(), 'id', 'name');
        }
    }
    /**
     * 获取料仓编号数组
     * @return array
     */
    public static function getMaterialStockCodeName()
    {
        return Tools::map(self::find()->all(), 'stock_code', 'name');
    }

    /**
     * 获取料仓ID对应的料仓编号
     */
    public static function getMaterialStockIdToCode()
    {
        $list = self::find()->select('id,stock_code')->asArray()->all();
        return $list ? Tools::map($list, 'id', 'stock_code', null, null) : [];
    }

    /**
     * 获取料仓ID对应的料仓编号
     */
    public static function getMaterialStockCodeToId()
    {
        $list = self::find()->select('id,stock_code')->asArray()->all();
        return $list ? Tools::map($list, 'stock_code', 'id', null, null) : [];
    }
}
