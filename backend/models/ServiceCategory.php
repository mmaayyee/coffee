<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;
use common\helpers\Tools;
use common\helpers\CurlRequest;

/**
 * This is the model class for table "service_category".
 *
 * @property int $id
 * @property string $category 类型名称
 * @property int $status 类型状态
 * @property int $created_time 创建时间
 */
class ServiceCategory extends \yii\db\ActiveRecord
{
    public $id;
    public $category;
    public $status;
    public $created_time;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_time'], 'required'],
            [['status', 'created_time'], 'integer'],
            [['category'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '类别ID',
            'category' => '类别名称',
            'status' => '状态',
            'created_time' => '添加时间',
        ];
    }
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * POST 方式提交数据共用方法
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data = [], $params = '')
    {
       // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;var_dump(Json::encode($data));exit();
       return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }

    /**
     * GET 提交数据共用方法
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);

    }

    /**
     *  获取全部分类
     * @return array
     */
    public static function getCategoryList($params)
    {
        if (empty($params['page'])) {
            $page = 0;
        } else {
            $page =$params['page'];
            unset($params['page']);
        }
        $list = self::postBase("service-api/category-list", $params, '&page=' . $page);
        return !$list ? [] : Json::decode($list);
    }
    /**
     * 创建分类
     */
    public static function postCategoryCreate($data)
    {
        return self::postBase("service-api/category-create",$data);
    }
    /**
     *  修改分类
     */
    public static function getCategoryID($CategoryID = 0)
    {

        $ID = self::getBase("service-api/category-id","&id=". $CategoryID);
        return $ID  ? $ID  : [];
    }
    /**
     *  定义状态
     * @param string $status
     * @return array|mixed
     */
    public function getStatus($status = ''){
        $statusList = ['1' => '上线', '2' => '下线'];
        return $status ? $statusList[$status] : $statusList;
    }
}
