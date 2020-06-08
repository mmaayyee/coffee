<?php

namespace backend\models;

use backend\models\Organization;
use backend\models\ScmMaterial;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "scm_stock".
 *
 * @property string $id
 * @property integer $company_id
 * @property integer $reason
 * @property integer $distribution_clerk_id
 * @property string $ctime
 */
class ScmStock extends \yii\db\ActiveRecord
{
    const WAIT_SURE = 1;
    const GIVE_BACK = 2;
    public $startTime;
    public $endTime;
    public $date;
    public $material_id;
    public $material_num;
    public $material_gram;
    public $companyReasonArray = array(
        ''  => '请选择',
        '1' => '供应链采购',
        '2' => '配送员归还',
        '3' => '其他原因',
    );
    const DISTRIBUTION_RETURN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'reason'], 'required'],
            [['reason', 'ctime'], 'integer'],
            [['distribution_clerk_id'], 'string'],
            [['material_gram'], 'integer', 'when' => function($model) { return $model->material_gram;}]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'warehouse_id'          => '分库',
            'reason'                => '入库原因',
            'distribution_clerk_id' => '配送员',
            'material_id'           => '物料',
            'material_num'          => '物料数量',
            'ctime'                 => '添加时间',
            'startTime'             => '开始时间',
            'endTime'               => '结束时间',
            'sure_time'             => '审核时间',
            'material_gram'         => '散料数量(克)',
        ];
    }

    /**
     *   入库原因数组
     *   @return array $companyReasonArray
     **/
    public function getCompanyReasonArr()
    {
        $companyReasonArray = ['' => '请选择'];
        if (Yii::$app->user->can('供应链采购')) {
            $companyReasonArray['1'] = '供应链采购';
        }
        if (Yii::$app->user->can('配送员归还')) {
            $companyReasonArray['2'] = '配送员归还';
        }
        if (Yii::$app->user->can('其它原因')) {
            $companyReasonArray['3'] = '其它原因';
        }
        return $companyReasonArray;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'distribution_clerk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyName()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'company_id']);
    }

    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(ScmWarehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     *@return \yii\db\ActiveQuery
     */
    public function getStockNum(){
        return $this->hasOne(ScmStockNum::className(), ['scm_stock_id' => 'id']);
    }

    /**
     *
     *   @return array $companymaterialArr
     **/
    public static function getCompanymaterialArr()
    {
        $companymaterials       = ScmMaterial::find()->all();
        $companymaterialArr     = array();
        $companymaterialArr[''] = '请选择';
        $model                  = new ScmEquipType();
        foreach ($companymaterials as $companymaterial) {
            if ($companymaterial->weight > 0) {
                $companymaterialArr[$companymaterial->id] = '物料名称：' . $companymaterial->name . '，供应商：' . $companymaterial->supplier->name . '，规格：' . $companymaterial->weight . $companymaterial->materialType->spec_unit;
            } else {
                $companymaterialArr[$companymaterial->id] = '物料名称：' . $companymaterial->name . '，供应商：' . $companymaterial->supplier->name;
            }
        }

        return $companymaterialArr;
    }

    /**
     *   配送人员数组
     *   @return array $companyDistriClerkArr
     **/
    public function getDistriClerkArr($org_id = '')
    {
        if ($org_id) {
            $memberArr = WxMember::find()->where(['position' => WxMember::DISTRIBUTION_MEMBER, 'org_id' => $org_id])->asArray()->all();
        } else {
            $memberArr = WxMember::find()->where(['position' => WxMember::DISTRIBUTION_MEMBER])->asArray()->all();
        }

        $companyDistriClerkArr = ['' => '请选择'];
        foreach ($memberArr as $key => $value) {
            $companyDistriClerkArr[$value['userid']] = $value['name'];
        }
        return $companyDistriClerkArr;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanymaterial($id)
    {
        $companymaterials = ScmMaterial::find()->where(['id' => $id])->all();
        if (!$companymaterials) {
            return '';
        }

        $companymaterialArr = array();
        foreach ($companymaterials as $companymaterial) {
            if ($companymaterial->weight > 0) {
                $companymaterialArr[$companymaterial->id] = '物料名称：' . $companymaterial->name . '，供应商：' . $companymaterial->supplier->name . '，规格：' . $companymaterial->weight . $companymaterial->materialType->spec_unit;
            } else {
                $companymaterialArr[$companymaterial->id] = '物料名称：' . $companymaterial->name . '，供应商：' . $companymaterial->supplier->name;
            }
        }

        return $companymaterialArr[$id];
    }

    /**
     *  获取整包的库存信息
     * @param int $warehouseId
     * @return string
     */
    public static function getTotalInventory($warehouseId = 0)
    {
        $materialString = '';
        $inventoryList = ScmTotalInventory::getWarehouseInventory($warehouseId);
        foreach($inventoryList as $inventory){
            $material = ScmMaterial::find()->where(['id' => $inventory['material_id']])->one();
            if ($material->weight > 0) {
                $materialString .= "<tr><td>" . $material->name . "</td><td>" . $material->weight . $material->materialType->spec_unit . "</td><td>" . $inventory['total_number'] . $material->materialType->unit ."</td><td>" . $material->supplier->name ."</td></tr>";
            } else {
                $materialString .= "<tr><td>" . $material->name . "</td><td>-</td><td>" . $inventory['total_number'] . $material->materialType->unit . "</td><td>" . $material->supplier->name ."</td></tr>";
            }
        }

        return "<table class= 'table table-bordered'><tr><td>物料名称</td><td>规格</td><td>数量</td><td>供应商</td></tr>" . $materialString . "</table>";

    }


    /**
     * 根据物料ID组装数据
     * @param int $materialId
     * @param int $num
     * @return string
     */
    public static function getTotalInventoryByMaterialId()
    {
        $query        = ScmTotalInventory::find()->select(['sum(total_number) total_number', 'material_id', 'warehouse_id'])->groupBy('material_id');
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $query->joinWith('warehouse w')->andFilterWhere([
                'w.organization_id' => $managerOrgId,
            ]);
        }
        $totalinventory = $query->asArray()->all(); 

        $materialString = '';
        foreach ($totalinventory as $totalKey => $totalValue) {
            $material = ScmMaterial::find()->where(['id' => $totalValue['material_id']])->one();
            if ($material->weight > 0) {
                $materialString .= "<tr><td>" . $material->name . "</td><td>" . $material->weight . $material->materialType->spec_unit . "</td><td>" . $totalValue['total_number'] . $material->materialType->unit . "</td><td>" . $material->supplier->name . "</td></tr>";
            } else {
                $materialString .= "<tr><td>" . $material->name . "</td><td>-</td><td>" . $totalValue['total_number'] . $material->materialType->unit . "</td><td>" . $material->supplier->name . "</td></tr>";
            }
        }
        return "<table class= 'table table-bordered'><tr><td>物料名称</td><td>规格</td><td>数量</td><td>供应商</td></tr>" . $materialString . "</table>";
    }

    /**
     * 获取符合条件的入库单
     * @author  zgw
     * @version 2016-12-05
     * @param   string     $field 要查询的字段
     * @param   array      $where 查询条件
     * @return  array
     */
    public static function getScmStock($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->asArray()->one();
    }

    /**
     * 获取符合条件的入库单
     * @param string $field
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getScmStockList($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->asArray()->all();
    }

}
