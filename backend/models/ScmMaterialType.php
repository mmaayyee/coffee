<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "scm_material_type".
 *
 * @property integer $id
 * @property string $material_type_name
 * @property string $unit
 */
class ScmMaterialType extends \yii\db\ActiveRecord
{
    //是否是放入料仓的物料
    const TYPE_ON       = 1;
    const TYPE_OFF      = 2;
    public static $type = ['' => '请选择', self::TYPE_ON => '是', self::TYPE_OFF => '否'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_material_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_type_name', 'unit', 'type'], 'required'],
            [['material_type_name'], 'string', 'max' => 50],
            [['unit', 'weight_unit', 'spec_unit', 'new_spec_unit'], 'string', 'max' => 20],
            [['type'], 'integer'],
            [['material_type_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'material_type_name' => '物料分类名称',
            'unit'               => '物料单位',
            'spec_unit'          => '物料规格单位',
            'new_spec_unit'      => '新增物料规格单位',
            'type'               => '是否为放入料仓中的物料',
            'weight_unit'        => '散料单位',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasMany(ScmMaterial::className(), ['material_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasMany(ScmMaterialType::className(), ['id' => 'material_type']);
    }

    /**
     * 返回物料分类Id和name对应的数组
     * @author  zgw
     * @version 2016-08-11
     * @param   integer    $type  [是否有请选择]
     * @param   array      $where [where条件]
     * @return  [array]           [id=>name]
     */
    public static function getIdNameArr($type = 1, $where = [])
    {
        if ($where) {
            return Tools::map(self::find()->where($where)->all(), 'id', 'material_type_name', null, $type);
        } else {
            return Tools::map(self::find()->all(), 'id', 'material_type_name', null, $type);
        }
    }

    /**
     * 返回是否为放入料仓中的物料
     * @author  zgw
     * @version 2016-08-12
     * @param   integer    $id 物料分类id
     * @return  integer
     */
    public static function getType($id = 0)
    {
        return self::findOne($id) ? self::findOne($id)->type : 0;
    }

    /**
     * 获取物料类型详细信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getMaterialTypeDetail($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

    /**
     * 获取物料类型数组
     * @param  [type] $where $pieces 单位
     * @return [type]
     */
    public static function getMaterialTypeArray($where = "", $pieces = '')
    {
        $materialTypes   = self::find()->select(['id', 'material_type_name', 'type'])->where($where)->asArray()->all();
        $materialTypeArr = [];
        foreach ($materialTypes as $materialType) {
            if ($pieces) {
                if ($materialType['type'] == 2) {
                    // 非物料
                    $materialTypeArr[$materialType['id']] = $materialType['material_type_name'] . '/个';
                } else {
                    $materialTypeArr[$materialType['id']] = $materialType['material_type_name'] . "/克";
                }
            } else {
                $materialTypeArr[$materialType['id']] = $materialType['material_type_name'];
            }
        }
        return $materialTypeArr;
    }
    /**
     * 获取物料分类单位 克|个
     * @author wangxiwen
     * @version 2018-09-05
     * @return array
     */
    public static function getMaterialTypeUnit()
    {
        $materialArr = self::find()
            ->select(['id', 'unit', 'weight_unit', 'type'])
            ->asArray()
            ->all();
        $materialList = [];
        foreach ($materialArr as $material) {
            $materialTypeId = $material['id'];
            if ($material['type'] == 2) {
                // 非物料
                $materialList[$materialTypeId] = $material['unit'];
            } else {
                $materialList[$materialTypeId] = $material['weight_unit'];
            }
        }
        return $materialList;
    }

    /**
     * 获取所有放入料仓中的物料
     */
    public static function getMaterialTypeStock()
    {
        $list = self::getOnlineMaterialType();
        return Tools::map($list, 'id', 'material_type_name', null, null);
    }

    /**
     * 获取放入料仓中的物料名称
     * @param int $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getBulkMaterialName($type = 1)
    {
        $bulks = self::find()->select('material_type_name')->where(['type' => $type])->asArray()->all();
        return ArrayHelper::getColumn($bulks, 'material_type_name');
    }

    /**
     * 获取放入料仓总的物料分类
     * @author zhenggangwei
     * @date   2020-03-21
     * @return [type]     [description]
     */
    public static function getOnlineMaterialType()
    {
        return self::find()
            ->where(['type' => self::TYPE_ON])
            ->asArray()
            ->all();
    }

}
