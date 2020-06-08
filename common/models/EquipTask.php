<?php

namespace common\models;

use backend\models\EquipAcceptance;
use backend\models\EquipDelivery;
use backend\models\EquipSymptom;
use common\helpers\Tools;
use common\models\Building;
use common\models\Equipments;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "equip_task".
 *
 * @property integer $id
 * @property integer $build_id
 * @property string $content
 * @property string $malfunction_reason
 * @property string $malfunction_description
 * @property string $assign_userid
 * @property integer $task_type
 * @property integer $create_time
 * @property integer $recive_time
 * @property integer $create_user
 * @property integer $end_repair_time
 * @property string $process_method
 * @property integer $is_use_fitting
 * @property integer $process_result
 * @property string $remark
 *
 * @property Building $build
 * @property EquipTaskMalfuntionAssoc[] $equipTaskMalfuntionAssocs
 */
class EquipTask extends \yii\db\ActiveRecord
{
    public $extra_name;
    /**
     *   任务
     **/
    /*维修任务*/
    const MAINTENANCE_TASK = 1;

    /*投放验收任务*/
    const TRAFFICKING_TASK = 2;

    /*灯箱验收任务*/
    const LIGHTBOX_ACCEPTANCE_TASK = 3;

    /*设备附件任务*/
    const EXTRA_TASK = 4;

    /*无负责人*/
    const NO_USER = 2;

    /**
     *   结果
     *   处理结果 1-未维修（验收） 2-维修（验收）成功 3-维修（验收）失败
     **/
    /*1-未维修（验收）*/
    const UNTREATED = 1;

    /*2-维修（验收）成功*/
    const RESULT_SUCCESS = 2;

    /*3-维修（验收）失败*/
    const RESULT_FAILURE = 3;

    const RESULT_RECYCLE = 4;

    public $build_name; // 楼宇名称

    //任务类型
    public static $task_type = [
        ''                             => '请选择',
        self::MAINTENANCE_TASK         => '维修任务',
        self::TRAFFICKING_TASK         => '投放验收任务',
        self::LIGHTBOX_ACCEPTANCE_TASK => '灯箱验收任务',
        self::EXTRA_TASK               => '设备附件任务',
    ];

    //维修结果
    public static $repair_result = [
        self::UNTREATED      => '未维修',
        self::RESULT_SUCCESS => '维修成功',
        self::RESULT_FAILURE => '维修失败',
    ];
    //验收结果结果
    public static $acceptance_result = [
        self::UNTREATED      => '未验收',
        self::RESULT_SUCCESS => '验收成功',
        self::RESULT_FAILURE => '验收失败',
    ];
    //附件配送结果
    public static $extra_result = [
        self::UNTREATED      => '未配送',
        self::RESULT_SUCCESS => '配送成功',
        self::RESULT_FAILURE => '配送失败',
        self::RESULT_RECYCLE => '回收成功',
    ];

    public static $assign_label = [
        self::MAINTENANCE_TASK         => '指定维修人',
        self::TRAFFICKING_TASK         => '指定投放验收人',
        self::LIGHTBOX_ACCEPTANCE_TASK => '指定灯箱验收人',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id'], 'required'],
            [['build_id', 'relevant_id', 'task_type', 'create_time', 'recive_time', 'is_use_fitting', 'process_result', 'repair_id', 'light_box_repair_id', 'equip_id', 'update_time', 'is_userid'], 'integer'],
            [['content'], 'string', 'max' => 1000],
            [['malfunction_reason', 'malfunction_description', 'process_method', 'create_user', 'remark'], 'string', 'max' => 500],
            [['assign_userid'], 'string', 'max' => 64],
            [['assign_userid'], 'required', 'on' => 'change'],
            [['remark', 'content'], 'twoChoose', 'skipOnError' => false, 'skipOnEmpty' => false],
        ];
    }

    /**
     * 验证故障想象和故障内容不能同时为空
     * @author  zgw
     * @version 2016-12-07
     * @param   [type]     $attribute [description]
     * @param   [type]     $params    [description]
     * @return  [type]                [description]
     */
    public function twoChoose($attribute, $params)
    {
        $tip = $this->task_type == self::EXTRA_TASK ? '设备附件和备注不可同时为空.' : '故障现象和备注不可同时为空.';
        if (!$this->content && !$this->remark) {
            $this->addError($attribute, $tip);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                      => '任务id',
            'build_id'                => '楼宇名称',
            'content'                 => '任务内容',
            'assign_userid'           => '指定人',
            'task_type'               => '任务类型',
            'create_time'             => '创建任务时间',
            'recive_time'             => '接到任务时间',
            'start_repair_time'       => '开始维修时间',
            'end_repair_time'         => '结束维修时间',
            'malfunction_reason'      => '故障原因',
            'process_method'          => '处理方法',
            'is_use_fitting'          => '是否使用配件',
            'process_result'          => '处理结果',
            'remark'                  => '备注',
            'create_user'             => '创建者',
            'malfunction_description' => '故障描述',
            'build_name'              => '楼宇名称',
            'start_address'           => '开始任务位置',
            'end_address'             => '结束任务位置',
            'org_type'                => '机构类型',
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
    public function getDelivery()
    {
        return $this->hasOne(EquipDelivery::className(), ['id' => 'relevant_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['id' => 'equip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipDelivery()
    {
        return $this->hasOne(EquipDelivery::className(), ['Id' => 'relevant_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignMemberName()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'assign_userid']);
    }

    public function getAcceptanceResult()
    {
        return $this->hasOne(EquipLightBoxAcceptanceTaskResult::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipTaskMalfuntionAssocs()
    {
        return $this->hasMany(EquipTaskMalfuntionAssoc::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcceptance()
    {
        return $this->hasOne(EquipAcceptance::className(), ['delivery_id' => 'relevant_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipTaskFitting()
    {
        return $this->hasMany(EquipTaskFitting::className(), ['task_id' => 'id']);
    }

    /**
     * 根据故障现象id获取故障现象内容
     * @param  string $content 故障现象id(或者故障描述) 如：'1,2'
     * @return string      故障内容
     */
    public static function getMalfunctionContent($content, $type = 1)
    {
        if ($type != EquipTask::MAINTENANCE_TASK && $type != EquipTask::EXTRA_TASK) {
            return $content;
        }
        //如果是附件任务获取附件
        if ($type == EquipTask::EXTRA_TASK) {
            return EquipExtra::getExtraNameByID($content);
        }

        return EquipSymptom::getSymptomNameStr($content);
    }

    /**
     * 获取任务列表
     * @param  string $field [description]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public static function getEquipTaskList($field = "*", $where, $limit = 10, $page = 0)
    {
        return self::find()->select($field)->joinWith('build')->where($where)->asArray()->offset($page * $limit)->limit($limit)->orderby('end_repair_time desc, id desc')->all();
    }

    /**
     * 获取任务列表
     * @author  zgw
     * @version 2016-11-30
     * @param   string     $field [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getEquipTaskObjList($field = "*", $where)
    {
        return self::find()->select($field)->where($where)->all();
    }

    /**
     * 获取任务详情
     * @param  string $filed [description]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public static function getEquipTaskDetail($field = "*", $where)
    {
        return self::find()->select($field)->where($where)->joinWith('build')->asArray()->one();
    }

    /**
     * 获取任务详情
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function taskDetailObj($where)
    {
        return self::find()->where($where)->one();
    }

    /**
     * 修改设备信息
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $taskModel [description]
     * @param   [type]     $equipId   [description]
     * @return  [type]                [description]
     */
    public static function changeTask($taskModel, $equipId, $params = [])
    {
        $taskModel->end_repair_time = time(); //结束验收的时间
        $taskModel->equip_id        = $equipId;
        $taskModel->end_address     = isset($params['end_address']) ? $params['end_address'] : '';
        $taskModel->end_longitude   = isset($params['end_longitude']) ? $params['end_longitude'] : '';
        $taskModel->end_latitude    = isset($params['end_latitude']) ? $params['end_latitude'] : '';
        if ($taskModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '任务信息修改失败');
            return false;
        }
        return true;
    }

    /**
     * 创建维修任务（投放验收失败时）
     * @author  zgw
     * @version 2016-09-09
     * @param   [type]     $buildId    [description]
     * @param   [type]     $params     [description]
     * @param   [type]     $deliveryId [description]
     * @return  [type]                 [description]
     */
    public static function createTask($buildId, $equipId, $content, $remark, $deliveryId)
    {
        if (!$buildId || !$equipId) {
            Yii::$app->getSession()->setFlash('error', '维修任务中不能没有楼宇和设备');
            return false;
        }
        $taskModel              = new EquipTask();
        $taskModel->build_id    = $buildId;
        $taskModel->equip_id    = $equipId;
        $taskModel->content     = $content ? implode(',', $content) : '默认内容';
        $taskModel->remark      = Tools::filterEmoji($remark, '?');
        $taskModel->task_type   = self::MAINTENANCE_TASK;
        $taskModel->create_time = time();
        $taskModel->relevant_id = $deliveryId;
        if ($taskModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '维修任务创建失败');
            return false;
        }
        return true;
    }

    /**
     * 获取任务数量
     * @author  zgw
     * @version 2016-11-30
     * @param   [type]     $author [description]
     * @param   integer    $type   [description]
     * @return  [type]             [description]
     */
    public static function getCount($author, $type = 1)
    {
        $query = self::find();
        $query->andWhere(['assign_userid' => $author]);
        $query->andWhere(['>', 'process_result', 1]);
        if ($type == 1) {
            // 今天的开始时间
            $startTime = strtotime(date('Y-m-d'));
            // 今天的结束时间
            $endTime = strtotime(date('Y-m-d') . ' 23:59:59');
        } else {
            // 本月1号的时间戳
            $startTime = strtotime(date('Y-m') . '-01');
            // 下月1号的时间戳
            $endTime = strtotime(date('Y-m', strtotime('1 month')) . '-01');
        }
        $query->andWhere(['between', 'end_repair_time', $startTime, $endTime]);
        return $query->count();
    }

    /**
     * 根据orgId获取该分公司下待分配的任务
     * @param int $orgId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEquipTaskByOrgId($orgId = 0)
    {
        return self::find()->joinWith('build')->where(['building.org_id' => $orgId, 'equip_task.is_userid' => EquipTask::NO_USER])->asArray()->orderBy('equip_task.create_time DESC')->all();
    }

    /**
     * 投放验收任务当天还没有分配时发送通知
     */
    public static function checkWaitForTask()
    {
        $tasks = self::find()->where(['task_type' => self::TRAFFICKING_TASK, 'is_userid' => self::NO_USER])->asArray()->all();
        foreach ($tasks as $k => $task) {
            if (!$task['build_id']) {
                continue;
            }
            $orgId = Building::getField('org_id', ['id' => $task['build_id']]);
            //查询总部配送经理和配送主管
            $user     = ArrayHelper::getColumn(WxMember::getRoleByOrg($orgId), 'userid');
            $userList = implode('|', $user);

            $url       = 'equip-task/assigned-personnel?id=' . $task['id'];
            $buildName = Building::getField('name', ['id' => $task['build_id']]);
            $taskRet   = SendNotice::sendWxNotice($userList, $url, $buildName . '投放验收任务待分配' . '，请注意查看。', Yii::$app->params['equip_agentid']);
            if (!$taskRet) {
                echo json_encode(['status' => 0, 'msg' => '信息发送失败']);
            }
        }
    }

    /**
     * 企业微信任务完成时,维修任务未修复添加设备任务
     * @author wangxiwen
     * @datetime 2018-07-12
     * @param array $params 维修任务信息
     * @param object $taskModel 运维任务信息
     * return boole
     */
    public static function insetEquipTask($params, $taskModel)
    {
        $equipTask              = new EquipTask();
        $equipTask->build_id    = $taskModel->build_id;
        $equipTask->equip_id    = $taskModel->equip_id;
        $equipTask->create_user = WxMember::find()->where(['userid' => $taskModel->assign_userid])->select('name')->scalar();
        $equipTask->task_type   = EquipTask::MAINTENANCE_TASK;
        $equipTask->create_time = time();
        $equipTask->content     = $taskModel->malfunction_task;
        $equipTaskRet           = $equipTask->save();
        if ($equipTaskRet === false) {
            return false;
        }
        return true;
    }

    /**
     * 根据投放单ID获取投放验收任务
     * @author zhenggangwei
     * @date   2019-01-28
     * @param  integer     $deliveryId 投放单ID
     * @return object
     */
    public static function getEquipTaskByDeliveryId($deliveryId)
    {
        return self::find()->where(['relevant_id' => $deliveryId])->one();
    }

    /**
     * 删除投放验收任务（修改投放单时）
     * @author zhenggangwei
     * @date   2019-02-11
     * @param  integer     $deliveryId 投放单ID
     * @return integer
     */
    public static function deleteDeliveryTask($deliveryId)
    {
        return self::deleteAll(['relevant_id' => $deliveryId, 'process_result' => self::UNTREATED, 'task_type' => self::TRAFFICKING_TASK]);
    }
}
