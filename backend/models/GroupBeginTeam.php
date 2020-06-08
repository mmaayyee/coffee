<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "group_begin_team".
 *
 * @property int $begin_team_id 开团表id
 * @property int $group_id 活动表id
 * @property string $main_title 活动名称
 * @property int $u_id 开团人用户ID
 * @property int $group_booking_status 拼团状态(0进行中 1成功  2 是退款中 3 已退款 4未支付)
 * @property string $begin_datatime 开始时间
 * @property string $end_datatime 截止时间
 * @property int $group_booking_num 拼团人数
 * @property double $group_booking_price 拼团价格
 * @property string $drink_ladder 饮品梯度
 * @property int $type 类型(1新手团,2老带新,3全民参与)
 * @property string $begin_time 创建时间
 * @property string $activity_img 商品图片
 * @property string $activity_details_img 详情图片(json串)
 * @property string $subhead 活动副标题
 * @property double $original_cost 商品原价
 *
 */
class GroupBeginTeam extends \yii\db\ActiveRecord
{
    public $status; // 活动状态
    public $nicknameHead; // 昵称
    public $mobileHead; // 手机号
    public $team; //团员管理

    public $nicknameMember; //团员昵称
    public $mobileMember; // 手机号

    public $begin_team_id; // 开团表id
    public $group_id; // 活动表id
    public $main_title; // 活动名称
    public $u_id; // 开团人用户ID
    public $group_booking_status; // 拼团状态(0进行中 1成功  2 是退款中 3 已退款 4未支付)
    public $begin_datatime; // 开始时间
    public $end_datatime; // 截止时间
    public $group_booking_num; // 拼团人数
    public $group_booking_price; // 拼团价格
    public $drink_ladder; // 饮品梯度
    public $type; // 类型(1新手团,2老带新,3全民参与)
    public $begin_time; // 创建时间
    public $activity_img; // 商品图片
    public $activity_details_img; // 详情图片(json串)
    public $subhead; // 活动副标题
    public $original_cost; // 商品原价

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_begin_team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['begin_team_id', 'group_id', 'u_id', 'group_booking_status', 'group_booking_num', 'type', 'status'], 'integer'],
            [['begin_datatime', 'end_datatime', 'begin_time'], 'safe'],
            [['group_booking_price', 'original_cost'], 'number'],
            [['main_title', 'drink_ladder', 'activity_img', 'subhead', 'team'], 'string', 'max' => 100],
            [['activity_details_img'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'begin_team_id'        => '开团表id',
            'group_id'             => '活动表id',
            'main_title'           => '活动名称',
            'u_id'                 => '开团人用户ID',
            'group_booking_status' => '拼团状态',
            'begin_datatime'       => '时间:', //开始时间
            'end_datatime'         => '---', //截止时间
            'group_booking_num'    => '拼团人数',
            'group_booking_price'  => '拼团价格',
            'drink_ladder'         => '饮品梯度',
            'type'                 => '类型',
            'begin_time'           => '创建时间',
            'activity_img'         => '商品图片',
            'activity_details_img' => '详情图片(json串)',
            'subhead'              => '活动副标题',
            'original_cost'        => '商品原价',

            'status'               => '活动状态',
            'nickname'             => '昵称',
            'mobile'               => '手机号',
            'team'                 => '团员管理',
        ];
    }

    /**
     *  下拉筛选
     *  @column string 字段
     *  @value mix 字段对应的值，不指定则返回字段数组
     *  @return mix 返回某个值或者数组
     */
    public static function dropDown($column, $value = null)
    {
        $dropDownList = [
            "type"                 => [
                ''  => "请选择",
                "1" => "新手团",
                "2" => "老带新",
                "3" => "全民参与",
            ],
            "status"               => [
                ''  => "请选择",
                "0" => "下线",
                "1" => "上线",
                "2" => "待上线",
            ],
            "new_type"             => [
                ''  => "请选择",
                "0" => "未定义",
                "1" => "无购买用户",
                "2" => "无付费购买",
            ],
            "group_booking_status" => [
                ''  => "请选择",
                "0" => "进行中",
                "1" => "成功",
                "2" => "退款中",
                "3" => "已退款",
                // "4" => "未支付",
            ],
        ];
        //根据具体值显示对应的值
        if ($value !== null) {
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column][$value] : false;
        }

        //返回关联数组，用户下拉的filter实现
        else {
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column] : false;
        }

    }

    // 获取 客服搜索 页面数据
    public static function getIndex($data = [])
    {
        $params         = !empty($data['GroupBeginTeamSearch']) ? $data['GroupBeginTeamSearch'] : [];
        $params['page'] = $data['page'];
        $groupDate      = self::postBase("group-booking-api/service-search", $params);
        return Json::decode($groupDate);
    }

    // 获取活动名称
    public static function getMainTitle()
    {
        $groupDate = self::getBase("group-booking-api/get-main-title");
        return Json::decode($groupDate);
    }

    // 获取 单个团数据
    public static function getOne($id)
    {
        $url       = '?begin_team_id=' . $id;
        $groupDate = self::getBase("group-booking-api/service-search-one", $url);
        return Json::decode($groupDate);
    }

    public static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;
        // var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
//         echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
