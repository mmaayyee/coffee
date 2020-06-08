<?php

namespace backend\models;

use backend\models\ScmMaterialType;
use Yii;

/**
 * This is the model class for table "distribution_filler_gram".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $build_id
 * @property integer $distribution_task_id
 * @property integer $supplier_id
 * @property integer $gram
 * @property integer $material_type_id
 */
class DistributionFillerGram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_filler_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'build_id', 'distribution_task_id', 'supplier_id', 'gram', 'material_type_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'equip_id'             => 'Equip ID',
            'build_id'             => 'Build ID',
            'distribution_task_id' => 'Distribution Task ID',
            'supplier_id'          => 'Supplier ID',
            'gram'                 => 'Gram',
            'material_type_id'     => 'Material Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }

    /**
     * 获取添料信息(散料)
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $taskId 任务ID
     * @return array
     */
    public static function getDistributionFillerGram($taskId)
    {
        return self::find()
            ->where(['distribution_task_id' => $taskId])
            ->asArray()
            ->all();
    }
    /**
     * 插入散料配送记录
     * @author wxl
     * @param array $store
     * @return bool
     */
    public static function addDistributionFillerGramRecord($store = [])
    {
        $fillerGramModel                       = new DistributionFillerGram();
        $fillerGramModel->equip_id             = $store["equip_id"];
        $fillerGramModel->build_id             = $store['build_id'];
        $fillerGramModel->distribution_task_id = $store['distribution_task_id'];
        $fillerGramModel->material_type_id     = $store['material_type_id'];
        $fillerGramModel->supplier_id          = $store['supplier_id'];
        $fillerGramModel->gram                 = intval($store['gram']);
        return $fillerGramModel->save();
    }
}
