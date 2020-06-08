<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "coffee_language".
 *
 * @property int $id
 * @property int $language_type 咖语类型1设备2-指定饮品
 * @property string $language_name 咖语名称
 * @property int $language_product 咖语对应饮品0是所有设备
 * @property int $language_static 咖语状态1上线2-下线
 * @property int $language_equipment 设备ID.0是默认支持所有设备
 * @property string $language_content 咖语详细内容
 * @property int $language_time 咖语创建时间
 */
class CoffeeLanguage extends \yii\db\ActiveRecord
{

    public $id;
    public $language_type;
    public $language_product;
    public $language_static;
    public $language_equipment;
    public $language_time;
    public $language_name;
    public $language_content;
    public $language_sort;
    // 搜索开始时间
    public $start_time;
    // 搜索结束时间
    public $end_time;

    //上线状态
    const ONLINE_UP   = 1; //上线
    const ONLINE_DOWN = 2; //下线
    const BRAND       = 0; //所有品牌
    const DRINK       = 1; //饮品
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coffee_language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_type', 'language_static', 'language_name', 'language_content'], 'required'],
            [['language_type', 'language_product', 'language_static', 'language_equipment', 'language_time'], 'integer'],
            [['language_name'], 'string', 'max' => 255],
            [['language_content'], 'string', 'max' => 1000],
            [['id', 'start_time', 'end_time', 'language_sort'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'language_type'      => '咖语类型',
            'language_name'      => '咖语名称',
            'language_product'   => '对应饮品',
            'language_static'    => '咖语状态',
            'language_equipment' => '设备',
            'language_content'   => '咖语内容',
            'language_time'      => '添加时间',
            'start_time'         => '开始时间',
            'end_time'           => '结束时间',
            'language_sort'      => '咖语顺序',
        ];
    }
    /**
     * 获取上下线信息
     * @author  wbq
     * @version 2018-08-2
     * @return  [array]     [description]
     */
    public static function getOnlineStaticList($languageStatic = '')
    {
        $languageStaticList = [
            ''                => '请选择',
            self::ONLINE_UP   => '上线',
            self::ONLINE_DOWN => '下线',
        ];
        return $languageStatic === '' ? $languageStaticList : $languageStaticList[$languageStatic];
    }
    /**
     * 获取咖语类型
     * @author  wbq
     * @version 2018-08-2
     * @return  [array]     [description]
     */
    public static function getLanguageTypeList($languageType = '')
    {
        $LanguageTypeList = [
            ''          => '请选择',
            self::BRAND => '品牌',
            self::DRINK => '饮品',
        ];
        return $languageType === '' ? $LanguageTypeList : $LanguageTypeList[$languageType];
    }

    /**
     * 获取单品名字
     * @Author  : GaoYongLi
     * @DateTime: 2018/6/1
     * @return array|mixed
     */
    public static function getAllProductName()
    {
        $productNameList = self::getBase("user-consumes-api/get-product-name");
        return !$productNameList ? [] : Json::decode($productNameList);
    }
    /**
     * 获取条件搜索的咖语列表信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-10
     * @param:    [param]
     * @return
     * @param     [type]     $searchParams [搜索的条件]
     * @return    [type]                   [返回的咖语数据]
     */
    public function getCoffeeLanguageList($searchParams)
    {
        $page               = isset($searchParams['page']) ? $searchParams['page'] : 0;
        $coffeeLanguageList = self::postBase("coffee-language-api/get-coffee-languange-list", $searchParams, '?page=' . $page);
        return !$coffeeLanguageList ? [] : Json::decode($coffeeLanguageList);
    }
    /**
     * 获取需要修改的详细信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-10
     * @param:    [param]
     * @return
     * @param     [type]     $coffeeLanguageID [修改的ID]
     * @return    [type]                       [获取修改前的数据]
     */
    public static function getCoffeeLanguageInfo($coffeeLanguageID)
    {
        $coffeeLanguageModel = self::getBase("coffee-language-api/get-coffee-language-info", "?coffee_language_id=" . $coffeeLanguageID);
        return $coffeeLanguageModel ? Json::decode($coffeeLanguageModel) : [];
    }
    /**
     * 获取修改的咖语详细信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-11
     * @param:    [param]
     * @param     [type]     $coffeeLanguage [智能系统的传的数据]
     * @return    [type]                     [转为修改的obj]
     */
    public static function getObjCoffeeLanguageInfo($coffeeLanguage)
    {
        $coffeeLanguageModel                   = new self();
        $coffeeLanguageModel->id               = $coffeeLanguage['data']['id'];
        $coffeeLanguageModel->language_type    = $coffeeLanguage['data']['language_type'];
        $coffeeLanguageModel->language_product = $coffeeLanguage['data']['language_product'];
        $coffeeLanguageModel->language_content = $coffeeLanguage['data']['language_content'];
        $coffeeLanguageModel->language_static  = $coffeeLanguage['data']['language_static'];
        $coffeeLanguageModel->language_name    = $coffeeLanguage['data']['language_name'];
        $coffeeLanguageModel->language_sort    = $coffeeLanguage['data']['language_sort'];
        return $coffeeLanguageModel;
    }
    /**
     * 组装查看详情的数据对象
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-11
     * @param     [type]     $coffeeLanguage [智能系统的传的数据]
     * @return    [type]                     [转为查看详情的obj]
     */
    public static function getViewsCoffeeLanguageObj($coffeeLanguage)
    {
        $productNameList                       = CoffeeLanguage::getAllProductName();
        $coffeeLanguageModel                   = new self();
        $coffeeLanguageModel->id               = $coffeeLanguage['data']['id'];
        $coffeeLanguageModel->language_type    = self::getLanguageTypeList($coffeeLanguage['data']['language_type']);
        $coffeeLanguageModel->language_product = empty($coffeeLanguage['data']['language_product'])
        ? '所有' :
        $productNameList[$coffeeLanguage['data']['language_product']];
        $coffeeLanguageModel->language_content   = $coffeeLanguage['data']['language_content'];
        $coffeeLanguageModel->language_static    = self::getOnlineStaticList($coffeeLanguage['data']['language_static']);
        $coffeeLanguageModel->language_name      = $coffeeLanguage['data']['language_name'];
        $coffeeLanguageModel->language_equipment = empty($coffeeLanguage['data']['language_equipment']) ? '所有设备' : '';
        $coffeeLanguageModel->language_sort      = $coffeeLanguage['data']['language_sort'];
        $coffeeLanguageModel->language_time      = date('Y-m-d H:i:s',$coffeeLanguage['data']['language_time']);
        return $coffeeLanguageModel;
    }
    /**
     * POST 方式提交数据共用方法
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data = [], $params = '')
    {
         // echo Yii::$app->params['fcoffeeUrl'] . $action . $params;
         //var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    /**
     * GET 提交数据共用方法
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
