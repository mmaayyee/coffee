<?php

namespace backend\models;

use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\SendNotice;
use Yii;

/**
 * This is the model class for table "equip_repair".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $create_time
 * @property string $content
 * @property string $remark
 * @property integer $recive_time
 *
 * @property Equipments $equip
 */
class EquipRepair extends \yii\db\ActiveRecord
{
    public $build_id;
    public $is_accept;
    public $process_status;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_repair';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id'], 'required'],
            [['equip_id', 'create_time', 'recive_time'], 'integer'],
            [['content', 'remark'], 'string', 'max' => 500],
            [['build_name'], 'string', 'max' => 80],
            [['author'], 'string', 'max' => 64],
            [['build_address'], 'string', 'max' => 200],
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
        if (!$this->content && !$this->remark) {
            $this->addError($attribute, "故障现象和备注不可同时为空.");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'equip_id'      => '设备id',
            'create_time'   => '上报时间',
            'content'       => '故障内容',
            'remark'        => '备注',
            'recive_time'   => '接报时间',
            'build_name'    => '楼宇名称',
            'build_address' => '楼宇地址',
            'build_id'      => '楼宇',
            'author'        => '创建者',
            'process_status'=> '处理状态',
            'is_accept'     => '是否接报',
        ];
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
    public function getTask()
    {
        return $this->hasOne(EquipTask::className(), ['repair_id' => 'id']);
    }

    /**
     * 获取处理状态
     * @author  zgw
     * @version 2017-04-28
     * @return  [type]     [description]
     */
    public function getRepairStatus()
    {
        $equipTaskObj = EquipTask::findOne(['repair_id' => $this->id]);
        if (!$equipTaskObj || !$equipTaskObj->start_repair_time) {
            return '未处理';
        } else if (!$equipTaskObj->end_repair_time) {
            return '处理中';
        } else {
            return $equipTaskObj->process_result == 2 ? '成功解决' : '解决失败';
        }
    }

    /**
     * 处理结果
     * @author  zgw
     * @version 2017-04-28
     * @return  [type]     [description]
     */
    public function getRepairResult()
    {
        $equipTaskObj = EquipTask::findOne(['repair_id' => $this->id]);
        return $equipTaskObj ? $equipTaskObj->process_method : '';
    }

    /**
     * 处理结果
     * @author  zgw
     * @version 2017-04-28
     * @return  [type]     [description]
     */
    public function getSuccessTime()
    {
        $equipTaskObj = EquipTask::findOne(['repair_id' => $this->id]);
        return isset($equipTaskObj->end_repair_time) && $equipTaskObj->end_repair_time > 0 ? date('Y-m-d H:i:s', $equipTaskObj->end_repair_time) : '';
    }

    /**
     * 客服上报发送消息给配送主管
     * @author  zgw
     * @version 2017-04-28
     * @param   [type]     $buildId [description]
     * @return  [type]              [description]
     */
    public function sendRepairNotice($buildId, $taskId)
    {
        $userId = Building::getDistributionManager($buildId);
        if ($userId) {
            $content = '客服' . Yii::$app->user->identity->realname . "上报了一个设备故障，点此分配任务";
            $url     = "equip-task/assigned-personnel?id=" . $taskId;
            $appid   = Yii::$app->params['equip_agentid'];
            SendNotice::sendWxNotice($userId, $url, $content, $appid);
        }
        return true;
    }
}
