<?php

namespace backend\models;

use common\models\Api;
use common\models\CoffeeProductApi;
use Yii;

/**
 * This is the model class for table "coffee_label".
 *
 * @property string $id
 * @property string $label_name 标签名称
 * @property string $desk_img_url 桌面图标
 * @property string $label_img_url 标签打标图片路径 非必填
 * @property int $online_status 1=上线 2=下线
 * @property int $access_status 1=默认 2=非默认
 * @property int $sort 排序用字段
 * @property int $status 1=数据正常
 */
class CoffeeLabel extends \yii\db\ActiveRecord
{
    public $id;
    public $label_name;
    public $desk_img_url;
    public $desk_selected_img_url;
    public $label_img_url;
    public $online_status;
    public $access_status;
    public $sort;
    public $status;
    //单品名称
    public $product_name;
    //定义常量
    const VALID_STATUS     = 1; //正常
    const NOT_VALID_STATUS = 2; //异常
    //上线状态
    const ONLINE_UP   = 1; //上线
    const ONLINE_DOWN = 2; //下线
    //标签类别
    const ACCESS_DEFAULT     = 1; //默认标签
    const ACCESS_NOT_DEFAULT = 2; //非默认标签
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coffee_label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label_name', 'desk_img_url', 'desk_selected_img_url', 'sort'], 'required'],
            [['id', 'online_status', 'access_status', 'sort', 'status'], 'integer'],
            [['label_name'], 'string', 'max' => 4],
            [['desk_img_url', 'desk_selected_img_url', 'label_img_url'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'label_name'            => '标签名称',
            'coffee_product_id'     => '产品id',
            'desk_img_url'          => '桌面图(选中前)',
            'desk_selected_img_url' => '桌面图(选中后)',
            'label_img_url'         => '标签图',
            'online_status'         => '上/下线',
            'access_status'         => '是否默认',
            'product_name'          => '产品名称',
            'sort'                  => '排序信息',
            'status'                => '状态',
        ];
    }
    /**
     * 获取 标签列表
     * @version 2018-07-31
     * @author wbq
     * @param  [array]  $where  条件
     * @return  [array]          [成功/失败]
     */
    public static function getCoffeeLabelList($params)
    {
        return CoffeeProductApi::getCoffeeLabelList($params);
    }
    /**
     * 获取 标签详情
     * @version 2018-07-31
     * @author wbq
     * @param  [array]  $where  条件
     * @return  [array]          [成功/失败]
     */
    public static function getCoffeeLabelDetail($where)
    {
        $detail = CoffeeProductApi::getCoffeeLabelDetail($where);
        if ($detail) {
            //数组降维度
            $detail['coffeeProductList'] = array_column($detail['coffeeProductList'], 'coffee_product_id');
            return $detail;
        }
        return [];
    }
    /**
     * 获取需要的产品组信息
     * @author  wbq
     * @version 2018-08-1
     * @return  [type]     [description]
     */
    public static function getProducts()
    {
        $productList = json_decode(Api::getProductList(), true); // 获取单品
        $list        = [];
        foreach ($productList as $key => $value) {
            if ($value['cf_source_id'] == 0) {
                $list[] = [
                    'id'   => $value['cf_product_id'],
                    'name' => $value['cf_product_name'],
                ];
            }
        }
        return $list;
    }
    /**
     * 获取上下线信息
     * @author  wbq
     * @version 2018-08-2
     * @return  [array]     [description]
     */
    public static function getOnlineList()
    {
        return [
            ''                => '请选择',
            self::ONLINE_UP   => '上线',
            self::ONLINE_DOWN => '下线',
        ];
    }
    /**
     * 获取标签类别
     * @author  wbq
     * @version 2018-08-2
     * @return  [array]     [description]
     */
    public static function getAccessList()
    {
        return [
            ''                       => '请选择',
            self::ACCESS_DEFAULT     => '默认标签',
            self::ACCESS_NOT_DEFAULT => '非默认标签',
        ];
    }
}
