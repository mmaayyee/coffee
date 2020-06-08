<?php

namespace backend\models;

use backend\models\Organization;
use backend\models\ScmSupplier;
use common\models\Building;
use common\models\Equipments;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "distribution_water".
 *
 * @property integer $Id
 * @property string $build_id
 * @property double $surplus_water
 * @property double $need_water
 * @property string $order_time
 * @property string $upload_time
 * @property string $supplier_id
 * @property string $completion_status
 * @property string $distribution_task_id
 */
class DistributionWater extends \yii\db\ActiveRecord
{
    const NO_WATER_ORDER = 0;
    const WAIT_SEND      = 1;
    const ALREADY_SEND   = 2;

    public static $completionStatus = [
        ''                   => '请选择',
        self::NO_WATER_ORDER => '未下单',
        self::WAIT_SEND      => '待配送',
        self::ALREADY_SEND   => '已配送',
    ];

    public $startTime;
    public $endTime;
    public $orgId;
    public $managerOrgId;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_water';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'surplus_water', 'need_water', 'supplier_id'], 'required'],
            [['build_id', 'order_time', 'upload_time', 'supplier_id', 'distribution_task_id', 'completion_status', 'create_time'], 'integer'],
            [['surplus_water', 'need_water'], 'number'],
            ['need_water', 'integer'],
            [['need_water'], 'match', 'pattern' => '/^[1-9]{1}\d{0,1}$/', 'message' => '{attribute}只能为小于100的正整数'],
            [['surplus_water'], 'match', 'pattern' => '/^[0-9]{0,2}(\.[0-9])?$/', 'message' => '{attribute}只能为大于等于0小于100的浮点数'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'                   => 'ID',
            'build_id'             => '楼宇',
            'surplus_water'        => '剩余水量',
            'need_water'           => '需水量',
            'order_time'           => '下单时间',
            'upload_time'          => '送达时间',
            'create_time'          => '创建时间',
            'supplier_id'          => '供水商',
            'distribution_task_id' => '配送任务ID',
            'completion_status'    => '配送状态',
            'startTime'            => '开始下单时间',
            'endTime'              => '结束下单时间',
            'completion_date'      => "日期",
            'orgId'                => '分公司',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipMents()
    {
        return $this->hasOne(Equipments::className(), ['build_id' => 'build_id']);
    }

    /**
     *  水单信息发送（obj 供水商）
     *  @param $model, $transaction
     **/
    public static function sendWaterNews($model)
    {
        //给送水商 发送 订单消息 position
        $buildModel = Building::find()->where(['id' => $model->build_id])->one();
        // 楼宇分公司下的供水商
        $query = ScmSupplier::find()->where(['and',
            ['id' => $model->supplier_id],
            ['like', 'org_id', $buildModel->org_id],
        ]);
        $supplierArr = $query->asArray()->one();
        if (!$supplierArr) {
            return false;
        }
        $orgId    = trim($supplierArr['org_id'], '-');
        $orgIdArr = explode('-', $orgId);

        $query = WxMember::find()->where(['supplier_id' => $model->supplier_id]);
        $query->andFilterWhere(['org_id' => $orgIdArr]);
        $memberArr = $query->asArray()->all();
        foreach ($memberArr as $key => $value) {
            self::sendContentInfo($value['userid']);
        }
        return true;
    }

    /**
     *  发送供水商微信内容
     *  @param memberName
     *
     **/
    public static function sendContentInfo($memberName)
    {
        return SendNotice::sendWxNotice($memberName, 'distribution-water/index', '您有新的水单任务，请注意查收！', Yii::$app->params['water_agentid']);
    }

    /**
     *  批量发送水单时发送消息组成人员数组。
     *  @param($model,$memberNameArr)
     *  @return array
     */
    public static function getMemberNameArr($model, $memberNameArr)
    {
        // 给送水商 发送 订单消息 position
        $buildModel = Building::find()->where(['id' => $model->build_id])->one();
        // 楼宇分公司下的供水商
        $query = ScmSupplier::find()->where(['and',
            ['id' => $model->supplier_id],
            ['like', 'org_id', $buildModel->org_id],
        ]);
        $supplierArr = $query->asArray()->one();
        if (!$supplierArr) {
            return false;
        }
        $orgId    = trim($supplierArr['org_id'], '-');
        $orgIdArr = explode('-', $orgId);

        $query = WxMember::find()->where(['supplier_id' => $model->supplier_id]);
        $query->andFilterWhere(['org_id' => $orgIdArr]);
        $memberArr = $query->asArray()->all();

        foreach ($memberArr as $key => $value) {
            $memberNameArr[] = $value['userid'];
        }
        return array_unique($memberNameArr);
    }

    /**
     *  水单管理中的楼宇列表
     **/
    public static function getDistributionWaterBuildList($type = 1, $orgId = 0)
    {
        $query = self::find()->joinWith(['build b']);
        if ($type == 1) {
            $query->andFilterWhere(['order_time' => 0, 'completion_status' => DistributionWater::NO_WATER_ORDER]);
        } else {
            $query->andFilterWhere(['completion_status' => DistributionWater::ALREADY_SEND]);
        }
        //$query->andFilterWhere(['!=', 'b.build_status', Building::PRE_DELIVERY]);

        $orgId = $orgId ? $orgId : Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->andFilterWhere(['b.org_id' => $orgId]);
        }
        $distributionWaterInfoArr = $query->all();

        $buildList = ['' => '请选择'];
        foreach ($distributionWaterInfoArr as $distributionWaterInfo) {
            if (empty($distributionWaterInfo->build)) {
                continue;
            }
            $buildList[$distributionWaterInfo->build_id] = $distributionWaterInfo->build->name;
        }
        return $buildList;

    }

    /**
     *  通过build_id 查询出对应的楼宇
     *  @param  $buildList
     *  @return
     **/
    public static function getWaterBuildListArr($buildList)
    {
        $waterBuildListArr = [];
        foreach ($buildList as $key => $value) {
            $waterBuildListArr[$value] = Building::getBuildDetail(['id' => $value])['name'];
        }
        return $waterBuildListArr;
    }

    /**
     *  计算获取输入的天数
     *  @param param
     *  @return array
     **/
    public static function getTitleDate($param)
    {
        $month = substr($param['DistributionWater']['completion_date'], -2, 2);

        //计算出每月多少天数
        $thisMonth = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, date("Y")));
        $nextMonth = date("Y-m-d H:i:s", mktime(0, 0, 0, $month + 1, 1, date("Y")));
        $d1        = strtotime($nextMonth);
        $d2        = strtotime($thisMonth);
        $days      = round(($d1 - $d2) / 3600 / 24);

        $titleDate = [];
        for ($i = 1; $i <= $days; $i++) {
            if ($i < 10) {
                $titleDate[] = date("Y") . '-' . $month . '-0' . $i;
            } else {
                $titleDate[] = date("Y") . '-' . $month . '-' . $i;
            }
        }
        return $titleDate;
    }

    /**
     * 返回index控制器中的$data 查询
     * @author  zmy
     * @version 2017-02-18
     * @return  [type]     [description]
     */
    public static function distributionWaterStatisticsIndex($managerModel)
    {
        $dataQuery = Building::find()->alias('b')
            ->leftJoin('equipments e', 'b.id = e.build_id');
        if ($managerModel->branch != 1) {
            //  分公司
            $dataQuery->andFilterWhere(['b.org_id' => Organization::getOrgByWhereIdList(['org_id' => $managerModel->branch])]);
        }
        $dataQuery->andFilterWhere(['b.org_id' => Organization::getOrgByWhereIdList(['organization_type' => Organization::TYPE_ORG])]);
        $data = $dataQuery->andFilterWhere(['in', 'e.operation_status', [Equipments::COMMERCIAL_OPERATION, Equipments::TEMPORARY_OPERATIONS]]);
        // echo $dataQuery->createCommand()->getRawSql();exit();
        return $data;
    }

    /**
     * 返回search控制器中的$data 查询
     * @author  zmy
     * @version 2017-02-18
     * @param   [type]     $managerModel [managerObj]
     * @param   [type]     $param        [搜索条件]
     * @return  [type]                   [description]
     */
    public static function distributionWaterStatisticsSearch($managerModel, $param)
    {
        $dataQuery = Building::find()->alias('b')
            ->leftJoin('equipments e', 'b.id = e.build_id');
        if ($managerModel->branch == 1) {
            //  总公司
            $param['DistributionWater']['orgId'] = $param['DistributionWater']['orgId'] ? $param['DistributionWater']['orgId'] : '';

            if (!empty($param['DistributionWater']['build_id'])) {
                $dataQuery->andFilterWhere(['b.id' => $param['DistributionWater']['build_id']]);
            }
            $dataQuery->andFilterWhere(['b.org_id' => $param["DistributionWater"]['orgId']]);
        } else {
            //  分公司
            if (!empty($param['DistributionWater']['build_id'])) {
                $dataQuery->andWhere(['b.id' => $param['DistributionWater']['build_id']]);
            }
            $dataQuery->andFilterWhere(['b.org_id' => $managerModel->branch]);
        }
        $data = $dataQuery->andFilterWhere(['b.org_id' => Organization::getOrgByWhereIdList(['organization_type' => Organization::TYPE_ORG])]);
        return $data;
    }

    /**
     *  获取设备月用水量统计数据，进行Excel导出
     *  @param $param
     *  @return array
     **/
    public static function getWaterStatisticsExcelArr($param)
    {
        $userId       = Yii::$app->user->identity->id;
        $managerModel = Manager::find()->where(['id' => $userId])->one();

        $titleDate = self::getTitleDate($param);
        $buildArr  = self::buildArrSelect($param, $managerModel);
        $waterArr  = self::waterArrSelect($param);

        $combinArr = self::combinArrSelect($waterArr, $buildArr, $titleDate);

        return $combinArr;
    }

    /**
     * 查询Building 数组
     * @author  zmy
     * @version 2017-02-18
     * @param   [type]     $param        [条件]
     * @param   [type]     $managerModel [managerObj]
     * @return  [type]                   [数组]
     */
    public static function buildArrSelect($param, $managerModel)
    {
        $buildQuery = Building::find()->select("b.id, b.name")->alias('b')
            ->leftJoin('equipments e', 'b.id = e.build_id');
        if ($managerModel->branch == 1) {
            $orgId = '';
            if (isset($param["DistributionWater"]['orgId'])) {
                $orgId = $param['DistributionWater']['orgId'] ? $param['DistributionWater']['orgId'] : '';
            }
            //    总公司
            if (isset($param['DistributionWater']["build_id"]) && $param["DistributionWater"]['build_id']) {
                $buildQuery->andFilterWhere(['b.id' => $param['DistributionWater']["build_id"]]);
            }
            $buildQuery->andFilterWhere(['b.org_id' => $orgId]);
        } else {
            //  分公司
            if (isset($param['DistributionWater']["build_id"]) && $param["DistributionWater"]['build_id']) {
                $buildQuery->andWhere(['b.id' => $param['DistributionWater']["build_id"]]);
            }
            $buildQuery->andFilterWhere(['b.org_id' => $managerModel->branch]);
        }
        $buildQuery->andFilterWhere(['b.org_id' => Organization::getOrgByWhereIdList(['organization_type' => Organization::TYPE_ORG])]);
        //$buildQuery->andFilterWhere(['in', 'e.operation_status', [Equipments::COMMERCIAL_OPERATION, Equipments::TEMPORARY_OPERATIONS]]);
        // echo $buildQuery->createCommand()->getRawSql();exit();
        $buildArr = $buildQuery->asArray()->all();
        return $buildArr;
    }

    /**
     * 组合查询的水单表和楼宇表
     * @author  zmy
     * @version 2017-02-18
     * @param   [type]     $waterArr  [水单数组]
     * @param   [type]     $buildArr  [楼宇数组]
     * @param   [type]     $titleDate [Excel表头]
     * @return  [type]                [组合数组]
     */
    public static function combinArrSelect($waterArr, $buildArr, $titleDate)
    {
        $waterRecordArr = [];
        $combinArr      = [];
        foreach ($waterArr as $key => $value) {
            $waterRecordArr[$value['build_id']][] = $value;
        }
        foreach ($buildArr as $buildK => $buildV) {
            $combinArr[$buildK]['build_name'] = $buildV['name'];
            foreach ($titleDate as $dateV) {
                $combinArr[$buildK][$dateV] = 0;
                if (isset($waterRecordArr[$buildV['id']])) {
                    foreach ($waterRecordArr[$buildV['id']] as $key => $value) {
                        if ($value['completion_date'] == $dateV) {
                            $combinArr[$buildK][$dateV] = $value['count'];
                        }
                    }
                }
            }
        }
        return $combinArr;
    }

    /**
     * 查询水单表中的数组
     * @author  zmy
     * @version 2017-02-18
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function waterArrSelect($param)
    {
        $query = DistributionWater::find()->select(['build_id', 'completion_date', 'sum(need_water) count'])->where(['completion_status' => DistributionWater::ALREADY_SEND]);

        if ($param['DistributionWater']['build_id']) {
            $query->andFilterWhere([
                'build_id' => $param['DistributionWater']['build_id'],
            ]);
        }
        $query->andFilterWhere(['like', 'completion_date', $param["DistributionWater"]['completion_date']]);
        $waterArr = $query->groupBy('build_id, completion_date')->asArray()->all();
        return $waterArr;
    }

    /**
     *  组合 公共的数组 (上下2个设备月用水量统计数据方法公用)
     *  @param ($titleDate, $waterRecordArr)
     *  @return array
     **/
    public static function getCombinationArr($titleDate, $waterRecordArr)
    {
        $waterStatisticsArr = [];
        foreach ($titleDate as $titleDateKey => $titleDateVal) {
            foreach ($waterRecordArr as $waterKey => $waterVal) {
                $waterStatisticsArr[$waterKey][$titleDateVal] = isset($waterVal[$titleDateVal]) ? $waterVal[$titleDateVal] : 0;
            }
        }
        return $waterStatisticsArr;
    }
    /**
     *  获取设备月用水量统计数据
     *  @param $buildModel, $param= ''
     *  @return array;
     **/
    public static function getWaterStatisticsArr($buildModel, $param = '')
    {
        $titleDate = self::getTitleDate($param);
        $buildList = [];
        $waterArr  = [];
        foreach ($buildModel as $value) {
            $query = DistributionWater::find()->select(['build_id', 'completion_date', 'sum(need_water) count'])->where(['completion_status' => DistributionWater::ALREADY_SEND]);
            $query->andFilterWhere([
                'build_id' => $value['id'],
            ]);
            $query->andFilterWhere(['like', 'completion_date', $param["DistributionWater"]['completion_date']]);
            $waterArr[$value["id"]] = $query->groupBy('build_id, completion_date')->asArray()->all();
        }
        $waterRecordArr = [];
        foreach ($waterArr as $buildId => $water) {
            if ($water) {
                foreach ($water as $key => $value) {
                    if (!$value) {
                        $waterRecordArr[$buildId] = 0;
                    } else {
                        $waterRecordArr[$buildId][$value['completion_date']] = $value['count'];
                    }
                }
            } else {
                $waterRecordArr[$buildId] = 0;
            }
        }
        return self::getCombinationArr($titleDate, $waterRecordArr);
    }

    /**
     * 获取任务水单的详细信息
     * @author wxl
     * @param int $distributionTaskId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getWaterInfoByDistributionId($distributionTaskId = 0)
    {
        return self::find()->select('surplus_water,need_water,supplier_id')->where(['distribution_task_id' => $distributionTaskId])->asArray()->one();
    }

}
