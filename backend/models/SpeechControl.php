<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "speech_control".
 *
 * @property int $id 语音控制ID
 * @property int $create_time 创建时间
 * @property int $start_time 活动开始时间
 * @property int $end_time 活动结束时间
 * @property int $status 语音控制状态1待审核2待上线3审核失败4上线5下线
 * @property string $speech_control_title 语音控制标题
 * @property string $speech_control_content 语音控制内容
 */
class SpeechControl extends \yii\db\ActiveRecord
{
    public $id;
    public $create_time;
    public $examine_time;
    public $start_time;
    public $end_time;
    public $status;
    public $speech_control_title;
    public $speech_control_content;

    //待审核
    const NO_CONFIRM = 1;
    //待上线
    const NO_ONLINE = 2;
    //审核失败
    const IS_REFUSE = 3;
    //上线
    const IS_ONLINE = 4;
    //下线
    const IS_DOWNLINE = 5;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'speech_control';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'examine_time'], 'integer'],
            [['speech_control_title'], 'string', 'max' => 255],
            [['speech_control_content'], 'string', 'max' => 1000],
            [['start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => '编号',
            'speech_control_title'   => '语音控制标题',
            'speech_control_content' => '语音控制内容',
            'create_time'            => '创建时间',
            'examine_time'           => '审核时间',
            'start_time'             => '开始时间',
            'end_time'               => '结束时间',
            'status'                 => '状态',
        ];
    }

    public static function getSpeechList($params)
    {
        $list = self::postBase("speech-control-api/speech-control-index.html", $params);
        return !empty($list) ? Json::decode($list) : [];
    }

    /**
     * 获取语音控制信息
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getSpeechInfo($id)
    {
        return self::getBase("speech-control-api/speech-control-view.html?id=" . $id);
    }

    /**
     * 更新语音控制信息
     * @return [type] [description]
     */
    public static function saveSpeechControlExamine($id, $status)
    {
        return self::getBase("speech-control-api/speech-control-examine.html?id=" . $id . '&status=' . $status);
    }

    /**
     * 获取语音控制初始化数据
     * @return [type] [description]
     */
    public static function getSpeechControlInit($id)
    {
        return self::getBase("speech-control-api/speech-control-init.html?id=" . $id);
    }

    /**
     * 添加和编辑时更新数据
     * @param [type] $params [description]
     */
    public static function SaveSpeechControlInfo($params)
    {
        return self::postBase("speech-control-api/save-speech-control-info.html", $params);
    }

    /**
     * 检测楼宇
     */
    public static function checkBuild($params)
    {
        return self::postBase("speech-control-api/check-build.html", $params);
    }

    /**
     * 筛选楼宇
     */
    public static function filterBuild($params)
    {
        return self::postBase("speech-control-api/filter-build.html", $params);
    }

    /**
     * POST 方式提交数据共用方法
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data = [])
    {
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, Json::encode($data));
    }

    /**
     * GET 提交数据共用方法
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
}
