<?php

namespace backend\models;

use backend\models\BuildingTaskSetting;
use backend\models\DistributionTaskImgurl;
use backend\models\EquipmentTaskSetting;
use backend\models\EquipSymptom;
use backend\models\EquipWarn;
use backend\models\Holiday;
use backend\models\ScmMaterial;
use backend\models\TemporaryEquipSetting;
use common\dailyTask\Tasks;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\EquipTaskFitting;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "distribution_task".
 *
 * @property string $id
 * @property string $build_id
 * @property string $content
 * @property string $assign_userid
 * @property integer $task_type
 * @property string $creat_time
 * @property string $start_delivery_time
 * @property string $end_delivery_time
 * @property string $remark
 * @property integer $is_sue
 * @property string $result
 *
 * @property WxMember $assignUser
 * @property Building $build
 * @property WxMember $deliveryUser
 */
class DistributionTask extends \yii\db\ActiveRecord
{
    public $build_name;
    public $start_time;
    public $end_time;
    public $orgId;
    // public $end_delivery_date;

    /*
    配送任务类型
    任务类型 1-配送任务 2-维修任务 3-配送维修任务 4-紧急任务
     */

    // 配送
    const DELIVERY = 1;

    // 维修
    const SERVICE = 2;

    // 紧急
    const URGENT = 3;

    // 清洗任务
    const CLEAN = 4;

    // 换料任务
    const REFUEL = 5;

    //未完成
    const NO_FINISH = 1;
    const FINISHED  = 2;
    const ABOLISH   = 3;

    public static $taskResult = [
        1 => '成功',
        2 => '失败',
    ];

    public static $isReplaceProductGroup = [
        1 => '否',
        2 => '是',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_task';
    }

    /**
     *  @return array
     *
     **/
    public static function getTaskTypeList($select = '')
    {
        $arr = [
            ''             => '请选择',
            self::DELIVERY => '配送任务',
            self::SERVICE  => '维修任务',
            self::URGENT   => '紧急任务',
            self::REFUEL   => '换料任务',
            self::CLEAN    => '清洗任务',
        ];
        if (!$select) {
            unset($arr['']);
        }
        return $arr;
    }
    /**
     * 任务状态
     * @return [type] [description]
     */
    public static function getTaskStatus()
    {
        return [
            1 => '未完成',
            2 => '已完成',
            3 => '已作废',
            4 => '已接收',
            5 => '已打卡',
        ];
    }
    /**
     * 运维任务列表按任务状态检索sql拼接
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $status 任务状态
     * @return
     */
    public static function getSearchWhere($status)
    {
        $searchWhereArray = [
            1 => ['!=', 'is_sue', 2], //未完成
            2 => ['is_sue' => 2], //已完成
            3 => ['is_sue' => 3], //已作废
            4 => ['>', 'recive_time', 0], //已接收
            5 => ['>', 'start_delivery_time', 0], //已打卡
        ];
        return $searchWhereArray[$status];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id'], 'required'],
            [['create_time', 'is_sue', 'equip_id', 'result'], 'integer'],
            [['content', 'delivery_task', 'abnormal'], 'string', 'max' => 1000],
            [['assign_userid'], 'string', 'max' => 64],
            [['task_type', 'end_delivery_date'], 'string', 'max' => 32],
            [['remark', 'malfunction_task', 'reading'], 'string', 'max' => 500],
            [['reason'], 'string', 'max' => 100],
            [['is_finish', 'no_finish', 'date', 'count', 'name', 'recive_time', 'start_delivery_time', 'start_longitude', 'start_latitude', 'start_address', 'end_longitude', 'end_latitude', 'end_address'], 'safe'],
            ['build_name', 'in', 'range' => Building::buildNameArray()],
        ];
    }

    /**
     * 加入表中没有的字段
     * @author wangxiwen
     * @version 2018-06-11
     * @return array
     */
    public function attributes()
    {
        $attributes   = parent::attributes();
        $attributes[] = 'is_finish';
        $attributes[] = 'no_finish';
        $attributes[] = 'date';
        $attributes[] = 'count';
        $attributes[] = 'name';
        return $attributes;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                  => 'ID',
            'build_id'            => '楼宇名称',
            'content'             => '任务详情',
            'assign_userid'       => '负责人',
            'task_type'           => '任务类别',
            'create_time'         => '任务创建时间',
            'recive_time'         => '任务接收时间',
            'start_delivery_time' => '任务打卡时间',
            'end_delivery_time'   => '上传物料时间',
            'remark'              => '配送备注',
            'is_sue'              => '是否完成',
            'result'              => '维修结果',
            'build_name'          => '楼宇',
            'malfunction_task'    => '故障现象',
            'delivery_task'       => '配送内容',
            'equip_id'            => '设备',
            'end_delivery_date'   => '任务完成时间',
            'meter_read'          => '电表读数',
            'start_time'          => '开始查询时间',
            'end_time'            => '结束查询时间',
            'start_address'       => '任务开始地址',
            'end_address'         => '任务结束地址',
            'abnormal'            => '异常报警',
            'is_finish'           => '本月台次',
            'no_finish'           => '未完成任务',
            'date'                => '年月',
            'count'               => '设备台次',
            'name'                => '运维人员姓名',
            'road_time'           => '路上时间',
            'task_time'           => '任务用时',
            'equipment_status'    => '运维完成时设备状态',
            'latest_log'          => '运维完成时设备日志',
            'process_result'      => '处理结果',
            'addAmount'           => '物料添加量',
            'overAmount'          => '物料剩余量',
            'changeAmount'        => '物料剩余值修改',
            'wash_time'           => '清洗任务',
            'refuel_time'         => '换料任务',
            'reason'              => '作废原因',
        ];
    }

    /**
     *  task_type
     *
     **/
    public static $taskType = [
        self::DELIVERY => '配送任务',
        self::SERVICE  => '维修任务',
        self::URGENT   => '紧急任务',
        self::REFUEL   => '换料任务',
        self::CLEAN    => '清洗任务',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'assign_userid']);
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
    public function getEquip()
    {
        return $this->hasOne(\common\models\Equipments::className(), ['id' => 'equip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildName()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiller()
    {
        return $this->hasMany(DistributionFiller::className(), ['distribution_task_id' => 'id']);
    }

    /**
     * 备件表
     * @author  zgw
     * @version 2016-09-13
     * @return  [type]     [description]
     */
    public function getFitting()
    {
        return $this->hasMany(EquipTaskFitting::className(), ['task_id' => 'id']);
    }

    public function getImageUrl()
    {
        return $this->hasMany(DistributionTaskImgurl::className(), ['task_id' => 'id']);
    }

    /**
     * 维修内容添加表
     * @author  zgw
     * @version 2016-09-13
     * @return  [type]     [description]
     */
    public function getMaintenance()
    {
        return $this->hasOne(DistributionMaintenance::className(), ['distribution_task_id' => 'id']);
    }

    /**
     * 用户
     * @author  zgw
     * @version 2016-09-13
     * @return  [type]     [description]
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\WxMember::className(), ['userid' => 'assign_userid']);
    }

    /**
     * 将秒转成时分秒格式
     * @author wangxiwen
     * @version 2018-07-10
     * @param int $second 秒
     * return string
     */
    public static function getDateHis($second)
    {
        if ($second <= 0) {
            return '暂无';
        }
        if ($second > 3600) {
            $hours   = intval($second / 3600);
            $minutes = intval(($second % 3600) / 60);
            $time    = $hours . '小时' . $minutes . '分';
        } else {
            $minutes = intval($second / 60);
            $time    = $minutes . '分';
        }
        return $time;
    }
    /**
     * 获取运维人员路上时间
     * @author wangxiwen
     * @version 2018-07-10
     * @param object $model 任务数据
     * return int
     */
    public static function getRoadTime($model)
    {
        //通过任务创建时间获取当天中距离该当前任务最近的一次任务完成时间
        $date            = date('Y-m-d', $model->create_time);
        $startTime       = strtotime($date . ' 00:00:00');
        $endTime         = strtotime($date . ' 23:59:59');
        $endDeliveryDate = self::getEndDeliveryDate($model, $startTime, $endTime);
        if (!empty($endDeliveryDate)) {
            $roadTime = $model->start_delivery_time - strtotime($endDeliveryDate);
        } else {
            $roadTime = $model->start_delivery_time - strtotime(date('Y-m-d', $model->create_time) . ' 09:00:00');
        }
        return $roadTime;
    }
    /**
     * 查询当前任务的最接近的上次完成时间
     * @author wangxiwen
     * @version 2018-07-10
     * @param object $model 任务数据
     * @param int $stratTime 开始时间
     * @param int $endTime 结束时间
     * return string
     */
    public static function getEndDeliveryDate($model, $startTime, $endTime)
    {
        return self::find()
            ->select('end_delivery_date')
            ->andWhere(['>', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime])
            ->andWhere(['is_sue' => 2])
            ->andWhere(['assign_userid' => $model->assign_userid])
            ->andWhere(['!=', 'id', $model->id])
            ->orderBy('start_delivery_time DESC')
            ->limit(1)
            ->scalar();
    }
    /**
     *  添加紧急任务处理方法
     * @author  wxw
     * @version 2018-05-07
     * @return  [type]     [description]
     */
    public static function createTask($param, $model)
    {
        if (!$model->id) {
            //通过楼宇ID查询运维任务表中是否存在未完成的任务
            $model = self::getTaskByBuild($param['build_id']) ?? $model;
        }
        //原运维人员
        $oldDistributionUser  = $model->assign_userid ? $model->assign_userid : $param['assign_userid'];
        $taskType             = !$model->task_type ? '' : $model->task_type;
        $taskType             = strstr($taskType, (string) DistributionTask::URGENT) ? $taskType : $taskType . ',' . DistributionTask::URGENT;
        $taskType             = strpos($taskType, ',') === 0 ? substr($taskType, 1) : $taskType;
        $model->task_type     = self::saveTaskType($taskType);
        $model->content       = $model->id ? $param['content'] : $model->content . ' ' . $param['content'];
        $model->assign_userid = $param['assign_userid'];
        $model->create_time   = $model->create_time ? $model->create_time : time();
        $model->equip_id      = Equipments::find()->where(['build_id' => $param['build_id']])->one()->id;
        $model->build_id      = $model->build_id ? $model->build_id : $param['build_id'];
        $model->is_daily_task = $model->is_daily_task ? $model->is_daily_task : 2;

        if (!$model->save()) {
            Yii::$app->getSession()->setFlash('error', '更新失败,请检测任务');
            return false;
        }
        //需要更新前的运维人员
        $model->assign_userid = $oldDistributionUser;
        return $model;
    }

    /**
     *  添加和更新临时任务处理数据
     *  @param $param, $model, $delivery_task
     *  @return save $ret
     **/
    public static function saveDistributionTask($param, $model, $deliveryTask)
    {
        //过滤临时任务配送数据
        $filterDelivery = self::filterDelivery($deliveryTask);
        //model->id存在是更新提交的数据，否则是添加临时任务和故障任务下发提交的数据
        if (!$model->id) {
            $taskModel     = self::getTaskByBuild($param['build_id']);
            $model         = $taskModel ? $taskModel : $model;
            $delivery      = $model->delivery_task != '' ? Json::decode($model->delivery_task) : [];
            $dailyDelivery = self::getDeliveryTask($delivery, $filterDelivery);
        } else {
            $dailyDelivery = !empty($filterDelivery) ? Json::encode($filterDelivery) : '';
        }
        //原运维人员
        $oldDistributionUser = !empty($model->assign_userid) ? $model->assign_userid : $param['assign_userid'];
        //异常报警
        $abnormal      = !empty($model->abnormal) ? Json::decode($model->abnormal) : [];
        $dailyAbnormal = isset($param['abnormal_id']) && !empty($param['abnormal_id']) ? Json::decode($param['abnormal_id']) : [];
        $abnormalTask  = self::getAbnormal($abnormal, $dailyAbnormal);
        //配送任务类型
        $taskType = $model->task_type ?? '';
        if (!empty($dailyDelivery)) {
            $taskType = strstr($taskType, (string) DistributionTask::DELIVERY) ? $taskType : $taskType . ',' . DistributionTask::DELIVERY;
            $taskType = strpos($taskType, ',') === 0 ? substr($taskType, 1) : $taskType;
        }
        //维修任务类型
        $taskType = !empty($taskType) ? $taskType : '';
        $abnormal = Json::decode($model->abnormal);
        if (!empty($param['malfunction_task']) || !empty($abnormal)) {
            $taskType = strstr($taskType, (string) DistributionTask::SERVICE) ? $taskType : $taskType . ',' . DistributionTask::SERVICE;
            $taskType = strpos($taskType, ',') === 0 ? substr($taskType, 1) : $taskType;
        }
        $malfunction     = $model->malfunction_task != '' ? explode(',', $model->malfunction_task) : [];
        $malfunctionTask = self::getMalfunctionTask($malfunction, $param['malfunction_task']);
        //检测任务类型是否存在清洗任务,不存在则添加
        $taskType = self::saveTaskType($taskType);
        //执行插入或更新操作
        $model->build_id         = trim($param['build_id']);
        $model->equip_id         = Equipments::find()->where(['build_id' => $param['build_id']])->select('id')->scalar();
        $model->content          = $model->content ? $model->content : '默认内容';
        $model->assign_userid    = trim($param['assign_userid']);
        $model->task_type        = (string) $taskType;
        $model->create_time      = $model->create_time ? $model->create_time : time();
        $model->remark           = $model->remark . ' ' . $param['remark'];
        $model->abnormal         = $abnormalTask;
        $model->malfunction_task = $malfunctionTask;
        $model->delivery_task    = $dailyDelivery;
        $model->is_sue           = self::NO_FINISH;
        $model->is_daily_task    = $model->is_daily_task ?? 2;
        $modelRes                = $model->save();
        if (!$modelRes) {
            return $model;
        }
        //需要更新前的运维人员
        $model->assign_userid = $oldDistributionUser;
        return $model;
    }

    /**
     * 过滤临时任务配送数据
     * @author wangxiwen
     * @version 2018-10-24
     * @param array $deliveryArray 配送数据
     * @return
     */
    public static function filterDelivery($deliveryArray)
    {
        $deliveryTask = [];
        foreach ($deliveryArray as $stockCode => $delivery) {
            $packets = isset($delivery['packets']) && $delivery['packets'] != '' ? $delivery['packets'] : 0;
            $gram    = isset($delivery['gram']) && $delivery['gram'] != '' ? $delivery['gram'] : 0;
            if ($packets || $gram) {
                $deliveryTask[$stockCode] = [
                    'material_type_id' => $delivery['material_type_id'],
                    'material_id'      => $delivery['material_id'],
                    'packets'          => (string) $packets,
                    'gram'             => (string) $gram,
                ];
            }
        }
        unset($deliveryArray);
        return $deliveryTask;
    }

    /**
     * 下发日常任务，如果存在该楼宇运维任务时组合故障异常报警
     * @author wangxiwen
     * @version 2018-06-05
     * @param array $abnormalArr 运维任务中的异常报警
     * @param array $dailyAbnormalArr 故障异常报警
     * @return json|string
     */
    public static function getAbnormal($abnormalArr, $dailyAbnormalArr)
    {
        if (empty($abnormalArr) && empty($dailyAbnormalArr)) {
            return '';
        }
        if (!empty($abnormalArr) && empty($dailyAbnormalArr)) {
            return Json::encode($abnormalArr);
        }
        if (empty($abnormalArr) && !empty($dailyAbnormalArr)) {
            return Json::encode($dailyAbnormalArr);
        }
        foreach ($dailyAbnormalArr as $abnormal) {
            if (!in_array($abnormal, $abnormalArr)) {
                array_push($abnormalArr, $abnormal);
            }
        }
        return Json::encode($abnormalArray);
    }

    /**
     *  添加临时任务时处理abnormal字段
     *  @author wangxiwen
     *  @version 2018-05
     *  @param  $param新提交的数据
     *  @param  $model原有数据
     *  @return string
     **/
    public static function getAbnormalTask($param, $model)
    {
        //判断添加临时任务时是否存在异常报警（存在代表故障任务下发，不存在代表添加临时任务）
        if (array_key_exists('abnormal_id', $param)) {

            if ($param['abnormal_id'] && $model->abnormal) {
                //对原数据中abnormal字段更新
                $newAbnormal = Json::decode($param['abnormal_id']);
                $oldAbnormal = Json::decode($model->abnormal);
                foreach ($newAbnormal as $abnorlmal) {
                    if (!in_array($abnorlmal, $oldAbnormal)) {
                        array_push($oldAbnormal, $abnorlmal);
                    }
                }
                return Json::encode($oldAbnormal);
            } elseif ($param['abnormal_id'] && !$model->abnormal) {
                return $param['abnormal_id'];
            } else {
                return Json::encode([]);
            }
        } else {
            return !$model->abnormal ? Json::encode([]) : $model->abnormal;
        }
    }
    /**
     * 下发日常任务，如果存在该楼宇运维任务时组合故障客服上报
     * @author wangxiwen
     * @version 2018-06-05
     * @param string $malfunctionArr 运维任务中的客服上报
     * @param string $dailyMalfunctionArr 故障客服上报
     * @return json|string
     */
    public static function getMalfunctionTask($malfunctionArr, $dailyMalfunctionArr)
    {
        if (empty($malfunctionArr) && empty($dailyMalfunctionArr)) {
            return '';
        }
        if (!empty($malfunctionArr) && empty($dailyMalfunctionArr)) {
            return implode(',', $malfunctionArr);
        }
        if (empty($malfunctionArr) && !empty($dailyMalfunctionArr)) {
            return implode(',', $dailyMalfunctionArr);
        }
        foreach ($dailyMalfunctionArr as $malfunction) {
            if (!in_array($malfunction, $malfunctionArr)) {
                array_push($malfunctionArr, $malfunction);
            }
        }
        return implode(',', $malfunctionArr);
    }

    /**
     *  添加临时任务时处理delivery_task字段
     *  @author wangxiwen
     *  @version 2018-10-24
     *  @param string $deliveryArr 运维任务中原有的配送数据
     *  @param array $dailyDeliveryArr 日常任务中的配送数据或临时任务新增的配送数据
     *  @return
     **/
    public static function getDeliveryTask($deliveryArr = [], $dailyDeliveryArr = [])
    {
        if (empty($deliveryArr) && empty($dailyDeliveryArr)) {
            return '';
        }
        if (!empty($deliveryArr) && empty($dailyDeliveryArr)) {
            return Json::encode($deliveryArr);
        }
        if (empty($deliveryArr) && !empty($dailyDeliveryArr)) {
            return Json::encode($dailyDeliveryArr);
        }
        foreach ($dailyDeliveryArr as $stockCode => $delivery) {
            if (!empty($deliveryArr[$stockCode])) {
                $deliveryArr[$stockCode]['packets'] += $delivery['packets'];
                if (!isset($deliveryArr[$stockCode]['gram'])) {
                    $deliveryArr[$stockCode]['gram'] = $delivery['gram'];
                } else {
                    $deliveryArr[$stockCode]['gram'] += $delivery['gram'];
                }
            } else {
                $deliveryArr[$stockCode]['packets'] = $delivery['packets'];
                $deliveryArr[$stockCode]['gram']    = $delivery['gram'];
            }
            $deliveryArr[$stockCode]['material_type_id'] = $delivery['material_type_id'];
            $deliveryArr[$stockCode]['material_id']      = $delivery['material_id'];
        }
        return Json::encode($deliveryArr);
    }
    /**
     * 添加和修改运维任务时编辑任务类型
     * @author wangxiwen
     * @version 2018-07-12
     * @param string $taskType 任务类型字符串
     * @return string
     */
    public static function saveTaskType($taskType)
    {
        if (!$taskType) {
            return (string) DistributionTask::CLEAN;
        }
        $taskType = explode(',', $taskType);
        if (!in_array(self::CLEAN, $taskType)) {
            array_push($taskType, self::CLEAN);
        }
        sort($taskType);
        $taskType = implode(',', $taskType);
        return $taskType;
    }

    /**
     * 获取未完成的运维任务
     * @author wangxiwen
     * @version 2018-06-04
     * @param array $where 查询条件
     * @return array
     */
    public static function getTaskList()
    {
        $taskArray = self::find()
            ->where(['is_sue' => self::NO_FINISH])
            ->all();
        $taskList = [];
        foreach ($taskArray as $task) {
            $taskList[$task->build_id] = $task;
        }
        return $taskList;
    }
    /**
     *  添加配送任务
     *  @param ($param, $model, $delivery_task, $malfunctionTaskSign, $deliverySign)
     **/
    public static function JudgmentTaskType($param)
    {
        $model                = self::find()->where(['build_id' => $param['build_id'], 'is_sue' => self::NO_FINISH])->one();
        $model                = $model ? $model : new self();
        $model->task_type     = (string) DistributionTask::DELIVERY;
        $model->delivery_task = '';
        $model->create_time   = time();
        $model->assign_userid = trim($param['assign_userid']);
        $model->build_id      = trim($param['build_id']);
        $model->content       = '默认内容';
        $model->equip_id      = Equipments::find()->where(['build_id' => $param['build_id']])->one()->id;
        if (!$model->save()) {
            Yii::$app->getSession()->setFlash('error', '添加失败,请检测任务');
            return false;
        }
        return true;
    }

    /**
     * 批量添加数据
     * @author  zgw
     * @version 2016-08-13
     * @param   [type]     $data [description]
     */
    public static function addAll($data)
    {
        return Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['create_time', 'content', 'assign_userid', 'remark', 'is_daily_task', 'build_id', 'equip_id', 'task_type', 'delivery_task'], $data)->execute();
    }

    /**
     * 获取蓝牙秤初始化数据
     * @author wangxiwen
     * @version 2018-06-14
     * @params int $userId 用户ID
     * return array
     */
    public static function getBluetoothBalanceInitData($userId)
    {
        return self::find()
            ->alias('dt')
            ->leftJoin('building b', 'dt.build_id = b.id')
            ->leftJoin('equipments e', 'e.build_id = b.id')
            ->select('b.name build_name,b.id build_id,dt.delivery_task,e.id equip_id,dt.id task_id,dt.reading')
            ->andWhere(['dt.assign_userid' => $userId])
            ->andWhere(['>', 'dt.start_delivery_time', 0])
            ->andWhere(['dt.is_sue' => self::NO_FINISH])
            ->asArray()
            ->one();
    }
    /**
     * 获取物料信息数据
     * @author wangxiwen
     * @version 2018-06-14
     * @param int $equipId 设备ID
     * @return array
     */
    public static function getMaterialInfoList($equipId)
    {
        //获取产品组信息ID
        $equipCode = Equipments::getEquipCode($equipId);
        //获取产品组料仓信息
        $equipGroupInfo = Api::getEquipProductGroupStockInfo('equip-product-group-stock-info', '&equipCode=' . $equipCode);
        //获取放入料仓中物料
        $stockMaterialInfo = ScmMaterialType::getMaterialTypeStock();
        //获取物料信息
        $scmMaterialInfo = ScmMaterial::getScmMaterial();
        $scmMaterialList = [];
        foreach ($equipGroupInfo as $group) {
            $materialTypeId = $group['material_type_id'];
            $stockCode      = $group['stock_code'];
            if (empty($stockMaterialInfo[$materialTypeId])) {
                continue;
            }
            $scmMaterialList[$stockCode]['material_type_id'] = $materialTypeId;
            $scmMaterialList[$stockCode]['stock_code']       = $stockCode;
            $scmMaterialList[$stockCode]['material_name']    = $scmMaterialInfo[$materialTypeId]['material_name'] ?? '';
            $scmMaterialList[$stockCode]['weight']           = $scmMaterialInfo[$materialTypeId]['weight'] ?? '';

        }
        return $scmMaterialList;
    }

    /**
     * 获取楼宇误差值
     * @author wangxiwen
     * @version 2018-06-14
     * @param int $equipId 设备ID
     */
    public static function getErrorValue($equipId)
    {
        $equipInfo = self::getEquipments($equipId);
        //优先查找楼宇日常任务表
        $buildErrorValue = BuildingTaskSetting::find()->where(['building_id' => $equipInfo['build_id']])->select('error_value')->asArray()->one();
        if (!empty($buildErrorValue) && $buildErrorValue['error_value'] > 0) {
            return $buildErrorValue['error_value'];
        }
        $equipErrrValue = EquipmentTaskSetting::find()->where(['equipment_type_id' => $equipInfo['equip_type_id'], 'organization_id' => $equipInfo['org_id']])->select('error_value')->asArray()->one();
        if (!empty($equipErrrValue) && $equipErrrValue['error_value'] > 0) {
            return $equipErrrValue['error_value'];
        }
        return 0;
    }

    /**
     * 获取设备信息
     * @author wangxiwen
     * @version 2018-06-14
     * @param $equipId 设备ID
     * @return array
     */
    private static function getEquipments($equipId)
    {
        //通过设备ID获取楼宇ID,分公司ID和设备类型ID
        return Equipments::find()
            ->select('equip_type_id,build_id,org_id')
            ->andWhere(['id' => $equipId])
            ->asArray()
            ->one();
    }

    /**
     * 获取设备料仓剩余物料
     * @author wangxiwen
     * @version 2018-06-15
     * @param int $equipCode 设备编号
     * return array
     */
    public static function getEquipmentVolumesArr($equipCode)
    {
        return Api::getBase('stock-surplus-material', '&equipCode=' . $equipCode);
    }
    /**
     * 更新设备料仓剩余物料
     * @author wangxiwen
     * @version 2018-06-16
     * @param array $surplusMaterialList 剩余物料信息
     * return array
     */
    public static function SaveSurplusMaterial($surplusMaterialList)
    {
        return Api::postBaseSurplusMaterial('save-surplus-material', $surplusMaterialList);
    }

    /**
     * 获取设备空料盒重量
     * @author wangxiwen
     * @version 2018-06-27
     * @param int $equipId 设备ID
     * @return array 格式 stockId=>weight
     */
    public static function getEmptyBoxWeight($equipId)
    {
        return ScmEquipType::find()
            ->alias('st')
            ->leftJoin('equipments e', 'st.id = e.equip_type_id')
            ->where(['e.id' => $equipId])
            ->select('empty_box_weight')
            ->scalar();
    }

    /**
     *  获取开箱签到的数组
     *  @param $param
     *  @return array
     **/
    public static function getSignBoxExcelArr($param)
    {
        $query = self::find();
        $query->andFilterWhere([
            'task_type' => [1, 3],
            'is_sue'    => 2,

        ]);
        if ($param) {
            $query->andFilterWhere([
                'build_id'          => $param['build_id'],
                'end_delivery_date' => $param['end_delivery_date'],
                'assign_userid'     => $param['assign_userid'],
            ]);
        }

        $managerOrgId = Manager::getManagerBranchID();
        $orgId        = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : $managerOrgId;
        if ($orgId > 1) {
            $query->joinWith('assignUser u')->andFilterWhere(['u.org_id' => $orgId]);
        }

        $taskAssocArr = $query->all();

        return self::getSignBoxArr($taskAssocArr);
    }

    public static function getSignBoxArr($distributionTaskModel)
    {
        $taskArr = [];
        if ($distributionTaskModel) {
            foreach ($distributionTaskModel as $taskAssocKey => $taskAssocVal) {
                $taskArr[$taskAssocKey]['build_id']            = $taskAssocVal->build->name;
                $taskArr[$taskAssocKey]['assign_userid']       = $taskAssocVal->assignUser->name;
                $taskArr[$taskAssocKey]['start_delivery_time'] = $taskAssocVal['start_delivery_time'];
                $taskArr[$taskAssocKey]['end_delivery_time']   = $taskAssocVal['end_delivery_time'];
                foreach ($taskAssocVal->filler as $key => $value) {
                    if ($value->materialType->type == 2) {
                        // 非物料 杯子
                        $taskArr[$taskAssocKey]['filler'][$value->material_type] = $value->material->weight ? $value->number : $value->number;
                    } else {
                        // 物料
                        if (isset($taskArr[$taskAssocKey]['filler'][$value->material_type])) {
                            $taskArr[$taskAssocKey]['filler'][$value->material_type] += $value->material->weight ? $value->material->weight * $value->number : '0';
                        } else {
                            $taskArr[$taskAssocKey]['filler'][$value->material_type] = $value->material->weight ? $value->material->weight * $value->number : '0';
                        }
                    }
                }
            }
        }
        return $taskArr;
    }

    /**
     * 获取配送任务数据
     * @author  zgw
     * @version 2016-08-25
     * @param   array     $where 查询条件
     * @return  array            配送任务列表
     */
    public static function getDistributionTaskList($where)
    {
        return self::find()->where($where)->all();
    }
    /**
     * 根据楼宇获取未完成任务数据
     * @author  wangxiwen
     * @version 2018-10-24
     * @param   int $buildId 楼宇ID
     * @return
     */
    public static function getTaskByBuild($buildId)
    {
        return self::find()->orderBy('id DESC')
            ->where(['build_id' => $buildId, 'is_sue' => 1])
            ->one();
    }

    /**
     * 获取任务统计数据
     * @author  zgw
     * @version 2016-10-12
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function getTaskData($param)
    {
        $syncData = [];
        // 初始化全国和各分公司的所需数据
        $orgIdList = Organization::getAllOrgName();
        foreach ($orgIdList as $orgName) {
            $syncData[$orgName]['totalTime']  = 0;
            $syncData[$orgName]['repairTime'] = 0;
            $syncData[$orgName]['taiCi']      = 0;
            $syncData[$orgName]['userId']     = [];
            $syncData[$orgName]['userCount']  = 0;
        }

        // 获取已完成的任务
        $query = self::find()->where(['is_sue' => 2]);
        // 日期查询
        if ($param["start_delivery_time"]) {
            $query->andFilterWhere(['>=', 'end_delivery_date', $param["start_delivery_time"]]);
        }
        if ($param["end_delivery_time"]) {
            $query->andFilterWhere(['<=', 'end_delivery_date', $param["end_delivery_time"]]);
        }
        // 默认查询
        if (!$param["start_delivery_time"] && !$param["end_delivery_time"]) {
            $query->andFilterWhere(['>=', 'end_delivery_date', date('Y-m') . '-01']);
        }
        // 获取符合条件的任务数据
        $taskList = $query->all();
        if ($taskList) {
            $syncData = self::getTaskSyncData($taskList, $syncData);
        }
        // 计算配送人员做的设备维修任务数据
        $equipTaskList = self::getEquipTaskList($param["start_delivery_time"], $param["end_delivery_time"]);
        if ($equipTaskList) {
            $syncData = self::getEquipTaskSyncData($equipTaskList, $syncData);
        }

        return $syncData;
    }
    /**
     * 计算配送工作统计
     * @author  zgw
     * @version 2016-10-12
     * @param   [type]     $taskList [description]
     * @param   [type]     $syncData [description]
     * @return  [type]               [description]
     */
    private static function getTaskSyncData($taskList, $syncData)
    {
        foreach ($taskList as $taskObj) {
            $totalDiffTime = $repairDiffTime = 0;
            // 计算本次任务总时长
            $totalDiffTime = $taskObj->end_delivery_time - $taskObj->start_delivery_time;
            $totalDiffTime = $totalDiffTime > 0 ? $totalDiffTime : 0;
            // 计算维修任务时长
            // 1、存维修任务
            if ($taskObj->task_type == 2) {
                $repairDiffTime = $totalDiffTime;
            }
            // 2、配送加维修任务（只去维修所需时间）
            if ($taskObj->task_type == 3 && isset($taskObj->maintenance)) {
                $repairDiffTime = $taskObj->maintenance->end_repair_time - $taskObj->maintenance->start_repair_time;
                $repairDiffTime = $repairDiffTime > 0 ? $repairDiffTime : 0;
            }

            if (array_key_exists('北京总部', $syncData)) {
                // 计算总公司任务时长以及台次
                $syncData['北京总部']['totalTime'] += $totalDiffTime;
                $syncData['北京总部']['repairTime'] += $repairDiffTime;
                $syncData['北京总部']['taiCi'] += 1;
            }
            $orgModel = Api::getOrgDetailsModel(['org_id' => $taskObj->build->org_id]);
            $orgName  = $orgModel['org_name'];
            if (array_key_exists($orgName, $syncData)) {
                // 计算分公司的总任务时长
                $syncData[$orgName]['totalTime'] += $totalDiffTime;
                $syncData[$orgName]['repairTime'] += $repairDiffTime;
                $syncData[$orgName]['taiCi'] += 1;
            }
            // 计算总公司的总人数
            if (array_key_exists('北京总部', $syncData) && !in_array($taskObj->assign_userid, $syncData['北京总部']['userId'])) {
                $syncData['北京总部']['userId'][] = $taskObj->assign_userid;
                $syncData['北京总部']['userCount'] += 1;
            }
            // 计算分公司的总人数
            if (array_key_exists($orgName, $syncData) && !in_array($taskObj->assign_userid, $syncData[$orgName]['userId'])) {
                $syncData[$orgName]['userId'][] = $taskObj->assign_userid;
                $syncData[$orgName]['userCount'] += 1;
            }
        }
        return $syncData;
    }

    /**
     * 获取设备维修任务列表
     * @author  zgw
     * @version 2016-11-30
     * @param   [type]     $startTime [description]
     * @param   [type]     $endTime   [description]
     * @return  [type]                [description]
     */
    public static function getEquipTaskList($startTime, $endTime)
    {
        $query = EquipTask::find();
        // 日期查询
        if ($startTime) {
            $query->andFilterWhere(['>=', 'end_repair_time', strtotime($startTime)]);
        }
        if ($endTime) {
            $query->andFilterWhere(['<=', 'end_repair_time', strtotime($endTime)]);
        }
        // 默认查询
        if (!$startTime && !$endTime) {
            $query->andFilterWhere(['>=', 'end_repair_time', strtotime(date('Y-m') . '-01')]);
        }
        // 获取符合条件的任务数据
        return $query->all();
    }

    /**
     * 获取设备任务数据统计
     * @author  zgw
     * @version 2016-11-30
     * @param   [type]     $taskList [description]
     * @param   [type]     $syncData [description]
     * @return  [type]               [description]
     */
    public static function getEquipTaskSyncData($taskList, $syncData)
    {
        foreach ($taskList as $taskObj) {
            $totalDiffTime = 0;
            if (isset($taskObj->assignMemberName->position) && !in_array($taskObj->assignMemberName->position, WxMember::$disPositionArr)) {
                continue;
            }
            // 计算本次任务总时长
            $totalDiffTime = $taskObj->end_repair_time - $taskObj->start_repair_time;
            $totalDiffTime = $totalDiffTime > 0 ? $totalDiffTime : 0;
            if (array_key_exists('北京总部', $syncData)) {
                // 计算总公司任务时长以及台次
                $syncData['北京总部']['totalTime'] += $totalDiffTime;
                $syncData['北京总部']['repairTime'] += $totalDiffTime;
                $syncData['北京总部']['taiCi'] += 1;
            }
            $orgModel = Api::getOrgDetailsModel(['org_id' => $taskObj->build->org_id]);
            $orgName  = $orgModel['org_name'];
            if (array_key_exists($orgName, $syncData)) {
                // 计算分公司的总任务时长
                $syncData[$orgName]['totalTime'] += $totalDiffTime;
                $syncData[$orgName]['repairTime'] += $totalDiffTime;
                $syncData[$orgName]['taiCi'] += 1;
            }
            // 计算总公司的总人数
            if (array_key_exists('北京总部', $syncData) && !in_array($taskObj->assign_userid, $syncData['北京总部']['userId'])) {
                $syncData['北京总部']['userId'][] = $taskObj->assign_userid;
                $syncData['北京总部']['userCount'] += 1;
            }
            // 计算分公司的总人数
            if (array_key_exists($orgName, $syncData) && !in_array($taskObj->assign_userid, $syncData[$orgName]['userId'])) {
                $syncData[$orgName]['userId'][] = $taskObj->assign_userid;
                $syncData[$orgName]['userCount'] += 1;
            }
        }
        return $syncData;
    }

    /**
     *  数字对应的字母列表
     *
     **/
    public static function getExcelConversionLetter($num)
    {
        $arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            "AA", 'AB', 'AC', 'AD', 'AE', 'AF', "AG", 'AH', 'AI', 'AJ', 'AK', 'Al', 'AM', 'AN', 'AO', 'AP', "AQ", 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        );
        return $arr[$num];
    }

    /**
     * 配送任务配送内容
     * @author  zgw
     * @version 2016-10-26
     * @param   [type]     $equipId             [description]
     * @param   [type]     $distributionContent [description]
     * @return  [type]                          [description]
     */
    public static function equipMaterialTypeArr($equipId, $distributionContent)
    {
        if (!$equipId) {
            return '';
        }
        // 获取设备编号
        $equipCode = Equipments::getEquipCode($equipId);
        // 获取产品组料仓和物料分类对应关系
        $productGroupStockInfo = Api::getEquipProductGroupStockInfo('get-product-group-stock-info', '&equipCode=' . $equipCode);
        if (empty($productGroupStockInfo)) {
            return '';
        }
        //获取设备配置信息
        $equipSetting = TemporaryEquipSetting::getTemporaryEquipSetting($equipCode);
        //获取当前时间后30天的节假日
        $futureHoliday = Holiday::getFutureHoliday();
        //获取物料详情
        $scmMaterial = ScmMaterial::getScmMaterial();
        $equipConfig = [
            $productGroupStockInfo,
            $equipSetting,
            $futureHoliday,
            $scmMaterial,
        ];
        //获取最终展示数据
        return self::getDistributionContent($equipConfig);
    }

    /**
     * 获取配送内容数据
     * @author  wangxiwen
     * @version 2018-10-29
     * @param   array $equipConfig 数据集合
     * @return
     */
    public static function getDistributionContent($equipConfig)
    {
        list($productGroupStockInfo, $equipSetting, $futureHoliday, $scmMaterial) = $equipConfig;

        $content = '';
        // 根据设备具有的物料分类获取物料信息
        foreach ($productGroupStockInfo as $stockInfo) {
            // 获取物料分类ID
            $materialTypeId    = $stockInfo['material_type_id'];
            $scmMaterialDetail = $scmMaterial[$materialTypeId] ?? [];
            if (empty($scmMaterialDetail)) {
                continue;
            }
            $materialTypeName = $scmMaterialDetail['material_type_name'];
            $materialId       = $scmMaterialDetail['material_id'];
            $materialName     = $scmMaterialDetail['material_name'];
            $weight           = $scmMaterialDetail['weight'];
            $supplierName     = $scmMaterialDetail['name'];
            $specUnit         = $scmMaterialDetail['spec_unit'];
            $unit             = $scmMaterialDetail['unit'];
            $weightUnit       = $scmMaterialDetail['weight_unit'];
            $type             = $scmMaterialDetail['type'];
            //物料单位
            $materialUnit = $type == 1 ? $weightUnit : $unit;
            //料仓编号
            $stockCode = $stockInfo['stock_code'];
            // 料仓剩余物料
            $surplusMaterial = $stockInfo['surplus_material'];
            //工作日平均消耗
            $workConsume = $equipSetting[$stockCode]['work_consumption'] ?? 0;
            //节假日平均消耗
            $holidayConsume = $equipSetting[$stockCode]['holiday_consumption'] ?? 0;
            //料仓上限
            $stockVolumeBound = $equipSetting[$stockCode]['stock_volume_bound'] ?? 0;
            //配送周期
            $dayNum = $equipSetting[$stockCode]['day_num'] ?? 0;
            //获取配送周期内物料总消耗
            $allConsume = Tasks::getConsumeTotal($workConsume, $holidayConsume, $dayNum, $futureHoliday, 1);
            // 添加量
            $feedConsume = $allConsume + $surplusMaterial > $stockVolumeBound ? $stockVolumeBound - $surplusMaterial : $allConsume;
            // 最终读数
            $overReading = $feedConsume + $surplusMaterial;

            $content .= '<div class="form-group" style="width:48%;display:inline-block ;">';
            // 物料分类名称
            $content .= '<span class="ys">' . $materialTypeName . '</span>';
            // 该分类的物料列表
            $content .= '<select class="form-control fc" id="select_' . $stockCode . '" name="delivery_task[' . $stockCode . '][material_id] ">';

            // 判断物料是否有规格有则显示规格，没有则不显示
            $materialInfo = $specUnit ? $supplierName . '--' . $weight . $specUnit : $supplierName;

            // 展示物料列表
            $content .= '<option value="' . $materialId . '">' . $materialInfo . '</option>';

            $content .= '</select>';

            $content .= '<input type="hidden" name="delivery_task[' . $stockCode . '][material_type_id]" value="' . $materialTypeId . '">';
            // 修改时显示原来填写的物料数量
            $packets = $distributionContent[$stockCode]['packets'] ?? '';
            $gram    = $distributionContent[$stockCode]['gram'] ?? '';
            // 物料数量和单位
            $content .= '<input class="form-control f2 check-info" type="text" id="input_' . $stockCode . '" name="delivery_task[' . $stockCode . '][packets]" value="' . $packets . '" /><span>' . $unit . '</span>';
            if ($unit == '包') {
                $content .= '<input class="form-control f2 check-info" type="text" id="gram_' . $stockCode . '" name="delivery_task[' . $stockCode . '][gram]" value="' . $gram . '" /><span>克</span>';
            }
            $content .= '<br>料仓剩余物料：' . $surplusMaterial . $materialUnit . '  添加量：' . $feedConsume . $materialUnit . '  最终读数：' . $overReading . $materialUnit;
            $content .= '<div style="margin-left:8%;" class="help-block"></div></div>';
        }
        return $content;
    }

    /**
     * 企业微信任务打卡页面添加物料展示
     * @author  wangxiwen
     * @version 2018-06-25
     * @param   int $taskId 任务ID
     * @return  string
     */
    public static function getDeliveryShowData($taskId)
    {
        $scmMaterial     = ScmMaterial::getScmMaterial();
        $deliveryTaskArr = self::getTaskDelivery($taskId);
        if (!$deliveryTaskArr) {
            return '';
        }
        $deliveryTask = Json::decode($deliveryTaskArr);
        $content      = '';
        foreach ($deliveryTask as $delivery) {
            $materialTypeId = $delivery['material_type_id'];
            $showMaterial   = '';
            $materialDetail = $scmMaterial[$materialTypeId] ?? '';
            if (!$materialDetail) {
                continue;
            }
            $type             = $materialDetail['type']; //是否放入料仓中的物料 1是2否
            $materialTypeName = $materialDetail['material_type_name'];
            $specUnit         = $materialDetail['spec_unit'];
            $unit             = $materialDetail['unit'];
            $weight           = $materialDetail['weight'];
            $weigthUnit       = $materialDetail['weight_unit'];
            $packets          = $delivery['packets'];
            $gram             = $delivery['gram'];
            if ($type == 1) {
                $showSpecUnit = '-' . $weight . $specUnit;
                $showGram     = $gram && $gram > 0 ? $gram . $weigthUnit : '';
            } else {
                $showSpecUnit = $weight > 1 ? '-' . $weight . $specUnit : '';
                $showGram     = '';
            }
            $showPackets = $packets && $packets > 0 ? $packets . $unit : '';
            $showMaterial .= $materialTypeName . $showSpecUnit . ' : ' . $showPackets;

            $content .= '<tr><td>' . $showMaterial . $showGram . "</td></tr>";
        }
        $tr = '<table>' . $content . '</table>';

        return $tr;
    }

    /**
     * 企业微信任务打卡页面添加物料展示
     * @author  wangxiwen
     * @version 2018-06-25
     * @param   int $taskId 任务ID
     * @return  string
     */
    public static function getTaskDelivery($taskId)
    {
        return self::find()->select('delivery_task')->where(['id' => $taskId])->scalar();
    }

    /**
     * 获取任务详情
     * @author  zgw
     * @version 2016-09-13
     * @param   [type]     $where [description]
     * @param   [type]     $stratTime [description]
     * @param   [type]     $endTime [description]
     * @return  [type]            [description]
     */
    public static function getDetail($where, $stratTime = [], $endTime = [])
    {
        $query = self::find()->where($where);
        if (!empty($stratTime)) {
            $query->andFilterWhere($stratTime);
        }
        if (!empty($endTime)) {
            $query->andFilterWhere($endTime);
        }
        return $query->one();
    }

    /**
     *  获取配送
     *  @param $taskId
     *
     **/
    public static function getDistributionData($taskId)
    {
        $taskModel       = self::find()->where(['id' => $taskId])->one();
        $deliveryTaskArr = json_decode($taskModel->delivery_task, true);
        if (!$deliveryTaskArr) {
            return "";
        }
        $tr = '';
        foreach ($deliveryTaskArr as $key => $value) {
            $materialObj = ScmMaterial::getMaterialObj(['id' => $value['material_id']]);

            $tr .= "<tr><td>" . $materialObj->materialType->material_type_name . "/" . $materialObj->materialType->unit . "</td><td>" . $materialObj->name . "</td><td>" . $value['packets'] . "</td></tr>";
        }
        return "<table class= 'table table-bordered'><tr><td>物料分类</td><td>物料名称</td><td>物料添加量</td></tr>" . $tr . "</table>";
    }

    /**
     *  获取维修的数据
     *  @param $taskId
     *
     **/
    public static function getMaintenanceData($taskId)
    {
        $taskModel    = self::find()->where(['id' => $taskId])->one();
        $symptomIdArr = explode(",", $taskModel->malfunction_task);
        $abnormalArr  = Json::decode($taskModel->abnormal);
        $serviceArr   = '';
        if (!empty($symptomIdArr)) {
            foreach ($symptomIdArr as $key => $value) {
                $serviceArr .= "<div>" . EquipSymptom::getEquipSymptomDetail(['id' => $value])['symptom'] . "</div>";
            }
        }
        if (!empty($abnormalArr)) {
            foreach ($abnormalArr as $abnormal) {
                $serviceArr .= "<div>" . EquipWarn::$warnContent[$abnormal] . "</div>";
            }
        }
        return $serviceArr;
        // return implode("；", $symptomNameArr);
    }
    /**
     *  获取异常报警的数据
     *  @param $taskId
     *
     **/
    public static function getAbnormalData($taskId)
    {
        $taskModel = self::find()->where(['id' => $taskId])->one();

        $abnormal_id = Json::decode($taskModel->abnormal);
        $abnormals   = '';
        if (!empty($abnormal_id)) {
            foreach ($abnormal_id as $abnormal) {
                $abnormals .= EquipWarn::$warnContent[$abnormal] . " | ";
            }
        }
        return $abnormals;

    }

    /**
     *  获取配送和维修的数据
     *  @param $taskId
     *
     **/
    public static function getDistributionMaintenanceData($taskId)
    {
        $distribution = self::getDistributionData($taskId);
        $maintenance  = self::getMaintenanceData($taskId);
        return $distribution . $maintenance;
    }

    /**
     * 获取指定任务中的配送内容
     * @author  zgw
     * @version 2016-09-14
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function distributionContent($where)
    {
        $taskInfo = self::getDetail($where);
        return $taskInfo ? json_decode($taskInfo->delivery_task, true) : '';
    }

    /*
     * 分配任务时发送提示消息
     *  @param $model变更前的任务数据
     * @param $param变更后的任务数据
     * @return \yii\web\Response
     */
    public static function detailSendWxInfo($model, $param)
    {
        $originalPersonnel = $model->assign_userid;
        $buildName         = Building::getBuildingName($model->build_id)['name'];
        $taskTypeArr       = explode(',', $model->task_type);
        sort($taskTypeArr);
        $taskType = '';
        foreach ($taskTypeArr as $type) {
            if ($type == DistributionTask::DELIVERY) {
                $taskType .= '配送,';
            } elseif ($type == DistributionTask::SERVICE) {
                $taskType .= '维修,';
            } elseif ($type == DistributionTask::URGENT) {
                $taskType .= '紧急,';
            } elseif ($type == DistributionTask::CLEAN) {
                $taskType .= '清洗,';
            } elseif ($type == DistributionTask::REFUEL) {
                $taskType .= '换料,';
            }
        }
        $taskType = substr($taskType, 0, -1);
        $username = Manager::getField('realname', ['id' => Yii::$app->user->id]);
        // 更改运维人员发送消息
        if (trim($originalPersonnel) !== trim($param['assign_userid'])) {
            $taskRet = SendNotice::sendWxNotice($originalPersonnel, 'distribution-task/index', $username . '给您撤销了一条' . $taskType . '任务（楼宇名称：' . $buildName . '）', Yii::$app->params['distribution_agentid']);
            if (!$taskRet) {
                Yii::$app->getSession()->setFlash("error", "运维任务撤销信息发送失败");
                return false;
            }
        }
        //下发任务发送消息
        $taskRetNew = SendNotice::sendWxNotice($param['assign_userid'], 'distribution-task/index', $username . '给您分配了一条' . $taskType . '任务，请注意查收。', Yii::$app->params['distribution_agentid']);
        if (!$taskRetNew) {
            Yii::$app->getSession()->setFlash("error", "运维任务信息发送失败");
            return false;
        }
        return true;
    }

    /**
     * 企业微信页面获取任务类型
     */
    public static function getTaskType($type)
    {
        $taskTypeArr = explode(',', $type);

        $taskType = '';
        foreach ($taskTypeArr as $type) {
            if ($type == DistributionTask::DELIVERY) {
                $taskType .= '配送' . ',';
            } elseif ($type == DistributionTask::SERVICE) {
                $taskType .= '维修' . ',';
            } elseif ($type == DistributionTask::URGENT) {
                $taskType .= '紧急' . ',';
            } elseif ($type == DistributionTask::CLEAN) {
                $taskType .= '清洗' . ',';
            } elseif ($type == DistributionTask::REFUEL) {
                $taskType .= '换料' . ',';
            }
        }
        $taskType = substr($taskType, 0, -1);
        return $taskType;
    }

    /**
     * 获取运维人员负责的楼宇数量
     * @author wangxiwen
     * @version 2018-06-11
     * @return array
     */
    public static function getUserBuildNum()
    {
        $orgId = Manager::getManagerBranchID();
        $where = [];
        if ($orgId > 1) {
            $useridList = WxMember::getMemberIDArr($orgId);
            $where      = ['in', 'distribution_userid', $useridList];
        }
        $buildCount = Building::find()
            ->andWhere($where)
            ->select('distribution_userid,count(*) count')
            ->andWhere(['!=', 'distribution_userid', ''])
            ->groupBy('distribution_userid')
            ->createCommand()
            ->queryAll();
        $buildCountList = [];
        foreach ($buildCount as $build) {
            $buildCountList[$build['distribution_userid']] = $build['count'];
        }
        return $buildCountList;
    }

    /**
     * 获取运维任务中存在的运维人员姓名
     * @author wangxiwen
     * @version 2018-06-11
     * @return array
     */
    public static function getUserNameList()
    {
        $orgId = Manager::getManagerBranchID();
        $where = [];
        if ($orgId > 1) {
            $useridList = WxMember::getMemberIDArr($orgId);
            $where      = ['in', 'assign_userid', $useridList];
        }
        $userArray = self::find()
            ->alias('dt')
            ->distinct()
            ->leftJoin('wx_member wx', 'dt.assign_userid = wx.userid')
            ->andWhere($where)
            ->select('dt.assign_userid,wx.name')
            ->andWhere(['!=', 'dt.assign_userid', '0'])
            ->andWhere(['!=', 'dt.assign_userid', ''])
            ->createCommand()
            ->queryAll();
        $userList = [];
        foreach ($userArray as $user) {
            $userList[$user['assign_userid']] = $user['name'];
        }
        return $userList;
    }
    /**
     * 获取运维任务中存在的日期列表
     * @author wangxiwen
     * @version 2018-06-11
     * @return array
     */
    public static function getDateList()
    {
        $dateArray = self::find()
            ->distinct()
            ->select(new Expression("FROM_UNIXTIME(create_time,'%Y-%m') date"))
            ->createCommand()
            ->queryAll();
        $dateList = [];
        foreach ($dateArray as $date) {
            $dateList[$date['date']] = $date['date'];
        }
        return $dateList;
    }

    /**
     * 获取运维任务统计中的起始和结束时间戳
     * @author wangxiwen
     * @version 2018-06-10
     * @param string $date 日期 格式 yyyy-mm
     * @return $array
     */
    public static function getTime($date)
    {
        $dayArr    = explode('-', $date);
        $day       = WxMember::getDays($dayArr[0], $dayArr[1]);
        $startTime = strtotime($date . '-01 00:00:00');
        $days      = $day < 10 ? '0' . $day : $day;
        $endTime   = strtotime($date . '-' . $days . ' 23:59:59');
        return [
            'start' => $startTime,
            'end'   => $endTime,
        ];
    }

    /**
     * 路上时间
     * @author sulingling
     * @param $model object
     * @return boolean | string
     */
    public function roadTime()
    {
        if (empty($this->end_delivery_time)) {
            return false;
        }
        $startTime = strtotime($this->end_delivery_date);
        $info      = self::find()
            ->select('end_delivery_time')
            ->andFilterWhere(['author_id' => $this->author_id])
            ->andFilterWhere(['>=', 'end_delivery_time', $startTime])
            ->andFilterWhere(['<', 'end_delivery_time', $this->end_delivery_date])
            ->orderBy('end_delivery_time desc')
            ->limit(1)
            ->one();
        if (empty($info->end_delivery_time)) {
            $preTaskComplateTime = strtotime(date('Y-m-d', strtotime($this->end_delivery_date)) . ' 09:00');
        } else {
            $preTaskComplateTime = $info->end_delivery_time;
        }
        return $preTaskComplateTime - $this->start_delivery_time;
    }

    /**
     * 获取配送任务表中蓝牙秤上传数据
     * @author sulingling
     * @version 2018-06-25
     * @param $bluetoothUpload json 蓝牙秤上传数据
     * @return string()
     */
    public function bluetoothUpload()
    {
        if ($this->bluetooth_upload == '') {
            return '';
        }
        $bluetoothUploadArr = Json::decode($this->bluetooth_upload, true);
//        物料分类表  物料分类id =>物料分类名称
        $scmMaterialTypeArr = ScmMaterialType::getIdNameArr(1);
        //物料单位
        $materialTypeUnit = ScmMaterialType::getMaterialTypeUnit();

        $str = '<table class="table table-striped table-bordered detail-view">';
        $str .= "<tr><td></td><td>实际添加量</td><td>添加后剩余量</td><td>添加后修改量</td></tr>";
        foreach ($bluetoothUploadArr as $stockCode => $bluetoothUploads) {
            foreach ($bluetoothUploads as $scmMaterialType => $bluetoothUpload) {
                $str .= "<tr><td>" . $stockCode . '号料仓-' . $scmMaterialTypeArr[$scmMaterialType] . '</td><td>' . $bluetoothUpload['addAmount'] . $materialTypeUnit[$scmMaterialType] . '</td><td>' . $bluetoothUpload['overAmount'] . $materialTypeUnit[$scmMaterialType] . '</td><td>' . $bluetoothUpload['changeAmount'] . $materialTypeUnit[$scmMaterialType] . "</td></tr>";
            }
            $str .= '</td>';
        }

        $str .= '</tr></table>';
        return $str;
    }

    public function task($field)
    {
        $str = '<table><tr><td>已完成</td></tr>';
        $str .= $this->equip->$field ? "<tr><td>" . date("Y-m-d H:i:s", $this->equip->$field) . "</td></tr>" : '';
        $str .= '</table>';
        return $str;
    }

    /**
     * 获取图片的展示
     * @author sulingling
     * @version 2018-06-26
     * @return string
     */
    public function imageUrl()
    {
        if (empty($this->imageUrl)) {
            return '';
        }
        $str = "<table><tr>";
        foreach ($this->imageUrl as $image) {
            $str .= "<td><image src='" . $image->imgurl . "' style='width:100px;height:50px;padding: 0 5px;'></td>";
        }
        $str .= "</tr></table>";
        return $str;
    }
    /**
     * 获取待办运维任务
     * @author wangxiwen
     * @param  array $userId 用户ID
     * @return array
     */
    public static function getTaskToBeDone($userId)
    {
        return self::find()->orderBy('create_time')
            ->where(['assign_userid' => $userId, 'is_sue' => self::NO_FINISH])
            ->asArray()
            ->all();
    }

    /**
     * 获取已完成或作废运维任务
     * @author wangxiwen
     * @param  array $userId 用户ID
     * @return array
     */
    public static function getTaskHistorical($userId)
    {
        return self::find()->orderBy('is_sue DESC')
            ->andWhere(['assign_userid' => $userId])
            ->andWhere(['!=', 'is_sue', self::NO_FINISH])
            ->asArray()
            ->all();
    }

    /**
     * 获取未接收运维任务
     * @author wangxiwen
     * @param  array $userId 用户ID
     * @return array
     */
    public static function getTaskUnreceived($userId)
    {
        return self::find()
            ->andWhere(['assign_userid' => $userId])
            ->andWhere(['recive_time' => 0])
            ->andWhere(['is_sue' => self::NO_FINISH])
            ->asArray()
            ->all();
    }

    /**
     * 修改任务接收时间
     * @author wangxiwen
     * @param  array $userId 用户ID
     * @return array
     */
    public static function saveTaskReciveTime($userId)
    {
        return DistributionTask::updateAll(['recive_time' => time()], ['assign_userid' => $userId, 'is_sue' => self::NO_FINISH]);
    }

    /**
     * 将运维任务中的紧急任务置顶
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $taskToBeDone待办任务
     * @param array $taskToBeDoneCount待办任务数量
     * @return array
     */
    public static function setUrgentTaskTop($taskToBeDone, $taskToBeDoneCount)
    {
        $taskToBeDoneList = [];
        $urgent           = 0;
        $unurgent         = $taskToBeDoneCount - 1;
        foreach ($taskToBeDone as $task) {
            $taskType = explode(',', $task['task_type']);
            if (!in_array(self::URGENT, $taskType)) {
                $taskToBeDoneList[$unurgent] = $task;
                $unurgent--;
            } else {
                $taskToBeDoneList[$urgent] = $task;
                $urgent++;
            }
        }
        ksort($taskToBeDoneList);
        return $taskToBeDoneList;
    }

    /**
     * 获取指定人员已打卡任务(除当前任务外)
     * @author wangxiwen
     * @param  int $taskId 任务ID
     * @param  string $userId 用户ID
     * @return array
     */
    public static function getTaskSignedIn($taskId, $userId)
    {
        return self::find()
            ->andWhere(['!=', 'id', $taskId])
            ->andWhere(['>', 'start_delivery_time', 0])
            ->andWhere(['is_sue' => self::NO_FINISH])
            ->andWhere(['assign_userid' => $userId])
            ->one();
    }
    /**
     * 更新任务打卡信息
     * @author wangxiwen
     * @param  object $model 任务详情
     * @param  array $params 打卡信息
     * @return array
     */
    public static function saveTaskSignedIn($model, $params)
    {
        $model->start_delivery_time = time();
        // 获取打卡位置
        $model->start_latitude  = $params['startLatitude'];
        $model->start_longitude = $params['startLongitude'];
        $model->start_address   = $params['startAddress'];
        return $model->save();
    }
    /**
     * 紧急任务完成时更新数据
     * @author wangxiwen
     * @param  object $model 任务详情
     * @param  array $params 打卡信息
     * @return array
     */
    public static function saveTaskUrgent($model, $params)
    {
        $model->end_delivery_date = date('Y-m-d H:i:s', time());
        $model->end_latitude      = $params['endLatitude'];
        $model->end_longitude     = $params['endLongitude'];
        $model->end_address       = $params['endAddress'];
        $model->is_sue            = self::FINISHED;
        return $model->save();
    }

    /**
     * 获取已经打卡的任务
     * @author wangxiwen
     * @version 2018-10-21
     * @param  int $buildId 楼宇ID
     * @return object
     */
    public static function getClockInTask($buildId)
    {
        return self::find()
            ->andWhere(['build_id' => $buildId])
            ->andWhere(['is_sue' => self::NO_FINISH])
            ->andWhere(['>', 'start_delivery_time', 0])
            ->asArray()
            ->one();
    }

    /**
     * 获取运维任务内容
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $taskId 任务id
     * @return object
     */
    public static function getDistributionTask($taskId)
    {
        return self::findOne($taskId);
    }

    /**
     * 更换运维任务的负责人
     * @author wangxiwen
     * @version 2018-10-27
     * @param array $taskIdArr 运维任务列表
     * @param string $userId 运维人员Id
     * @return
     */
    public static function saveTaskUser($taskIdArr, $userId)
    {
        foreach ($taskIdArr as $taskId) {
            $taskModel                = self::getDistributionTask($taskId);
            $taskModel->assign_userid = $userId;
            $taskModelRes             = $taskModel->save();
            if (!$taskModelRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取运维人员手中剩余物料总和(散料和整料)
     * @author wangxiwen
     * @version 2018-10-30
     * @param string $userid 运维人员
     * @return
     */
    public static function getSurplusMaterial($userid)
    {
        if (!$userid) {
            return '';
        }
        $scmMaterial         = ScmMaterial::getScmMaterial();
        $surplusMaterial     = ScmUserSurplusMaterial::getSurplusMaterialByUser($userid, $scmMaterial);
        $surplusMaterialGram = ScmUserSurplusMaterialGram::getSurplusMaterialGramByUser($userid);
        return self::getSurplusMaterialContent($scmMaterial, $surplusMaterial, $surplusMaterialGram);
    }

    /**
     * 组合运维人员手中剩余物料显示信息
     * @author wangxiwen
     * @version 2018-10-30
     * @param array $materialTypeArr 物料信息
     * @param array $surplusMaterial 剩余整料
     * @param array $surplusMaterialGram 剩余散料
     * @param string
     */
    public static function getSurplusMaterialContent($scmMaterial, $surplusMaterial, $surplusMaterialGram)
    {
        if (empty($surplusMaterial) && empty($surplusMaterialGram)) {
            return '';
        }
        $content = '';
        foreach ($scmMaterial as $materialTypeId => $material) {
            $scmMaterialDetail = $scmMaterial[$materialTypeId] ?? [];
            if (empty($scmMaterialDetail)) {
                continue;
            }
            $packets = $surplusMaterial[$materialTypeId] ?? 0;
            $gram    = $surplusMaterialGram[$materialTypeId] ?? 0;
            if (!$packets && !$gram) {
                continue;
            }
            $materialTypeName = $scmMaterialDetail['material_type_name'];
            $unit             = $scmMaterialDetail['unit'];
            $type             = $scmMaterialDetail['type'];
            $weightUint       = $scmMaterialDetail['weight_unit'];
            $weight           = $scmMaterialDetail['weight'];
            $materialUnit     = $type == 1 ? $weightUint : $unit;
            $overAmount       = $type == 1 ? $weight * $packets + $gram : $packets;

            $content .= $materialTypeName . '：' . $overAmount . $materialUnit . '   ';
        }
        return $content;
    }

    // 添加时 处理的微信消息
    /**
     * @param $model
     * @return \yii\web\Response
     */
    public static function detailCreateWxInfo($taskType, $assignUserid)
    {
        $taskTypeStr = self::getTaskType($taskType);
        $username    = Manager::getField('realname', ['id' => Yii::$app->user->id]);
        SendNotice::sendWxNotice($assignUserid, '/distribution-task/index', $username . '给您分配了一条' . $taskTypeStr . '任务，请注意查收。', Yii::$app->params['distribution_agentid']);
    }

}
