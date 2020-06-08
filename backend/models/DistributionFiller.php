<?php

namespace backend\models;

use backend\models\ScmMaterial;
use backend\models\ScmMaterialType;
use backend\models\ScmWarehouseOut;
use common\models\Building;
use common\models\WxMember;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "distribution_filler".
 *
 * @property integer $Id
 * @property string $equip_id
 * @property string $material_type
 * @property string $material_id
 * @property string $number
 * @property string $stock_id
 * @property string $distribution_task_id
 */
class DistributionFiller extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    public $orgId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_filler';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_material_author'], 'required'],
            [['equip_id', 'material_type', 'material_id', 'number', 'gram', 'stock_id', 'distribution_task_id'], 'integer'],
            [['create_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'                   => 'ID',
            'equip_id'             => 'Equip ID',
            'material_type'        => 'Material Type',
            'material_id'          => 'Material ID',
            'number'               => 'Number',
            'stock_id'             => 'Stock ID',
            'distribution_task_id' => 'Distribution Task ID',
            'start_time'           => '开始时间',
            'end_time'             => '结束时间',
            'orgId'                => '分公司',
        ];
    }
    /**
     * 获取添料数据
     * @author  zgw
     * @version 2016-08-22
     * @param   array     $where 查询条件
     * @return  array            返回的结果数组
     */
    public static function getFillerList($where)
    {
        return self::find()->where($where)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'add_material_author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(DistributionTask::className(), ['id' => 'distribution_task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    public function getMaterialStock()
    {
        return $this->hasOne(ScmMaterialStock::className(), ['id' => 'stock_id']);
    }

    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type']);
    }

    public static function getTaskFillerList($where = [])
    {
        return self::find()->joinWith('task')->where($where)->asArray()->all();
    }

    /**
     * 获取添料信息
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $taskId 任务ID
     * @return array
     */
    public static function getDistributionFiller($taskId)
    {
        return self::find()
            ->where(['distribution_task_id' => $taskId])
            ->asArray()
            ->all();
    }
    /**
     *  处理物料记录的数组 Excel
     *  @param $param
     **/
    public static function getMaterialRecordExcelArr($param)
    {
        $userId       = Yii::$app->user->identity->id;
        $managerModel = Manager::find()->where(['id' => $userId])->one();
        $query        = DistributionFiller::find();
        if ($managerModel->branch == 1) {
            //    总公司
            $orgId = '';
            if (isset($param['DistributionFiller']['orgId'])) {
                $orgId = $param['DistributionFiller']['orgId'] ? $param['DistributionFiller']['orgId'] : '';
            }
            $buildModel = Building::find()->andFilterWhere(['build_status' => Building::SERVED, 'org_id' => $orgId])->all();
        } else {
            //  分公司
            $buildModel = Building::find()->andWhere(['build_status' => Building::SERVED, 'org_id' => $managerModel->branch])->all();
        }
        $buildIdArr = ArrayHelper::getColumn($buildModel, 'id');

        if (!$buildIdArr) {
            return [];
        }
        if ($param['DistributionFiller']['build_id']) {
            $query->andFilterWhere([
                'build_id' => $param['DistributionFiller']['build_id'],
            ]);
        } else {
            $query->andFilterWhere([
                'build_id' => $buildIdArr,
            ]);
        }
        //日期查询
        if (!empty($param["DistributionFiller"]["start_time"])) {
            $query->andFilterWhere(['>=', 'distribution_filler.create_date', $param["DistributionFiller"]["start_time"]]);
            if (!$param['DistributionFiller']['end_time']) {
                $query->andFilterWhere(['<=', 'distribution_filler.create_date', date("Y-m-d H:i:s")]);
            } else {
                $query->andFilterWhere(['<=', 'distribution_filler.create_date', $param["DistributionFiller"]["end_time"] . ' 23:59:59']);
            }
        }
        $taskFillerArr = $query->all();
        if (!$taskFillerArr) {
            return [];
        }

        return self::getCombinationFillExcelArr($taskFillerArr);
    }

    //处理物料记录的数组 Excel 組合最終的data
    private static function getCombinationFillExcelArr($taskFillerArr)
    {
        $fillerArr      = [];
        $materialNumber = 0;
        foreach ($taskFillerArr as $key => $value) {
            if (!$value->material_id || !$value->build_id || !$value->material_type) {
                continue;
            }

            if ($value->materialType->type == 2) {
                //非物料 杯子
                $materialNumber = $value->number;
            } else {
                //物料
                $materialNumber = $value->material->weight ? $value->material->weight * $value->number : 0;
            }

            if (isset($fillerArr[$value->build->name][$value->material_type])) {
                $fillerArr[$value->build->name][$value->material_type] = $materialNumber + $fillerArr[$value->build->name][$value->material_type];
            } else {
                $fillerArr[$value->build->name][$value->material_type] = $materialNumber;
            }
        }
        return $fillerArr;
    }

    /**
     *  处理物料记录的数组
     *  @param $buildModel, $param
     *  @return array
     **/
    public static function getMaterialRecordArr($buildModel, $param)
    {
        $buildIdArr = ArrayHelper::getColumn($buildModel, 'id');

        $taskFillerArr = [];
        $taskGram      = [];
        foreach ($buildModel as $key => $value) {
            //整包数据
            $query = DistributionFiller::find();
            $query->andFilterWhere([
                'build_id' => $value['id'],
            ]);
            if (isset($param['start_time']) && $param['start_time']) {
                $query->andFilterWhere(['>=', 'create_date', $param["start_time"]]);
            }
            if (isset($param['end_time']) && $param['end_time']) {
                $query->andFilterWhere(['<=', 'create_date', $param["end_time"]]);
            }
            if ($query->all()) {
                $taskFillerArr[$value['id']] = $query->all();
            } else {
                $taskFillerArr[$value['id']] = 0;
            }

            //散料数据
            $gramQuery = DistributionFillerGram::find();
            $gramQuery->andFilterWhere([
                'build_id' => $value['id'],
            ]);
            /*if (isset($param['start_time']) && $param['start_time']) {
            $gramQuery->andFilterWhere(['>=', 'create_date', $param["start_time"]]);
            }
            if (isset($param['end_time']) && $param['end_time']) {
            $gramQuery->andFilterWhere(['<=', 'create_date', $param["end_time"]]);
            }*/

            if ($query->all()) {
                $taskGram[$value['id']] = $gramQuery->all();
            } else {
                $taskGram[$value['id']] = 0;
            }

        }
        if (!$taskFillerArr && !$taskGram) {
            return array();
        }
        //散料数据
        $buildingMaterialInfo = self::getDistributionGram($taskGram);

        return self::getCombinationFillArr($taskFillerArr, $buildingMaterialInfo);
    }

    /**
     * 处理物料记录的数组 , 組合最終的data
     * @author wxl
     * @param array $taskFillerArr
     * @param array $buildingMaterialInfo
     * @return array
     */
    private static function getCombinationFillArr($taskFillerArr = [], $buildingMaterialInfo = [])
    {

        if (!$taskFillerArr) {
            return $buildingMaterialInfo;
        }

        $fillerArr = [];
        foreach ($taskFillerArr as $taskBuildId => $taskFiller) {
            if (!$taskFiller) {
                $fillerArr[$taskBuildId] = 0;
                continue;
            }
            $materialNumber = 0;
            $arr            = [];
            foreach ($taskFiller as $value) {
                if (!$value->material_id || !$value->build_id || !$value->material_type) {
                    continue;
                }
                if ($value->materialType->type == 2) {
                    //非物料 杯子
                    $materialNumber = $value->number;
                } else {
                    //物料
                    $materialNumber = $value->material->weight ? $value->material->weight * $value->number : 0;
                }

                if (isset($arr[$value->material_type])) {
                    $arr[$value->material_type] = $materialNumber + $arr[$value->material_type];
                } else {
                    $arr[$value->material_type] = $materialNumber;
                }

                //散料数据
                $buildingStockGram = isset($buildingMaterialInfo[$value->build_id]) ? $buildingMaterialInfo[$value->build_id] : [];
                if (isset($buildingStockGram[$value->material_type])) {
                    $arr[$value->material_type] = $arr[$value->material_type] + $buildingStockGram[$value->material_type];
                }

            }
            if (isset($buildingMaterialInfo[$taskBuildId])) {
                $moreGram = array_diff($buildingMaterialInfo[$taskBuildId], $arr);
            }

            $fillerArr[$taskBuildId] = isset($moreGram) ? ($arr + $moreGram) : $arr;

        }

        return $fillerArr;
    }

    /**
     * 处理配送任务的散料数据
     * @author wxl
     * @param array $taskGram
     * @return array
     */
    private static function getDistributionGram($taskGram = [])
    {
        if (!$taskGram) {
            return [];
        }
        $fillerArr = [];
        foreach ($taskGram as $buildId => $distributionInfo) {
            if (!$distributionInfo) {
                $fillerArr[$buildId] = 0;
                continue;
            }

            $materialNumber = 0;
            $arr            = [];
            foreach ($distributionInfo as $value) {
                if (!$value->build_id || !$value->material_type_id) {
                    continue;
                }
                $materialNumber = $value->gram;

                if (isset($arr[$value->material_type_id])) {
                    $arr[$value->material_type_id] = $materialNumber + $arr[$value->material_type_id];
                } else {
                    $arr[$value->material_type_id] = $materialNumber;
                }
            }
            $fillerArr[$buildId] = $arr;
        }

        return $fillerArr;
    }

    /**
     *  获取物料类型规格的数组
     **/
    public static function getMaterialTypeSpecificationArr()
    {
        $scmMaterialArr = ScmMaterialType::find()->joinWith('material')->asArray()->all();

        $materialTypeArr = [];
        $materialArr     = [];
        foreach ($scmMaterialArr as $scmMaterialKey => $scmMaterialVal) {
            if ($scmMaterialVal['material']) {
                foreach ($scmMaterialVal['material'] as $key => $value) {
                    $materialTypeArr[$scmMaterialVal['id']][$value['id']]['weight']      = $value['weight'];
                    $materialTypeArr[$scmMaterialVal['id']][$value['id']]['unit']        = $scmMaterialVal['unit'];
                    $materialTypeArr[$scmMaterialVal['id']][$value['id']]['num']         = '数量';
                    $materialTypeArr[$scmMaterialVal['id']][$value['id']]['supplier_id'] = $value['supplier_id'];
                }
            }
        }
        return $materialTypeArr;
    }

    /**
     *  获取出库的数组记录
     *  @param param
     *  @return array
     **/
    private static function getWarehouseOutArr($param)
    {
        $libraryArr = [];
        $query      = ScmWarehouseOut::find()->where(['status' => 3]);
        if ($param['add_material_author']) {
            $query->andFilterWhere([
                'author' => $param['add_material_author'],
            ]);
            foreach ($param['add_material_author'] as $userId) {
                $libraryArr[$userId] = [];
            }

        }
        //日期查询
        if ($param["start_time"]) {
            $query->andFilterWhere(['>=', 'scm_warehouse_out.date', $param["start_time"]]);
        }
        if ($param["end_time"]) {
            $query->andFilterWhere(['<=', 'scm_warehouse_out.date', $param["end_time"]]);
        }

        $warehousOutArr = $query->asArray()->orderBy("material_type_id")->all();

        foreach ($warehousOutArr as $key => $value) {
            $libraryArr[$value['author']][$value['material_type_id']][$value['material_id']] = $value['material_out_num'];
        }
        return $libraryArr;
    }

    /**
     *  获取添加物料的数组记录
     *  @param param
     *  @return array
     **/
    private static function getDistributionFillerArr($param)
    {
        $fillerArr = [];
        $query     = DistributionFiller::find();
        if ($param['add_material_author']) {
            $query->andFilterWhere([
                'add_material_author' => $param['add_material_author'],
            ]);
            foreach ($param['add_material_author'] as $userId) {
                $fillerArr[$userId] = [];
            }
        }
        //日期查询
        if ($param["start_time"]) {
            $query->andFilterWhere(['>=', 'distribution_filler.create_date', $param["start_time"]]);
        }
        if ($param["end_time"]) {
            $query->andFilterWhere(['<=', 'distribution_filler.create_date', $param["end_time"]]);
        }
        $distributionFillerArr = $query->asArray()->orderBy("material_type")->all();
        foreach ($distributionFillerArr as $key => $value) {
            if (!$value['material_id'] || !$value['material_type'] || !$value['add_material_author']) {
                continue;
            }
            $fillerArr[$value['add_material_author']][$value['material_type']][$value['material_id']] = $value['number'];
        }
        return $fillerArr;
    }

    /**
     *  入库的数组记录
     *  @param param
     *  @return array
     **/
    private static function getScmStockArr($param)
    {
        $warehousArr = [];
        $query       = ScmStock::find()->where(['reason' => 2]);
        if ($param['add_material_author']) {
            $query->andFilterWhere([
                'distribution_clerk_id' => $param['add_material_author'],
            ]);
            foreach ($param['add_material_author'] as $userId) {
                $warehousArr[$userId] = [];
            }

        }
        //日期查询
        if ($param["start_time"]) {
            $query->andFilterWhere(['>=', 'scm_stock.ctime', strtotime($param["start_time"])]);
        }
        if ($param["end_time"]) {
            $query->andFilterWhere(['<=', 'scm_stock.ctime', strtotime($param["end_time"] . " 23:59:59")]);
        }
        $scmStockArr = $query->all();
        foreach ($scmStockArr as $key => $value) {
            if (!isset($value->material)) {
                continue;
            }
            $warehousArr[$value->distribution_clerk_id][$value->material->material_type][$value->stockNum->material_id] = $value->stockNum->material_num;
        }
        return $warehousArr;
    }

    /**
     *  整合方法 获取物料对比的数组
     *  @param param
     *  @return array
     **/
    public static function getMaterialComparisonArr($param, $distributionUserList = [], $type = 1)
    {
        $searchArr = [];

        // 配送员查询
        if (isset($param['add_material_author']) && $param['add_material_author']) {
            $searchArr['add_material_author'][] = $param['add_material_author'];
        } else {
            if ($distributionUserList) {
                $searchArr['add_material_author'] = ArrayHelper::getColumn($distributionUserList, 'userid');
            } else {
                $searchArr['add_material_author'] = [];
            }
        }

        // 开始时间查询
        $searchArr['start_time'] = isset($param['start_time']) ? $param['start_time'] : date('Y-m') . '-01';

        // 结束时间查询
        $searchArr['end_time'] = isset($param['end_time']) ? $param['end_time'] : date('Y-m-d');

        $libraryArr  = self::getWarehouseOutArr($searchArr); //出库
        $fillerArr   = self::getDistributionFillerArr($searchArr); //添料
        $warehousArr = self::getScmStockArr($searchArr); //入库

        if ($type == 2) {
            $libraryArr  = array_filter($libraryArr);
            $fillerArr   = array_filter($fillerArr);
            $warehousArr = array_filter($warehousArr);
        }

        $getMaterialComparisonArr = [];
        $surplusArr               = [];
        $diffArr                  = [];
        // 计算出库减去添料的剩余数据
        foreach ($libraryArr as $userId => $materialTypeArr) {
            $getMaterialComparisonArr[$userId]['出库'] = $materialTypeArr; //把出放入数组
            // 初始化添料数组
            $getMaterialComparisonArr[$userId]['添料'] = [];
            // 初始化入库数组
            $getMaterialComparisonArr[$userId]['入库'] = [];
            // 初始化剩余数组
            $getMaterialComparisonArr[$userId]['剩余'] = [];

            if ($materialTypeArr && isset($fillerArr[$userId])) {
                $getMaterialComparisonArr[$userId]['添料'] = $fillerArr[$userId]; // 把添料放入数组
                foreach ($materialTypeArr as $materialTypeId => $materialArr) {
                    if (isset($fillerArr[$userId][$materialTypeId])) {
                        foreach ($materialArr as $materialId => $materialValue) {
                            if (isset($fillerArr[$userId][$materialTypeId][$materialId])) {
                                $diffArr[$userId][$materialTypeId][$materialId] = $materialValue - $fillerArr[$userId][$materialTypeId][$materialId] > 0 ? $materialValue - $fillerArr[$userId][$materialTypeId][$materialId] : 0;
                            } else {
                                $diffArr[$userId][$materialTypeId][$materialId] = $materialValue;
                            }
                        }
                    } else {
                        $diffArr[$userId][$materialTypeId] = $materialArr;
                    }
                }
            } else {
                $diffArr[$userId] = $materialTypeArr;
            }
        }
        // 计算出库减去添料再减去入库的数据
        foreach ($diffArr as $userId => $materialTypeArr) {
            if (isset($warehousArr[$userId]) && !empty($warehousArr[$userId])) {
                $getMaterialComparisonArr[$userId]['入库'] = $warehousArr[$userId]; // 把入库的放入数组
                foreach ($materialTypeArr as $materialTypeId => $materialArr) {
                    if (isset($warehousArr[$userId][$materialTypeId])) {
                        foreach ($materialArr as $materialId => $materialValue) {
                            if (isset($warehousArr[$userId][$materialTypeId][$materialId])) {
                                $getMaterialComparisonArr[$userId]['剩余'][$materialTypeId][$materialId] = $materialValue - $warehousArr[$userId][$materialTypeId][$materialId] > 0 ? $materialValue - $warehousArr[$userId][$materialTypeId][$materialId] : 0;
                            } else {
                                $getMaterialComparisonArr[$userId]['剩余'][$materialTypeId][$materialId] = $materialValue;
                            }
                        }
                    } else {
                        $getMaterialComparisonArr[$userId]['剩余'][$materialTypeId] = $materialArr;
                    }
                }
            } else {
                $getMaterialComparisonArr[$userId]['剩余'] = $materialTypeArr;
            }
        }
        return $getMaterialComparisonArr;
    }

    /**
     * 获取物料消耗总量
     * @author  zgw
     * @version 2016-11-03
     * @return  [type]     [description]
     */
    public static function getMaterialConsumeSum($materialTypeId)
    {
        $threeDate  = date('Y-m-01', strtotime('-3 month'));
        $secondDate = date('Y-m-01', strtotime('-2 month'));
        $oneDate    = date('Y-m-01', strtotime('-1 month'));
        $date       = date('Y-m-01');

        // 获取消耗物料的数据
        $materialConsumeList = self::find()->where(['and', ['material_type' => $materialTypeId], ['>=', 'create_date', $threeDate]])->all();
        // 初始化最近三个月的物料消耗数量
        $threeNum = $secondNum = $oneNum = 0;
        foreach ($materialConsumeList as $materialConsume) {
            if ($materialConsume->create_date >= $threeDate && $materialConsume->create_date < $secondDate) {
                if ($materialConsume->materialType->type == 2) {
                    $threeNum += $materialConsume->number;
                } else {
                    $threeNum += $materialConsume->number * $materialConsume->material->weight / 1000;
                }
            } else if ($materialConsume->create_date >= $secondDate && $materialConsume->create_date < $oneDate) {
                if ($materialConsume->materialType->type == 2) {
                    $secondNum += $materialConsume->number;
                } else {
                    $secondNum += $materialConsume->number * $materialConsume->material->weight / 1000;
                }
            } else if ($materialConsume->create_date >= $oneDate && $materialConsume->create_date < $date) {
                if ($materialConsume->materialType->type == 2) {
                    $oneNum += $materialConsume->number;
                } else {
                    $oneNum += $materialConsume->number * $materialConsume->material->weight / 1000;
                }
            }
        }
        return [$threeNum, $secondNum, $oneNum];

    }

    /**
     * 插入配送记录
     * @author wxl
     * @param array $fillerRecord
     * @return bool
     */
    public static function addDistributionFillerRecord($fillerRecord = [])
    {
        $fillerModel                       = new DistributionFiller();
        $fillerModel->equip_id             = $fillerRecord["equip_id"];
        $fillerModel->build_id             = $fillerRecord['build_id'];
        $fillerModel->material_type        = $fillerRecord['material_type'];
        $fillerModel->stock_id             = $fillerRecord['stock_id'];
        $fillerModel->distribution_task_id = $fillerRecord['distribution_task_id'];
        $fillerModel->add_material_author  = $fillerRecord['add_material_author'];
        $fillerModel->create_date          = date("Y-m-d");
        $fillerModel->material_id          = $fillerRecord['material_id'];
        $fillerModel->number               = intval($fillerRecord['number']);
        return $fillerModel->save();
    }

}
