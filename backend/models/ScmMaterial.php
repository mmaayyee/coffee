<?php

namespace backend\models;

use backend\models\ProductMaterialStockAssoc;
use backend\models\ScmEquipTypeMaterialAssoc;
use backend\models\ScmMaterialStock;
use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use common\models\AgentsApi;
use common\models\Equipments;
use common\models\Sysconfig;
use Yii;

/**
 * This is the model class for table "scm_material".
 *
 * @property string $id
 * @property string $supplier_id
 * @property string $name
 * @property integer $weight
 * @property string $create_time
 *
 * @property ScmSupplier $supplier
 */
class ScmMaterial extends \yii\db\ActiveRecord
{

    const IS_OPERATION = 1;
    const NO_OPERATION = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_id', 'name'], 'required'],
            [['supplier_id', 'weight', 'material_type', 'create_time', 'is_operation'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmSupplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['material_type'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterialType::className(), 'targetAttribute' => ['material_type' => 'id']],
            [['name'], 'unique', 'targetAttribute' => ['name', 'supplier_id', 'material_type', 'weight'],
                'comboNotUnique' => '该物料已存在！', //错误信息
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'supplier_id'   => '供应商',
            'name'          => '物料名称',
            'weight'        => '规格',
            'material_type' => '物料分类',
            'create_time'   => '添加时间',
            'is_operation'  => '是否运维使用',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributionDailyTasks()
    {
        return $this->hasMany(DistributionDailyTask::className(), ['material_type' => 'material_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(ScmSupplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type']);
    }

    /**
     * 获取指定物料类型中规格最小的物料信息
     * @param  int      $material_type      物料类型id
     * @return      [description]
     */
    public static function getWeightLeast($material_type)
    {
        return self::find()->where(['material_type' => $material_type])->orderby('weight')->one();
    }

    /**
     * 获取指定物料类型中规格最小的物料信息
     * @param  int      $material_type      物料类型id
     * @return      [description]
     */
    public static function getMaterialObj($where)
    {
        return self::find()->where($where)->one();
    }

    /**
     * 获取是否运维使用
     * @author wangxiwen
     * @version 2018-05-22
     * return array
     */
    public static function getOperation()
    {
        return [
            ''                 => '请选择',
            self::NO_OPERATION => '否',
            self::IS_OPERATION => '是',
        ];
    }
    /**
     *  获取material的数组列表
     *  @return $materialList  Array
     **/
    public static function getScmMaterialList($select = false)
    {
        $list         = self::find()->select('id,name')->asArray()->all();
        $materialList = $select ? [0 => '请选择'] : [];
        foreach ($list as $key => $value) {
            $materialList[$value['id']] = $value['name'];
        }
        return $materialList;
    }

    /**
     *  组合物料信息的数组（其中组合新字段material_supplier 供应商--规格）
     *  @Author Zhangmuyu
     *  @return array 二维数组
     **/
    public static function getScmMaterialArr()
    {
        $list    = ScmMaterial::find()->asArray()->all();
        $listArr = [];
        foreach ($list as $key => $value) {
            $listArr[$key]['id']                = $value['id'];
            $listArr[$key]['name']              = $value["name"];
            $listArr[$key]['create_time']       = $value['create_time'];
            $listArr[$key]['supplier_id']       = $value["supplier_id"];
            $listArr[$key]['material_type']     = $value["material_type"];
            $listArr[$key]["material_supplier"] = ScmSupplier::getSurplierDetail('name', ["id" => $value['supplier_id']])['name'] . '--' . $value['weight'] . "包/克";
        }
        $materialArr = [];
        foreach ($listArr as $key => $value) {
            $materialArr[$value['material_type']][] = $value;
        }
        return $materialArr;
    }

    /**
     *  组合物料信息的数组（其中组合新字段material_supplier 供应商--规格）
     *  @Author Zhangmuyu
     *  @return array 二维数组
     **/
    public static function getScmMaterialTypeArr()
    {
        $materialObjList = self::find()->alias('a')->orderby('material_type')->where('weight=(' . self::find()->select("min(weight)")->where("material_type = a.material_type and a.is_operation = 1")->createCommand()->getRawSql() . ')')->groupBy('material_type')->all();

        $materialArr = [];
        foreach ($materialObjList as &$materialObj) {
            if ($materialObj->weight > 0) {
                $materialObj->supplier_id = $materialObj->materialType->material_type_name . '：' . $materialObj->supplier->name . '-' . $materialObj->name . '-' . $materialObj->weight . $materialObj->materialType->spec_unit;
            } else {
                $materialObj->supplier_id = $materialObj->materialType->material_type_name . '：' . $materialObj->supplier->name . '-' . $materialObj->name;
            }
            $materialArr[] = $materialObj;
        }
        return $materialArr;
    }

    /**
     *  获取物料料仓的信息表（其中组合新字段material_kind物料种类 供应商--规格）
     *  @Author Zhangmuyu
     *  @return array 二维数组
     **/
    public static function getMaterialStockArr($distributeTaskArr, $type = 1)
    {
        //$assocArr = EquipMaterialStockAssoc::find()->where(['equip_id' => $distributeTaskArr['equip_id']])->asArray()->orderBy("material_type")->all();
        $proGroupId = Equipments::getField(pro_group_id, ['id' => $distributeTaskArr['equip_id']]);
        $assocArr   = ProductMaterialStockAssoc::find()->where(['pro_group_id' => $proGroupId])->asArray()->orderBy("material_type")->all();

        $scmMaterialArr = [];
        foreach ($assocArr as $key => $value) {
            $scmMaterial = ScmMaterial::find()->where(['material_type' => $value['material_type']])->asArray()->all();
            if (!$scmMaterial) {
                continue;
            }
            $scmMaterialArr[$value['material_stock_id']] = $scmMaterial;
        }
        $listArr = self::combiningMaterialArr($scmMaterialArr, $type);
        return $listArr;

    }

    /**
     *  组合数组
     *  @param $scmMaterialArr
     *  @return Array
     **/
    public static function combiningMaterialArr($scmMaterialArr, $type)
    {
        $listArr = [];
        foreach ($scmMaterialArr as $stockId => $materialTypeArr) {
            foreach ($materialTypeArr as $key => $materialArr) {
                $listArr[$materialArr['material_type']]['data'][0]                         = ['id' => '', 'material_supplier' => '无'];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['id']            = $materialArr['id'];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['name']          = $materialArr["name"];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['create_time']   = $materialArr['create_time'];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['supplier_id']   = $materialArr["supplier_id"];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['material_type'] = $materialArr["material_type"];
                $listArr[$materialArr['material_type']]['data'][$key + 1]['stock_id']      = $stockId;

                $listArr[$materialArr['material_type']]['data'][$key + 1]["material_supplier"] = ScmSupplier::getSurplierDetail('name', ["id" => $materialArr['supplier_id']])['name'] . '--' . $materialArr['name'] . '--' . $materialArr['weight'] . "包/克";
                $stockName                                                                     = ScmMaterialStock::getMaterialStockDetail("name", ['id' => $stockId])["name"];
            }
            if ($type == 2) {
                $listArr[$materialArr['material_type']]['material_kind'] = ScmMaterialType::getIdNameArr()[$materialArr['material_type']];
            } else {
                $listArr[$materialArr['material_type']]['material_kind'] = $stockName . ScmMaterialType::getIdNameArr()[$materialArr['material_type']];
            }
        }
        return $listArr;
    }

    /**
     *  获取物料标签的数组
     *  @return 二维数组
     *
     **/
    public static function getScmEquipTypeArr()
    {
        $materialTypeArr = ScmMaterialType::find()->where(['type' => 2])->asArray()->all();
        $list            = [];
        foreach ($materialTypeArr as $key => $value) {
            $materialArr = ScmMaterial::find()->where(['material_type' => $value['id']])->asArray()->all();
            if ($materialArr) {
                $list[] = $materialArr;
            } else {
                continue;
            }
        }
        $listArr = [];
        foreach ($list as $listKey => $listVal) {
            foreach ($listVal as $key => $value) {
                $listArr[$value["material_type"]][$key + 1]['id']                = $value['id'];
                $listArr[$value["material_type"]][$key + 1]['name']              = $value["name"];
                $listArr[$value["material_type"]][$key + 1]['create_time']       = $value['create_time'];
                $listArr[$value["material_type"]][$key + 1]['supplier_id']       = $value["supplier_id"];
                $listArr[$value["material_type"]][$key + 1]['material_type']     = $value["material_type"];
                $listArr[$value["material_type"]][$key + 1]["material_supplier"] = "供应商:" . ScmSupplier::getSurplierDetail('name', ["id" => $value['supplier_id']])['name'] . '--' . $value["name"] . '--' . $value['weight'] . "规格";
            }
        }
        $materialArr = [];
        foreach ($listArr as $key => &$value) {
            $listArr[$key][0] = array('id' => '0', 'material_supplier' => '无');
            sort($listArr[$key]);
        }
        unset($value);
        return $listArr;
    }

    /**
     * 根据料仓id获取物料列表（带有供应商和规格信息）
     * @author  zgw
     * @version 2016-09-23
     * @param   [string]     $stockId     [料仓ID]
     * @param   [string]     $equipTypeId [设备类型ID]
     * @return  [string]                  [string]
     */
    public static function getMaterialFromMaterialStock($stockId, $equipTypeId)
    {
        $specialStock = json_decode(Sysconfig::getConfig('equipTypeStock'), 1);
        // 验证料仓id是否在需要根据设备类型设置规格的id数组中
        if ($specialStock && !in_array($stockId, array_keys($specialStock))) {
            return '';
        }
        // 获取指定的料仓和物料分类关联对象
        $materialTypeObj = ScmMaterialType::find()->where(['id' => $specialStock[$stockId]])->one();

        // 判断料仓对应的物料类型是否为放入料仓中的物料,去除硬币
        if ($materialTypeObj) {
            // 初始化已选中的物料id
            $selectedMaterialId = [];
            if ($equipTypeId) {
                $selectedMaterialId = ScmEquipTypeMaterialAssoc::getMaterialId($equipTypeId);
            }
            // 以物料分类名称为键以物料信息为值的数组
            $materialTypeList = self::getMaterialListFromMaterialType($materialTypeObj, $selectedMaterialId);
            return $materialTypeList;
        } else {
            return '';
        }

    }

    /**
     * 根据料仓和物料分类关联对象组装物料列表（带有供应商和规格信息）
     * 注：规则对应，料仓和物料分类 差1，以json格式配置在配置文件中
     * @author  zgw
     * @version 2016-09-23
     * @param   [type]     $materialTypeObj    [description]
     * @param   array      $selectedMaterialId [description]
     * @return  [type]                         [description]
     */
    public static function getMaterialListFromMaterialType($materialTypeObj, $selectedMaterialId = [])
    {
        $materialstr = '';
        if (isset($materialTypeObj->material) && $materialTypeObj->material) {
            if ($materialTypeObj->id == Sysconfig::getConfig('sugarMaterial')) {
                // 糖 特殊，
                $materialstr .= '<div id="stockId_' . Sysconfig::getConfig('sugarMaterialType') . '" class="form-group">
                <label class="control-label">' . $materialTypeObj->material_type_name . '</label>
                <select class="form-control" name="miscellaneous_material[' . $materialTypeObj->id . '][material_id]">';
            } else {
                $materialstr .= '<div id="stockId_' . ($materialTypeObj->id - 1) . '" class="form-group">
                <label class="control-label">' . $materialTypeObj->material_type_name . '</label>
                <select class="form-control" name="miscellaneous_material[' . $materialTypeObj->id . '][material_id]">';
            }
            foreach ($materialTypeObj->material as $materialObj) {
                if ($materialObj->supplier) {
                    $selected = in_array($materialObj->id, $selectedMaterialId) ? 'selected' : '';
                    $materialstr .= '<option value="' . $materialObj->id . '" ' . $selected . '>物料供应商：' . $materialObj->supplier->name . '--物料名称：' . $materialObj->name;
                    $materialstr .= !$materialObj->weight ? '' : '--规格：' . $materialObj->weight . $materialTypeObj->spec_unit . '</option>';
                } else {
                    $materialstr .= '<option value="">请先添加物料</option>';
                }
            }
            $materialstr .= '</select><div class="help-block" style="display:none;">' . $materialTypeObj->material_type_name . '不能为空。</div></div>';
        } else {
            $materialstr .= '<div id="stockId_' . ($materialTypeObj->id - 1) . '" class="form-group">
                <label class="control-label">' . $materialTypeObj->material_type_name . '</label>
                <select class="form-control" name="miscellaneous_material[' . $materialTypeObj->id . '][material_id]"><option value="">请先添加物料</option></select><div class="help-block" style="display:none;">' . $materialTypeObj->material_type_name . '不能为空。</div></div>';
        }
        return $materialstr;
    }

    /**
     * 获取物料详细信息
     * @author  zmy
     * @version update     2017-08-26
     * @param   [sting]     $filed [查询字段]
     * @param   [array]     $where [条件数组]
     * @return  [object]           [object]
     */
    public static function getMaterialDetail($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

    /**
     * 获取配送任务中的配送内容
     * @author  zgw
     * @version 2016-09-01
     * @param   [type]     $content [description]
     * @return  [type]              [description]
     */
    public static function getTaskMaterial($content)
    {
        $contentStr = '';
        if (!$content) {
            return '';
        }

        $content = json_decode($content, true);
        foreach ($content as $materialArr) {
            $materialDetail = self::findOne($materialArr['material_id']);
            $specUnit       = $materialDetail->materialType->spec_unit ? $materialDetail->weight . $materialDetail->materialType->spec_unit : '';
            $contentStr .= $materialDetail->materialType->material_type_name . '：' . $specUnit . ' ' . $materialArr['packets'] . $materialDetail->materialType->unit . '<br/>';
        }
        return $contentStr;
    }

    /**
     * 同步物料信息到代理商平台
     * @author  zgw
     * @version 2016-12-19
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function syncMaterial($data)
    {
        if (isset($data['supplier_id'])) {
            $data['supplier_name'] = ScmSupplier::getField('realname', ['id' => $data['supplier_id']]);
            unset($data['supplier_id']);
        }
        return AgentsApi::updateMaterial($data);
    }

    /**
     * 获取运维使用物料信息(每种物料分类下取物料规格最小项)
     * @author wangxiwen
     * @version 2018-10-13
     * @return
     */
    public static function getScmMaterial()
    {
        $materialArray = self::find()->orderby('sm.material_type,sm.weight,sm.id')
            ->alias('sm')
            ->leftJoin('scm_material_type smt', 'smt.id = sm.material_type')
            ->leftJoin('scm_supplier ss', 'ss.id = sm.supplier_id')
            ->where(['is_operation' => self::IS_OPERATION])
            ->select('sm.id material_id,sm.supplier_id,sm.name material_name,sm.weight,sm.material_type material_type_id,smt.material_type_name,smt.type,smt.unit,smt.spec_unit,smt.weight_unit,ss.name')
            ->asArray()
            ->all();
        $materialList = [];
        foreach ($materialArray as $material) {
            $materialTypeId = $material['material_type_id'];
            if (!empty($materialList[$materialTypeId])) {
                continue;
            }
            $materialList[$materialTypeId] = $material;
        }
        return $materialList;
    }

}
