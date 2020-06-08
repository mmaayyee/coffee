<?php

namespace backend\models;

use common\models\ActivityApi;
use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property integer $activity_id
 * @property string $activity_name
 * @property string $activity_desc
 * @property integer $multiple
 * @property integer $created_at
 * @property integer $status
 * @property string $start_time
 * @property string $end_time
 * @property string $activity_url
 * @property string $activity_sort
 */
class Activity extends \yii\db\ActiveRecord
{
    public $activity_id; // 活动ID
    public $activity_name; // 活动名称
    public $activity_desc; // 活动描述
    public $created_at; // 创建时间
    public $status; // 活动状态
    public $start_time; // 活动开始时间
    public $end_time; // 活动结束时间
    public $activity_url; // 活动链接地址
    public $activity_sort; // 活动排序

    public $person_day_frequency; // 每人每天参与次数
    public $max_frequency; // 活动内 参与最大次数
    public $awards_num; // 奖项数目

    public $background_music; // 背景音乐
    public $background_photo; // 背景图片
    public $activity_tips; // 活动锦囊
    public $title_photo; // 标题图片
    public $activity_background; // 九宫格活动背景图
    public $light_one_backgroup; // 灯泡背景1
    public $light_two_backgroup; // 灯泡背景2
    public $lottery_button; // 抽奖按钮
    public $click_effect; // 点选效果
    public $grid_photo; // 方格图片
    public $activity_type_id; // 活动类型Id

    public $createFrom; // 上线开始时间
    public $createTo; // 上线结束时间

    public $files; // 上传文件。
    public $isCopy; // 是否复制

    // 0正常 1下线 2-待上线 3-上线 4-已过期
    const NORMAL    = 0; // 正常
    const DOWN_LINE = 1; // 下线
    const ON_LINE   = 2; // 上线

    //红包类
    const RED_ENVELOPE_CLASS = 1;
    //抽奖类
    const LOTTERY_CLASS = 2;

    /** 是否验证关注 0-否 1-是 */
    const SUBSCRIBE_NO  = 0;
    const SUBSCRIBE_YES = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_name', 'activity_sort', 'status', 'activity_type_id'], 'required'],
            [['activity_id', 'awards_num', 'created_at', 'status', 'activity_sort', 'activity_type_id'], 'integer'],
            [['activity_name', 'activity_desc', 'activity_url', 'background_music', 'background_photo', 'activity_tips', 'title_photo', 'activity_background', 'light_one_backgroup', 'light_two_backgroup', 'lottery_button', 'click_effect', 'grid_photo', 'isCopy'], 'string'],

            [['start_time', 'end_time', 'max_frequency', 'person_day_frequency'], 'safe'],
        ];
    }

    /**
     * 状态常量数组
     * @var array
     */
    public static $statusList = array(
        self::NORMAL    => '正常',
        self::DOWN_LINE => '下线',
        self::ON_LINE   => '上线',
    );

    /**
     * 是否验证关注状态
     * @var array
     */
    public static $isVerifySubscribe = [
        ''                  => '请选择',
        self::SUBSCRIBE_NO  => '否',
        self::SUBSCRIBE_YES => '是',
    ];

    /**
     * 奖品类型
     * @author  zmy
     * @version 2017-12-08
     * @return  [type]     [description]
     */
    public static function prizesTypeList()
    {
        return [
            '' => '请选择',
            1  => '优惠券套餐',
            2  => '实物',
        ];
    }

    /**
     * 收货状态数组
     * @author  zmy
     * @version 2017-12-08
     * @return  [type]     [description]
     */
    public static function shipList()
    {
        return [
            '' => '请选择',
            1  => '未发货',
            2  => '已发货',
        ];
    }

    /**
     * 搜索时使用
     */
    public static function statusListSearch()
    {
        return [
            ''              => '请选择',
            '3'             => '待上线',
            self::ON_LINE   => '上线',
            self::DOWN_LINE => '下线',
            '4'             => '已过期',
        ];
    }

    /**
     * 上下线选择数组
     * @author  zmy
     * @version 2017-11-21
     * @return  [type]           [description]
     */
    public static function activityStatusList()
    {
        return [
            ''              => '请选择',
            self::DOWN_LINE => '下线',
            self::ON_LINE   => '上线',
        ];
    }

    /**
     * 获取奖项数目数组
     * @author  zmy
     * @version 2017-11-21
     * @return  [type]     [description]
     */
    public static function getAwardsNumList()
    {
        return [
            '3' => '三个',
            '4' => '四个',
            '5' => '五个',
            '6' => '六个',
            '7' => '七个',
            '8' => '八个',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_id'          => '活动Id',
            'activity_name'        => '活动名称',
            'activity_desc'        => '活动描述',
            'created_at'           => '创建时间',
            'status'               => '活动状态',
            'start_time'           => '活动开始时间',
            'end_time'             => '活动结束时间',
            'activity_url'         => '活动地址',
            'activity_sort'        => '活动排序',
            'createFrom'           => '上线开始时间',
            'createTo'             => '上线结束时间',
            'person_day_frequency' => '单人单天参与次数',
            'max_frequency'        => '单人最多参与次数',
            'awards_num'           => '奖项数目',
            'background_music'     => '背景音乐',
            'background_photo'     => '背景图片',
            'activity_tips'        => '活动锦囊',
            'title_photo'          => '标题图片',
            'activity_background'  => '九宫格活动背景图',
            'light_one_backgroup'  => '灯泡背景1',
            'light_two_backgroup'  => '灯泡背景2',
            'lottery_button'       => '抽奖按钮',
            'click_effect'         => '点选效果',
            'activity_type_id'     => '活动类型',
        ];
    }

    /**
     * 获取修改状态
     * @author  zmy
     * @version 2017-12-01
     * @param   [type]     $model [活动model]
     * @return  [type]            [description]
     */
    public static function getIsDisplayUpdate($model)
    {
        $ret       = true;
        $date      = time();
        $startTime = $model->start_time;
        $endTime   = $model->end_time;
        if ($date > $model->end_time) {
            $ret = false;
        }
        return $ret;
    }

    /**
     * 获取删除状态
     * @author  zmy
     * @version 2017-12-01
     * @param   [type]     $model [活动model]
     * @return  [type]            [description]
     */
    public static function getIsDisplayDelete($model)
    {
        $date = time();
        // 未过期
        if ($model->start_time > $date && ($model->status != self::ON_LINE || $date <= $model->end_time)) {
            // 可删除情况
            return true;
        }
        return false;
    }

    /**
     * 获取状态
     * @author  zmy
     * @version 2017-12-01
     * @param   [boolen]    $model [活动对象]
     * @param   [string]    $isNeedConversion [是否需要转化为字符]
     * @return  [string]            [状态值]
     */
    public static function getStatus($model, $isNeedConversion = 0)
    {
        $date = time();
        if (!$isNeedConversion) {
            $startTime = $model->start_time;
            $endTime   = $model->end_time;
        } else {
            $startTime = strtotime($model->start_time);
            $endTime   = strtotime($model->end_time);
        }
        if ($model->status == self::ON_LINE && $date < $startTime) {
            return '待上线';
        }
        if ($date > $endTime) {
            return '已过期';
        }
        return $model::$statusList[$model->status];
    }

    /**
     * 编辑领券活动所需数据
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  integer    $isNew 是否新增数据 1-是 0-否
     * @return array|json
     */
    public static function updateCouponActivity($isNew = 1)
    {
        $couponGroupList = \backend\models\QuickSendCoupon::getCouponPackage();
        unset($couponGroupList['']);
        $couponList = \backend\models\QuickSendCoupon::getQuickSendCouponList(['not_intrest' => 1]);
        unset($couponList['']);
        $activityStatus = Activity::activityStatusList();
        unset($activityStatus['']);
        $isVerifySubscribeLsit = Activity::$isVerifySubscribe;
        unset($isVerifySubscribeLsit['']);
        $activity = [];
        if ($isNew != 1) {
            $id       = Yii::$app->request->get('id');
            $activity = ActivityApi::getCouponActivityByActivityId($id);
        }
        return [
            'activity'          => $activity,
            'activityStatus'    => $activityStatus,
            'isVerifySubscribe' => $isVerifySubscribeLsit,
            'couponGroupList'   => $couponGroupList,
            'couponList'        => $couponList,
        ];
    }

    /**
     * 保存领券活动
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  arrray     $data 需要保存的数据
     * @return json
     */
    public static function saveCouponActivity($data)
    {
        $data['activityData']['start_time'] = strtotime($data['activityData']['start_time']);
        $data['activityData']['end_time']   = strtotime($data['activityData']['end_time']);
        return ActivityApi::saveCouponActivity($data);
    }
}
