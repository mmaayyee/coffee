<?php

namespace backend\models;

use backend\models\DistributionNoticeRead;
use backend\models\Manager;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "distribution_notice".
 *
 * @property integer $Id
 * @property string $create_time
 * @property string $sender
 * @property string $content
 * @property integer $send_num
 * @property string $receiver
 */
class DistributionNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_notice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiver'], 'required'],
            [['create_time', 'content'], 'required'],
            [['create_time', 'send_num'], 'integer'],
            [['sender', 'content', 'receiver'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'          => 'ID',
            'create_time' => '发送时间',
            'sender'      => '发送人',
            'content'     => '发送内容',
            'send_num'    => '发送人数量',
            'receiver'    => '接收人',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getManager()
    {
        return $this->hasOne(Manager::className(), ['username' => 'sender']);
    }
    /**
     *  根据登录人不同获取不同的配送员（配送经理：所有配送，配送主管：该分公司下的配送员）
     *  @author zhangMuYu
     *  @param $userid 当前登录人
     *  @return array()  distribution_attendance
     **/
    public static function getDisAttendanceList($userid)
    {
        $managerModel  = Manager::find()->where(['id' => $userid])->one();
        $wxMemberModel = WxMember::findOne($managerModel->userid);
        //如果为设备经理或超级管理员，则显示所有的配送人员
        if ($userid == 1 || $wxMemberModel->position == WxMember::DISTRIBUTION_MANAGER) {
            //查询所有设备人员（包括设备主管）
            $cond        = ['or', 'position=5', 'position=6'];
            $wxMemberArr = WxMember::getMemberNameList($cond);
            array_shift($wxMemberArr);
        } else {
            //判断是否为配送主管
            if ($wxMemberModel->position == WxMember::DISTRIBUTION_RESPONSIBLE) {
                //取到该分公司下的配送人员
                $cond        = ['org_id' => $wxMemberModel->org_id, 'position' => WxMember::DISTRIBUTION_MEMBER];
                $memberArr   = WxMember::find()->where($cond)->asArray()->all();
                $wxMemberArr = WxMember::getMemberNameList($cond);
                array_shift($wxMemberArr);
            } else {
                echo "角色错误，基于配送管理层，请返回登录主页.";exit();
            }
        }
        return $wxMemberArr;
    }

    /**
     *  怎么处理微信发送的消息
     *  @param $param, $model, $transaction
     **/
    public static function dealReadRelated($param, $model, $transaction)
    {
        //处理并发送消息
        $retSend = SendNotice::sendWxNotice($model->receiver, 'distribution-notice-read/index', $model->content, Yii::$app->params['distribution_agentid']);
        if (!$retSend) {
            echo '配送信息发送失败';
            $transaction->rollBack();exit();
        }
        //插入相关人员阅读表
        foreach ($param['DistributionNotice']['receiver'] as $key => $value) {
            $noticeReadModel            = new DistributionNoticeRead();
            $noticeReadModel->notice_id = $model->Id;
            $noticeReadModel->userId    = $value;
            $retRead                    = $noticeReadModel->save();
            if (!$retRead) {
                echo '配送相关人员添加失败';
                $transaction->rollBack();exit();
            }
        }

    }

    /**
     *  以逗号把名字连接成字符串。
     *
     *
     **/
    public static function getReceiverStr($receiver)
    {
        $receiverArr = explode('|', $receiver);
        // var_dump($receiverArr);exit();
        $readArr = array();
        foreach ($receiverArr as $key => $value) {
            $readArr[] = WxMember::find()->where(['userid' => $value])->one()->name;
        }
        $readStr = implode(",", $readArr);
        return $readStr;
    }
}
