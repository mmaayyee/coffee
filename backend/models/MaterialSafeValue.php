<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Building;
use common\models\Equipments;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "material_safe_value".
 *
 * @property integer $id
 * @property integer $equipment_id
 * @property integer $material_stock_id
 * @property integer $safe_value
 */
class MaterialSafeValue extends \yii\db\ActiveRecord
{
    public $build_id;
    public $equip_code;
    public $org_id;
    public $org_type;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_safe_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_id', 'material_stock_id', 'safe_value', 'bottom_value'], 'integer'],
            [['equipment_id', 'safe_value'], 'required'],
            ['equipment_id', 'unique', 'message' => '楼宇不能重复设置'],
            //['safe_value','each', 'rule' => ['integer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'equipment_id'      => '设备',
            'material_stock_id' => '料仓',
            'safe_value'        => '预警值',
            'build_id'          => '楼宇',
            'equip_code'        => '设备编号',
            'org_id'            => '分公司',
            'org_type'          => '机构类型',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipments::className(), ['id' => 'equipment_id']);
    }

    /**
     * 获取料仓阀值内容
     * @param int $equipmentId
     * @return string
     */
    public static function getStockSafeValue($equipmentId = 0)
    {
        $stocks = self::find()->where(['equipment_id' => $equipmentId])->asArray()->all();
        if (!$stocks) {
            return "";
        }
        //查询设备的产品组
        $productGroupId = Equipments::getField('pro_group_id', ['id' => $equipmentId]);
        $stockMaterial  = ProductMaterialStockAssoc::getStockIdOfMaterialType($productGroupId);
        //获取料仓对应的物料分类
        $materialTypeList = ScmMaterialType::getMaterialTypeStock();

        $tr            = '';
        $materialStock = ScmMaterialStock::getMaterialStockIdNameArr();
        foreach ($stocks as $key => $value) {
            if (!isset($materialStock[$value['material_stock_id']]) || !isset($stockMaterial[$value['material_stock_id']])) {
                continue;
            }
            $material = isset($materialTypeList[$stockMaterial[$value['material_stock_id']]]) ? $materialTypeList[$stockMaterial[$value['material_stock_id']]] : '';
            $unit     = ScmMaterialStock::getField('type', ['id' => $value['material_stock_id']]) === 0 ? '克' : '个';
            $tr .= "<tr><td>" . $materialStock[$value['material_stock_id']] . "</td><td>" . $material . "</td><td>" . $value['safe_value'] . $unit . "</td><td>" . $value['bottom_value'] . $unit . "</td></tr>";
        }
        return "<table class= 'table table-bordered'><tr><td>料仓</td><td>物料分类</td><td>预警值</td><td>下限值</td></tr>" . $tr . "</table>";
    }

    /**
     * 删除制定设备的预警值设置
     * @param $equipmentId
     * @return int
     */
    public static function clearEquipmentSaveValue($equipmentId)
    {
        return self::deleteAll('equipment_id = :equipmentId', [':equipmentId' => $equipmentId]);
    }

    /**
     * 获取设备的预警值
     * @param int $equipmentId
     * @param int $stockId
     * @return mixed|string
     */
    public static function getEquipmentStockByEquipmentId($equipmentId = 0, $stockId = 0)
    {
        $stockCode = ScmMaterialStock::getMaterialStockIdToCode();
        $stockCode = array_flip($stockCode);
        if (empty($stockCode[$stockId])) {
            return 0;
        }
        $safeValue = self::find()
            ->select('safe_value')
            ->where([
                'equipment_id'      => $equipmentId,
                'material_stock_id' => $stockCode[$stockId],
            ])
            ->asArray()
            ->one();
        return isset($safeValue['safe_value']) ? $safeValue['safe_value'] : 0;
    }

    /**
     * 获取指定设备料仓预警值
     * @param int $equipmentId
     * @return array
     */
    private static function getEquipmentStockKeyValue($equipmentId = 0)
    {
        $list = self::find()->select('material_stock_id,safe_value')->where(['equipment_id' => $equipmentId])->asArray()->all();
        return Tools::map($list, 'material_stock_id', 'safe_value', null, null);
    }

    /**
     * 获取设备的下限值
     * @author wxl
     * @param int $equipmentId
     * @return array
     */
    public static function getEquipmentStockBottomValue($equipmentId = 0)
    {
        $list = self::find()->select('material_stock_id,bottom_value')->where(['equipment_id' => $equipmentId])->asArray()->all();
        return $list ? Tools::map($list, 'material_stock_id', 'bottom_value', null, null) : [];
    }

    /**
     * 判断设备料仓是否到达预警值
     * @param array $data
     * @throws \yii\db\Exception
     */
    public static function checkStockSafeVal($data = [])
    {
        if (isset($data['volume']) && !empty($data['volume'])) {

            $stockString = '';
            $equipmentId = Equipments::getField('id', ['equip_code' => $data['equip_code']]);
            $orgId       = Equipments::getField('org_id', ['equip_code' => $data['equip_code']]);
            // 获取该设备所在分公司下配送主管的用户id
            $distributionResponsibleId = WxMember::getFiled('userid', ['org_id' => $orgId, 'position' => WxMember::DISTRIBUTION_RESPONSIBLE]);

            $buildId   = Equipments::getField('build_id', ['equip_code' => $data['equip_code']]);
            $buildName = Building::getField('name', ['id' => $buildId]);

            $stockValue = self::getEquipmentStockKeyValue($equipmentId);
            $stock      = ScmMaterialStock::getMaterialStockCodeName();

            //获取产品组料仓出料速率
            $productGroupId = Equipments::getField('pro_group_id', ['equip_code' => $data['equip_code']]);
            $stockSecond    = ProductMaterialStockAssoc::getStockIdOfSecond($productGroupId);

            //查出料仓ID对应的料仓编号
            $stockList = ScmMaterialStock::getMaterialStockIdToCode();
            //反转健和值
            $stockList = array_flip($stockList);

            foreach ($data['volume'] as $key => $value) {

                $second = isset($stockSecond[$key]) ? $stockSecond[$key] : 1;
                $id     = $stockList[$key];
                if (!isset($stockValue[$id]) || (isset($stockValue[$id]) && $stockValue[$id] == 0)) {
                    continue;
                }
                if (intval($stockValue[$id]) > ($value * $second)) {
                    $stockString .= $stock[$id] . '物料即将不足,';
                }
            }

            if ($distributionResponsibleId && !empty($stockString)) {
                $info = date('Y年m月d日 H点i分s秒') . ',' . $buildName . ',' . substr($stockString, 0, -1);
                SendNotice::sendWxNotice($distributionResponsibleId, '', $info, Yii::$app->params['equip_agentid']);
            }

        }
    }

    /**
     * 获取设备的ID
     * @return array
     */
    public static function getEquipmentId()
    {
        $list = self::find()->select('equipment_id')->distinct()->asArray()->all();
        return ArrayHelper::getColumn($list, 'equipment_id');
    }
}
