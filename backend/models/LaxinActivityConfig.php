<?php
namespace backend\models;

use common\helpers\Tools;
use yii;
use yii\helpers\Json;

class LaxinActivityConfig extends \yii\db\ActiveRecord
{

    public $laxin_activity_id;
    public $no_register_content;
    public $activity_description;
    public $new_user_content;
    public $old_user_content;
    public $rebate_node;
    public $is_repeate;
    public $new_coupon_groupid;
    public $old_coupon_groupid;
    public $share_coupon_groupid;
    public $new_beans_number;
    public $old_beans_number;
    public $share_beans_number;
    public $share_beans_percentage;
    public $start_time;
    public $end_time;
    public $create_time;
    public $couponGroupList;
    public $backgroud_img;
    public $cover_img;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_beans_number','old_beans_number','share_beans_number','share_beans_percentage'],'required'],
            [['laxin_activity_id', 'rebate_node', 'is_repeate', 'new_coupon_groupid', 'old_coupon_groupid', 'share_coupon_groupid', 'new_beans_number', 'old_beans_number', 'share_beans_number'], 'integer'],
            [['no_register_content', 'activity_description', 'new_user_content', 'old_user_content', 'backgroud_img', 'cover_img'], 'string', 'max' => 100],
            [['create_time', 'start_time', 'end_time', 'couponGroupList', 'share_beans_percentage'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'laxin_activity_id'      => '用户拉新奖励ID',
            'no_register_content'    => '老用户未注册推文',
            'activity_description'   => '活动描述推文',
            'new_user_content'       => '新用户推文',
            'old_user_content'       => '老用户有奖励推文',
            'rebate_node'            => '返利节点',
            'is_repeate'             => '是否重复获取奖励',
            'new_coupon_groupid'     => '新注册用户获取的优惠券套餐',
            'old_coupon_groupid'     => '老用户获取的优惠券套餐',
            'share_coupon_groupid'   => '分享者获取的优惠券套餐',
            'new_beans_number'       => '新注册用户获取的咖豆数量',
            'old_beans_number'       => '老用户获取的咖豆数量',
            'share_beans_number'     => '分享者获取的咖豆数量',
            'share_beans_percentage' => '分享者获取消费记录实际支付百分比的咖豆数量',
            'start_time'             => '活动开始时间',
            'end_time'               => '活动结束时间',
            'create_time'            => '添加时间',
            'backgroud_img'          => '背景图',
            'cover_img'              => '遮罩图',
        ];
    }

    public static function getLaxinActivityConfig()
    {
        $configList = self::getBase('laxin-activity-api/get-config.html');
//        echo "<pre>";
//        print_r($configList);die;
        $configList = Json::decode($configList);

        $model = new self();
        $model->load(['LaxinActivityConfig' => $configList]);
        return $model;
    }

    public static function saveLaxinActivityConfig($params)
    {
        $configList = self::postBase('laxin-activity-api/save-config.html', $params);
        $model      = new self();
        $model->load(['LaxinActivityConfig' => $configList]);

        return $model;
    }

    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffee'] . $action . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }

    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @param   string $action 请求的方法名
     * @param   array $data 发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data)
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action, json_encode($data, JSON_UNESCAPED_UNICODE);die;
        $res = Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($res === 'true' || $res == 1) {
            return true;
        }
        return false;
    }

}
