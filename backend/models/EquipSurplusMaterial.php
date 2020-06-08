<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\ArrayDataProviderSelf;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_surplus_material".
 *
 * @property integer $equip_code
 * @property integer $material_stock_code
 * @property double $surplus_material
 * @property string $date
 */
class EquipSurplusMaterial extends \yii\db\ActiveRecord
{
    public $topValue; //料仓上限值
    public $bottomValue; //料仓下限值
    public $safeValue; //预警值
    public $id;
    public $group_stock_id;
    public $stock_code;
    public $stock_volume_bound;
    public $materiel_id;
    public $blanking_rate;
    public $warning_value;
    public $bottom_value;
    public $pro_group_stock_info_id;
    public $surplus_material;
    public $build_id;
    public $operation_status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_surplus_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code', 'material_stock_code'], 'string'],
            [['surplus_material'], 'number'],
            [['group_stock_id', 'stock_code', 'stock_volume_bound', 'materiel_id', 'blanking_rate', 'warning_value', 'bottom_value', 'pro_group_stock_info_id', 'surplus_material', 'date', 'id', 'build_id', 'operation_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equip_code'          => '设备编号',
            'material_stock_code' => '料仓编号',
            'surplus_material'    => '剩余物料',
            'date'                => '时间',
            'topValue'            => '上限值',
            'bottomValue'         => '下限值',
            'safeValue'           => '预警值',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    /**
     * 获取剩余列表
     * @author  zgw
     * @version 2016-08-12
     * @return  [type]     [description]
     */
    public static function getSurplusMaterialList()
    {
        return self::find()->asArray()->all();
    }

    /**
     * 获取设备平均消耗物料数据
     * @author  zgw
     * @version 2016-08-29
     * @return  [type]     [description]
     */
    public static function getSurplusMaterialArr()
    {
        $equipSurplusMaterialArr = [];
        $surplusMaterialList     = self::find()->all();
        foreach ($surplusMaterialList as $surplusMaterialArr) {
            $equipSurplusMaterialArr[$surplusMaterialArr->equip_code][$surplusMaterialArr->material_stock_code] = $surplusMaterialArr->surplus_material;
        }
        return $equipSurplusMaterialArr;
    }

    /**
     * 批量添加数据
     * @param [type] $data [description]
     */
    public static function addAll($data)
    {
        $surplusMaterialArr = [];
        $i                  = 0;
        foreach ($data as $equipCode => $materialStockArr) {
            foreach ($materialStockArr as $materialStockCode => $surplusMaterial) {
                $surplusMaterialArr[$i]['equip_code']          = $equipCode;
                $surplusMaterialArr[$i]['material_stock_code'] = $materialStockCode;
                $surplusMaterialArr[$i]['surplus_material']    = $surplusMaterial > 0 ? $surplusMaterial : 0;
                $surplusMaterialArr[$i]['date']                = date('Y-m-d  H:i:s');
                $i++;
            }
        }
        // 开启事务
        $transactoin = Yii::$app->db->beginTransaction();
        $delRes      = self::deleteAll();
        // 添加最近三天的数据
        $addRes = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['equip_code', 'material_stock_code', 'surplus_material', 'date'], $surplusMaterialArr)->execute();

        if ($delRes === false || $addRes === false) {
            $transactoin->rollBack();
            return false;
        }

        $transactoin->commit();
        return true;
    }

    /**
     * 判断更新或者新增剩余物料
     * @param $data
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function addSurplusMaterial($data)
    {
        if (isset($data['volume']) && !empty($data['volume'])) {

            if ($data['equip_code']) {
                //获取产品组料仓出料速率
                $productGroupId = Equipments::getField('pro_group_id', ['equip_code' => $data['equip_code']]);
                // 料仓ID =》 出料速度
                $stockSecond = ProductMaterialStockAssoc::getStockIdOfSecond($productGroupId);
                // 料仓ID =》 料仓编号
                $scmMaterialStock = ScmMaterialStock::getMaterialStockIdToCode();
            }
            $stockSecondSpeed = [];
            foreach ($stockSecond as $stockCodeId => $speed) {
                if (isset($scmMaterialStock[$stockCodeId])) {
                    $stockSecondSpeed[$scmMaterialStock[$stockCodeId]] = $speed;
                }
            }
            @file_put_contents("/alidata/www/erp/trunk/frontend/web/uploads/sulingling.log", '回调返回结果：\r\n' . date("Y-m-d H:i:s") . json_encode($stockSecondSpeed) . "\r\n", FILE_APPEND);
            foreach ($data['volume'] as $key => $value) {
                $equipSurplusObj = self::findOne(['equip_code' => $data['equip_code'], 'material_stock_code' => $key]);
                if (empty($equipSurplusObj)) {
                    $equipSurplusObj                      = new self();
                    $equipSurplusObj->equip_code          = $data['equip_code'];
                    $equipSurplusObj->material_stock_code = (string) $key;
                    $equipSurplusObj->date                = date('Y-m-d H:i:s');
                }

                if (!isset($stockSecondSpeed[$key]) || $key == 'G') {
                    $second = 1;
                } else {
                    $second = $stockSecondSpeed[$key];
                }
                $surplusMaterial                   = $value > 0 ? round($value * $second) : 0;
                $equipSurplusObj->surplus_material = isset($data['editor']) ? $value : $surplusMaterial;
                $equipSurplusObj->save();
            }
        }

    }

    /**
     * 获取某个物料的剩余物料
     * @author  zgw
     * @version 2016-08-12
     * @param   array     $where 查询条件
     * @return  [type]            [description]
     */
    public static function getSurplusMaterial($equipCode, $materialStockCode)
    {
        $surplusMaterialModle = self::findOne(['equip_code' => $equipCode, 'material_stock_Code' => $materialStockCode]);

        return $surplusMaterialModle ? $surplusMaterialModle->surplus_material : false;
    }

    /**
     * 获取代理商的剩余物料
     * @author  zgw
     * @version 2016-11-16
     * @param   array     $equipCode  设备编号
     */
    public static function getAgentsSurplusMaterial($equipCode)
    {
        return self::find()->where(['equip_code' => $equipCode])->all();
    }

    /**
     * 获取设备料仓对应的剩余物料
     * @param int $equipCode
     * @return array
     */
    public static function getEquipmentStockMaterial($equipCode = 0)
    {
        $list = self::find()->select('material_stock_code,surplus_material')->where(['equip_code' => $equipCode])->orderBy('date DESC')->asArray()->all();
        return $list ? Tools::map($list, 'material_stock_code', 'surplus_material', null, null) : $list;
    }

    /**
     * 根据设备编号获取料仓的剩余物料
     * @author wxl
     * @param int $equipId
     * @param int $stockId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getEquipmentSurplusMaterialByStockId($equipId = 0, $stockId = 0)
    {
        $equipCode         = Equipments::getField('equip_code', ['id' => $equipId]);
        $stockCode         = ScmMaterialStock::getMaterialStockIdToCode();
        $materialStockCode = isset($stockCode[$stockId]) ? $stockCode[$stockId] : '';
        return EquipSurplusMaterial::find()->where(['equip_code' => $equipCode, 'material_stock_code' => $materialStockCode])->one();
    }

    /**
     * 将物料剩余值进行更新
     * @author sulingling
     * @version 2018-06-04
     * @param array() 更新的数组
     * @return boolean
     */
    public static function surplusMaterialUpdate($data)
    {
//        获取设备编号下面所有的料料仓剩余值
        $getAgentsSurplusMaterial = self::getAgentsSurplusMaterial($data['equipment']);
        $realStockCodeList        = array_keys($data['volume']);
        $nowStockColdeList        = array_column($getAgentsSurplusMaterial, 'material_stock_code');
//        对多余的料仓编号进行删除
        if (isset($data['timing'])) {
            foreach ($getAgentsSurplusMaterial as $surplusMaterial) {
                if (!in_array($surplusMaterial->material_stock_code, $realStockCodeList)) {
                    $surplusMaterial->delete();
                }
            }
        }
//        对已有的料仓编号进行更新
        foreach ($getAgentsSurplusMaterial as $surplusMaterial) {
            if (in_array($surplusMaterial->material_stock_code, $realStockCodeList)) {
                $surplusMaterial->surplus_material = $data['volume'][$surplusMaterial->material_stock_code];
                $surplusMaterial->date             = date("Y-m-d H:i:s", time());
                $surplusMaterial->save();
            }
        }
//        对没有的料仓编号进行添加
        foreach ($data['volume'] as $stockCode => $surplusMaterial) {
            if (!in_array($stockCode, $nowStockColdeList)) {
                $equipSurplusMaterial                      = new self();
                $equipSurplusMaterial->equip_code          = $data['equipment'];
                $equipSurplusMaterial->material_stock_code = $stockCode;
                $equipSurplusMaterial->surplus_material    = $surplusMaterial;
                $equipSurplusMaterial->date                = date("Y-m-d H:i:s", time());
                $equipSurplusMaterial->save();
            }
        }
    }

    /**
     * 将数组转化为对象
     * @author sulingling
     * @version 2018-08-10
     * @param $equipSurpluseMaterialList
     * @param $equipCode
     * @return ArrayDataProviderSelf
     */
    public static function equipSurplusMaterialSerch($equipSurpluseMaterialList, $equipCode)
    {
        $dataProviderList = [];
        if ($equipSurpluseMaterialList) {
            foreach ($equipSurpluseMaterialList as $equipSurpluseMaterial) {
                $model                               = new self();
                $equipSurpluseMaterial['equip_code'] = $equipCode;
                $model->load(['EquipSurplusMaterial' => $equipSurpluseMaterial]);
                $dataProviderList[] = $model;
            }
        }
        $dataProvider = new ArrayDataProviderSelf([
            'allModels'  => $dataProviderList,
            'pagination' => [
                'pageSize' => 50,
            ],
            'totalCount' => count($equipSurpluseMaterialList),
        ]);
        return $dataProvider;
    }
}
