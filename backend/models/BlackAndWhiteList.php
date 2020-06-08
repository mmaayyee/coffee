<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "building_holiday_status".
 *
 * @property integer $id
 * @property integer $building_id
 * @property integer $is_running
 * @property integer $create_userid
 */
class BlackAndWhiteList extends \yii\db\ActiveRecord
{
    public $add_type;
    public $user_list_type;
    public $market_type;
    public $user_content;
    public $black_white_list_remarks;
    public $username;
    public $buildname;
    public $user_id;

    /** 名单属性常量 */
    // 白名单
    const MARKET_WHITE = 1;
    // 黑名单
    const MARKET_BLACK = 2;

    /** 名单类型常量 */
    // 手机号
    const MOBILE_LIST = 1;
    // 楼宇
    const BUILDING_LIST = 2;

    /** 添加方式常量 */
    // 输入添加
    const INPUT_ADD = 1;
    // 导入添加
    const IMPORT_ADD = 2;

    // 名单属性列表
    public $marketType = [
        ''                 => '请选择',
        self::MARKET_WHITE => '白名单',
        self::MARKET_BLACK => '黑名单',
    ];

    // 名单类型列表
    public $userListType = [
        ''                  => '请选择',
        self::MOBILE_LIST   => '手机号',
        self::BUILDING_LIST => '楼宇',
    ];

    // 名单属性列表
    public $addType = [
        ''               => '请选择',
        self::INPUT_ADD  => '输入添加',
        self::IMPORT_ADD => '导入添加',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_type', 'user_list_type', 'market_type'], 'integer'],
            [['add_type', 'user_list_type', 'market_type', 'user_content'], 'required'],
            [['black_white_list_remarks'], 'string', 'max' => 100],
            [['username', 'buildname', 'user_id'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'add_type'                 => '添加方式',
            'user_list_type'           => '名单类型',
            'market_type'              => '名单属性',
            'user_content'             => '名单内容',
            'user_moblie'              => '用户手机号',
            'user_building'            => '用户日常消费楼宇',
            'black_white_list_remarks' => '备注',
            'username'                 => '手机号',
            'buildname'                => '楼宇名称',
        ];
    }

    /**
     * 获取名单属性名称
     * @author  zgw
     * @version 2017-09-05
     * @return  string     名单属性名称
     */
    public function getMarketType()
    {
        return !isset($this->marketType[$this->market_type]) ? '' : $this->marketType[$this->market_type];
    }

    /**
     * 去除utf-8的bom头
     * @author  zgw
     * @version 2017-09-27
     * @param   string     $content 要去除bom头的内容
     * @return  string              $content 去除bom头后的内容
     */
    public static function clearBom($content)
    {
        $charset[1] = substr($content, 0, 1);
        $charset[2] = substr($content, 1, 1);
        $charset[3] = substr($content, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $content = substr($content, 3);
        }
        return $content;
    }
}
