<?php

namespace backend\models;

use Yii;
use backend\models\LotteryAwards;
use yii\helpers\Json;
use backend\models\UserCoupon;
use frontend\models\Activity;
use common\models\ActivityApi;

/**
 * This is the model class for table "lottery_winning_record".
 *
 * @property string $winning_record_id
 * @property string $activity_id
 * @property string $awards_name
 * @property string $prizes_name
 * @property integer $prizes_type
 * @property string $user_id
 * @property string $user_phone
 * @property string $user_addr_info
 * @property integer $is_winning
 * @property integer $is_ship
 * @property integer $create_time
 * @property string $activity_type_id
 */
class LotteryWinningRecord extends \yii\db\ActiveRecord
{
    public $winning_record_id;  // 抽奖活动记录ID
    public $activity_id;        // 活动ID
    public $awards_name;        // 奖项名称
    public $prizes_name;        // 奖品名称
    public $prizes_type;        // 奖品类型
    public $created_time;       // 创建时间
    public $user_id;            // 用户iD
    public $receiver_name;      // 收货人姓名
    public $user_phone;         // 收货人电话
    public $user_addr_info;     // 用户信息
    public $is_winning;         // 是否中奖
    public $is_ship;            // 是否发货
    public $create_time;        // 创建时间
    public $activity_type_id;   // 活动类型ID

    public $start_time;         // 查询开始时间
    public $end_time;           // 查询结束时间

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['winning_record_id', 'user_id', 'activity_id','prizes_type', 'is_winning', 'is_ship', 'create_time', 'activity_type_id'], 'integer'],
            [['prizes_name', 'user_phone', 'awards_name'], 'string', 'max' => 50],
            [['user_addr_info', 'receiver_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'winning_record_id' => '抽奖活动记录ID',
            'activity_id'       => '抽奖活动名称',
            'awards_name'       => '奖项名称',
            'prizes_name'       => '奖品名称',
            'prizes_type'       => '奖品类型',
            'user_id'           => '用户名称',
            'user_phone'        => '收货人电话',
            'user_addr_info'    => '用户地址信息',
            'is_winning'        => '是否中奖',
            'is_ship'           => '是否发货',
            'create_time'       => '创建时间',
            'receiver_name'     => '收货人姓名',
            'activity_type_id'  => '活动类型',
            'start_time'        => '开始时间',
            'end_time'          => '结束时间',
        ];
    }

    /**
     * 通过用户Id，查询用户名
     * @author  zmy
     * @version 2017-12-04
     * @param   [type]     $userId [用户ID]
     * @return  [type]             [description]
     */
    public static function getUserNameById($userId)
    {
        return ActivityApi::getUserNameById($userId);
    }


}
