<?php

namespace backend\models;
use backend\models\ScmMaterial;
use Yii;

/**
 * This is the model class for table "scm_stock_num".
 *
 * @property integer $id
 * @property integer $scm_stock_id
 * @property integer $material_num
 * @property integer $material_id
 */
class ScmStockNum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_stock_num';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scm_stock_id', 'material_num', 'material_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scm_stock_id' => 'Scm Stock ID',
            'material_num' => 'Material Num',
            'material_id' => 'Material ID',
        ];
    }

    /**
     * 获取整包物料信息物料
     * @param int $scmStockId
     * @return string
     */
    public static function getScmStockNum($scmStockId = 0)
    {

        $list = ScmStockNum::find()->where(['scm_stock_id' => $scmStockId])->all();
        if (!$list) {
            return '';
        }
        $materialString = '';
        foreach ($list as $key => $material) {
            $materialInfo = ScmMaterial::find()->where(['id' => $material->material_id])->one();
            if(!isset($materialInfo->weight)){
                continue;
            }
            if ($materialInfo->weight > 0) {
                //供应商：$materialInfo->supplier->name
                $materialString .= "<tr><td>" . $materialInfo->name . "</td><td>" . $materialInfo->weight . $materialInfo->materialType->spec_unit . "</td><td>" . $material->material_num . "</td></tr>";
            } else {
                $materialString .= "<tr><td>" . $materialInfo->name . "</td><td>-</td><td>" . $material->material_num . "</td></tr>";
            }

        }
        return "<table class= 'table table-bordered'><tr><td>物料名称</td><td>规格</td><td>数量</td></tr>" . $materialString . "</table>";

    }

    /**
     * 修改时获取物料的数据
     * @param int $id
     * @return array
     */
    public static function getStockNum($id = 0)
    {
        //获取散料
        $gramArr = array();
        $materialIdStore = array();
        $gramList = ScmStockGram::getStockGram($id);
        foreach ($gramList as $k => $gram) {
            //根据供应商和物料分类查询物料ID
            $materialId = ScmMaterial::getMaterialDetail('id', ['material_type' => $gram['material_type_id'], 'supplier_id' => $gram['supplier_id']]);
            //判断是否是重复的散料数据
            if (!in_array($materialId['id'],$materialIdStore)) {
                $materialIdStore[] = $materialId['id'];
            }
            $gramArr[$materialId['id']] = $gram['material_gram'];
        }

        //获取物料数量
        $stockList = ScmStockNum::find()->where(['scm_stock_id' => $id])->asArray()->all();
        $stockArr = array();
        foreach ($stockList as $key => $stock) {
            if(!in_array($stock['material_id'], $materialIdStore)){
                $materialIdStore[] = $stock['material_id'];
            }
            $stockArr[$stock['material_id']] = $stock['material_num'];
        }
        foreach($materialIdStore as $k => $materialId){
            $store[$k]['material_id'] = $materialId;
            $store[$k]['material_num'] = isset($stockArr[$materialId]) ? $stockArr[$materialId] : '';
            $store[$k]['material_gram'] = isset($gramArr[$materialId]) ? $gramArr[$materialId] : '';
        }
        return $store;
    }

    /**
     * 获取物料的包数
     * @param int $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMaterialNumById($id = 0){
      return ScmStockNum::find()->where(['scm_stock_id' => $id])->asArray()->all();
    }

}
