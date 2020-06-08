<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "distribution_spare_packets".
 *
 * @property integer $material_id
 * @property integer $packets
 *
 * @property ScmMaterial $material
 */
class DistributionSparePackets extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_spare_packets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id'], 'required'],
            [['material_id', 'packets'], 'integer'],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterial::className(), 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_id' => '物料id',
            'packets'     => '数量（包数/个数）',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }
    /**
     * 获取备用料包数据
     * @author  zgw
     * @version 2016-08-17
     * @return  array     别用料包数据
     */
    public static function getSparePacketsList()
    {
        return self::find()->all();
    }

    /**
     * 批量添加数据
     * @author  zgw
     * @version 2016-08-17
     * @param   array     $data 要添加的数据
     */
    public static function addAll($data)
    {
        if ($data) {
            foreach ($data as &$value) {
                if (!$value['packets']) {
                    $value['packets'] = 0;
                }

            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        $delres      = self::deleteAll();
        $addres      = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['material_id', 'packets'], $data)->execute();
        if ($delres === false || $addres === false) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**
     * 获取备用料包内容
     * @author  zgw
     * @version 2016-08-18
     * @return  string
     */
    public static function getSparePacktesContent()
    {
        $sparePacktesList = self::getSparePacketsList();
        $content          = '';
        foreach ($sparePacktesList as $sparePacktesArr) {
            if (!isset($sparePacktesArr->material) || !isset($sparePacktesArr->material->materialType) || !$sparePacktesArr->packets) {
                continue;
            }

            $content .= $sparePacktesArr->material->materialType->material_type_name . '：';
            if ($sparePacktesArr->material->weight) {
                $content .= $sparePacktesArr->material->weight . $sparePacktesArr->material->materialType->spec_unit . ' ';
            }
            $content .= $sparePacktesArr->packets . $sparePacktesArr->material->materialType->unit . '<br/>';
        }
        return $content;
    }

    /**
     * 获取物料id以及对应的包数
     * @author  zgw
     * @version 2016-09-13
     * @return  [type]     [description]
     */
    public static function getMaterialIdSpacketArr()
    {
        return Tools::map(self::getSparePacketsList(), 'material_id', 'packets');
    }
    /**
     * 获取备用物料数组（计算出库单时用）
     * @author  zgw
     * @version 2016-10-18
     * @return  [type]     [description]
     */
    public static function getMaterialArr()
    {
        $materialArr      = [];
        $sparePacktesList = self::find()->all();
        if ($sparePacktesList) {
            foreach ($sparePacktesList as $sparePacktesObj) {
                if (!isset($sparePacktesObj->material) || !isset($sparePacktesObj->material->materialType) || !$sparePacktesObj['packets']) {
                    continue;
                }
                // 物料包数
                $materialArr[$sparePacktesObj['material_id']]['packets'] = $sparePacktesObj['packets'];
                // 物料分类名称、规格、规格单位
                $materialArr[$sparePacktesObj['material_id']]['content'] = $sparePacktesObj->material->weight ? $sparePacktesObj->material->materialType->material_type_name . '：' . $sparePacktesObj->material->weight . $sparePacktesObj->material->materialType->spec_unit : $sparePacktesObj->material->materialType->material_type_name . ':';
                // 物料单位
                $materialArr[$sparePacktesObj['material_id']]['unit'] = $sparePacktesObj->material->materialType->unit;
                // 物料分类id
                $materialArr[$sparePacktesObj['material_id']]['material_type_id'] = $sparePacktesObj->material->material_type;
            }
        }
        return $materialArr;
    }
}
