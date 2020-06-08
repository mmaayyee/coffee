<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "user_laxin_reward_record".
 *
 * @property string $laxin_reward_record_id 用户拉新奖励ID
 * @property string $share_userid 分享者用户ID
 * @property string $laxin_userid 新用户ID
 * @property string $beans_number 咖豆数量
 * @property string $coupon_group_id 优惠券套餐ID
 * @property string $coupon_number 优惠券数量
 * @property string $reward_time 获奖时间
 */
class UserLaxinRewardRecord extends \yii\db\ActiveRecord
{

    public $laxin_reward_record_id;
    public $share_userid;
    public $laxin_userid;
    public $beans_number;
    public $coupon_group_id;
    public $coupon_number;
    public $reward_time;
    public $group_name;
    public $is_register;
    public $created_at;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['laxin_reward_record_id', 'share_userid', 'laxin_userid', 'beans_number', 'coupon_group_id', 'coupon_number','beans_number'], 'integer'],
            [['reward_time','group_name','is_register','created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'laxin_reward_record_id' => '用户拉新奖励ID',
            'share_userid'           => '分享者用户ID',
            'laxin_userid'           => '新用户ID',
            'beans_number'           => '咖豆数量',
            'coupon_group_id'        => '优惠券套餐ID',
            'coupon_number'          => '优惠券数量',
            'reward_time'            => '获奖时间',
            'group_name'             => '优惠券套餐名称',
            'beans_number'     => '咖豆数量',
        ];
    }

    /**
     * 获取分享用户被绑定，人员列表
     * @author  wxw
     * @version 2018-3-30
     * @return  [type]     [description]
     */
    public static function shareBindUser($processParams = [])
    {
        $page = isset($processParams['page']) ? $processParams['page'] : 0;

//        echo "<pre>";
//        print_r($processParams);die;

        return self::postBase("laxin-activity-api/share-bind-user.html?page=" . $page, $processParams);

    }
    /**
     * 获取分享者奖励列表
     * @author  wxw
     * @version 2018-4-1
     * @return  [type]     [description]
     */
    public static function shareReward($processParams = [])
    {
        $page         = isset($processParams['page']) ? $processParams['page'] : 0;
        return self::postBase("laxin-activity-api/share-reward.html?page=" . $page, $processParams);

    }

    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function getBase($action, $params = '')
    {
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
    public static function postBase($action, $data = [])
    {
        // echo Yii::$app->params['fcoffee'] . $action;die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, Json::encode($data));
    }
}
