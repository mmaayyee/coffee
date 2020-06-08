<?php

namespace backend\models;

use backend\models\CoffeeGroup;
use backend\models\CoffeeProductSetup;
use backend\models\CoffeeRecipe;
use backend\models\Coupon;
use common\helpers\Tools;
use common\models\ActiveDiscountAssoc;
use common\models\CoffeeProductApi;
use common\models\OrderGoods;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * This is the model class for table "coffee_product".
 *
 * @property integer $cf_product_id
 * @property string $cf_product_name
 * @property double $cf_product_price
 * @property integer $cf_product_status
 * @property string $cf_product_thumbnail
 *
 * @property CoffeeGroupProduct[] $coffeeGroupProducts
 */
class CoffeeProduct extends \yii\db\ActiveRecord
{

    /** 上架状态 */
    const ONLINE = 0;

    /** 营销形式常量定义 1-无 2-折扣 3-满减 4-买赠 5-买返*/
    const NO_MARKET       = 1;
    const DISCOUNT_MARKET = 2;
    const MINUS_MARKET    = 3;
    const SEND_MARKET     = 4;
    const RETURN_MARKET   = 5;

    /** 限购方式常量定义 1-每人总数 2-每人每天总数 3-活动总数*/
    const EVERYONE = 1;
    const EVERYDAY = 2;
    const TOTALNUM = 3;

    /** 单品类型 0-普通单品 1-臻选单品*/
    const ORDINARY_PRODUCT = 0;
    const ELECTION_PRODUCT = 1;

    /** 饮品种类 0-咖啡 1-茶 2-无因 3-其它 */
    const KIND_COFFEE        = 0;
    const KIND_TEA           = 1;
    const KIND_DECAFFEINATED = 2;
    const KIND_OTHER         = 3;

    /**
     * @var UploadedFile file attribute
     */
    public $cf_product_id; // 单品ID
    public $cf_product_name; // 单品名称
    public $cf_product_price; // 单品价格
    public $cf_special_price; // 特价 （优先）
    public $cf_product_status; // 状态0正常，1下架 ，2已过期
    public $cf_product_update_at; // 更新时间
    public $cf_texture; // 口感
    public $cf_product_hot; // 是否热饮0热饮1冷饮
    public $cf_source_id; // 单品原ID
    public $cf_market_type; // 营销方式 1-无 2-折扣 3-满减 4-买赠 5-买返
    public $cf_product_english_name; // 单品英文名称
    public $cf_product_thumbnail; // 缩略图片
    public $cf_restriction_type; // 限购方式（json数组）1-每人 2-每天 3-总量
    public $cf_start_time; // 开始上架时间
    public $cf_end_time; // 结束上架时间
    public $price_start_time; // 特价开始时间
    public $price_end_time; // 特价结束时间
    public $file; // 上传文件
    public $file1; // 上传文件
    public $restriction; // 限购方式
    public $market; // 营销形式
    public $cf_product_cover; // 单品图
    public $equipment_type; //设备类型
    public $cf_product_type; // 单品类型
    public $cf_product_kind; // 饮品种类
    //冷热类型
    public static $coldOrHot = ['0' => '热饮', '1' => '冷饮'];
    // 单品类型列表
    public $productType = [
        ''                     => '请选择',
        self::ORDINARY_PRODUCT => '普通单品',
        self::ELECTION_PRODUCT => '臻选单品',
    ];

    // 单品类型列表
    public $productKind = [
        self::KIND_COFFEE        => '咖啡',
        self::KIND_TEA           => '茶',
        self::KIND_DECAFFEINATED => '无因',
        self::KIND_OTHER         => '其它',
    ];
    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cf_product_name', 'cf_product_price', 'cf_product_status', 'cf_product_hot', 'file', 'cf_product_type'], 'required'],
            [['cf_product_price', 'cf_source_id', 'cf_market_type'], 'number'],
            [['cf_product_id', 'cf_product_status', 'cf_product_update_at', 'cf_product_hot'], 'integer'],
            [['cf_product_name', 'cf_product_english_name', 'cf_product_thumbnail', 'cf_texture'], 'string', 'max' => 255],
            [['cf_restriction_type'], 'string', 'max' => 200],
            [['cf_product_id', 'cf_product_name', 'cf_product_price', 'cf_special_price', 'cf_product_status', 'cf_product_update_at', 'cf_start_time', 'cf_end_time', 'price_start_time', 'cf_texture', 'cf_product_hot', 'cf_source_id', 'cf_market_type', 'cf_product_english_name', 'cf_product_thumbnail', 'cf_product_cover', 'cf_restriction_type', 'cf_product_kind'], 'safe'],
            [['file'], 'file', 'extensions' => 'gif, jpg, png'],
            [
                'price_end_time',
                'required',
                'when'       => function ($model, $attribute) {
                    return !empty($model->price_start_time);
                },
                'whenClient' => "function (attribute, value) {
                    return $('#coffeeproduct-price_start_time').val() != '';
                }",
            ],
            [
                'price_start_time',
                'required',
                'when'       => function ($model, $attribute) {
                    return !empty($model->price_end_time);
                },
                'whenClient' => "function (attribute, value) {
                    return $('#coffeeproduct-price_end_time').val() != '';
                }",
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cf_product_id'           => '单品ID',
            'cf_product_name'         => '单品名称',
            'cf_product_english_name' => '单品英文名称',
            'cf_product_price'        => '单品价格(元)',
            'cf_special_price'        => '手机端特价',
            'cf_product_status'       => '单品状态',
            'cf_product_thumbnail'    => 'icon图',
            'file'                    => '单品图片',
            'cf_product_hot'          => '冷热类型',
            'cf_texture'              => '口感',
            'cf_start_time'           => '开始上架时间',
            'cf_end_time'             => '结束上架时间',
            'cf_restriction_type'     => '限购方式',
            'cf_market_type'          => '营销形式',
            'cf_source_id'            => '选择单品',
            'cf_set_total'            => '限购杯数',
            'cf_buy_total'            => '已售出',
            'price_start_time'        => '特价开始时间',
            'price_end_time'          => '特价结束时间',
            'cf_product_cover'        => 'banner图',
            'equipment_type'          => '设备类型',
            'cf_product_type'         => '单品类型',
            'cf_product_kind'         => '饮品种类',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffeeProduct()
    {
        return $this->hasOne(self::className(), ['cf_product_id' => 'cf_source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffeeGroupProducts()
    {
        return $this->hasMany(CoffeeGroupProduct::className(), ['cf_product_id' => 'cf_product_id']);
    }

    /**
     * 获取图片
     * @author  zmy
     * @version 2017-09-22
     * @param   [type]     $cfProductCover [description]
     * @return  [type]                     [description]
     */
    public function getCover($cfProductCover)
    {
        return "<img src='" . $cfProductCover . "' width='100' height='100'/>";
    }
    /**
     * 优惠券关联表
     */
    public function getProductCoupon()
    {
        return $this->hasMany(Coupon::className(), ['product_id' => 'cf_product_id']);
    }

    /**
     * 获取单品类型
     * @author  zgw
     * @version 2017-03-06
     * @return  [type]                 [description]
     */
    public function getProductType()
    {
        return empty($this->productType[$this->cf_product_type]) ? '' : $this->productType[$this->cf_product_type];
    }

    /**
     * 获取饮品种类
     * @author zhenggangwei
     * @date   2019-01-22
     * @return string
     */
    public function getProductKind()
    {
        return empty($this->productKind[$this->cf_product_kind]) ? '' : $this->productKind[$this->cf_product_kind];
    }

    /**
     * 根据单品ID，查询单品数据
     * @author  zmy
     * @version 2017-09-05
     * @param   [string]     $id [单品ID]
     * @return  [obj]            [model]
     */
    public static function getCoffeeProductInfo($id)
    {
        $model      = new self();
        $cofProList = CoffeeProductApi::getCoffeeProductInfo($id);
        $model->load(['CoffeeProduct' => $cofProList]);
        return $model;
    }

    /**
     * @param int $productID 单品ID
     * @param int $recipeId  配方ID
     *
     */
    public function getCoffeeSetup($productID, $recipeId)
    {
        return CoffeeProductSetup::find()->where(['product_id' => $productID, 'recipe_id' => $recipeId])->all();
    }

    /**
     * 获取单品状态
     * @return string 单品状态
     */
    public function getStatus()
    {
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->cf_product_status];
    }

    /**
     * 获取单品类型数组
     * @return array 单品数据数组
     */
    public function getTypeArray()
    {
        return array(
            ''  => '请选择',
            '0' => '热饮',
            '1' => '冷饮',
        );
    }

    /**
     * 获取单品类型
     * @return string 单品类型
     */
    public function getType()
    {
        $typeArray = $this->getTypeArray();
        return $typeArray[$this->cf_product_hot];
    }

    // /**
    //  * 获取单品状态数组
    //  * @return array 单品数据数组
    //  */
    // public function getStatusArr()
    // {
    //     return array(
    //         ''  => '请选择',
    //         '0' => '正常',
    //         '1' => '下架',
    //         '2' => '已过期',
    //     );
    // }

    /**
     * 获取单品状态数组
     * @return array 单品数据数组
     */
    public function getStatusArray()
    {
        return array(
            ''  => '请选择',
            '0' => '正常',
            '1' => '下架',
            '2' => '已过期',
            '3' => '退休',
        );
    }

    /**
     * 获取所有设备类型
     * @author wxl
     * @return array
     */
    public function getEquipmentType()
    {
        $equipmentTypeList = CoffeeProductApi::GetEquipmentTypeList();
        return !$equipmentTypeList ? [] : Json::decode($equipmentTypeList);
    }

    /**
     * 获取上架咖啡单品数组
     * @return array 上架咖啡单品数组
     */
    public static function getOnlineProduct()
    {
        $rsArray = array('' => '请选择');
        $rs      = CoffeeProduct::findAll(["cf_product_status" => self::ONLINE]);
        foreach ($rs as $row) {
            $rsArray[$row->cf_product_id] = $row->cf_product_name;
        }
        return $rsArray;
    }

    /**
     * 获取所有咖啡单品数组
     * @return array 咖啡单品数组
     */
    public static function getAllProduct($sourceID = '', $type = 1)
    {
        $rsArray = $type == 1 ? array('' => '请选择') : [];
        $query   = CoffeeProduct::find();
        if ($sourceID === 0) {
            $query->andwhere(['cf_source_id' => $sourceID]);
        } else if ($sourceID == 1) {
            $query->andWhere(['>', 'cf_source_id', 0]);
        }
        $rs = $query->all();
        foreach ($rs as $row) {
            $rsArray[$row->cf_product_id] = $row->cf_product_name;
        }
        return $rsArray;
    }

    /**
     * 获取单品url
     * @return string 单品url
     */
    public function getDetailUrl()
    {
        return Url::to(["site/product-detail", 'id' => $this->cf_product_id]);
    }

    /**
     * 获取盎司系列配方数据列表
     */
    public function getSetupList($id)
    {
        $setupList = array();
        $typeArray = self::getVolumeArray();
        $retRecipe = CoffeeRecipe::find()->select(['recipe_id', 'readable_attribute'])->asArray()->all();
        $recipeArr = array();
        foreach ($retRecipe as $key => $value) {

            $jsonValue[$value['recipe_id']] = json_decode($value["readable_attribute"]);

        }

        foreach ($jsonValue as $key => $value) {
            array_push($value, 'setup_id');
            $recipeArr[$key] = $value;
        }
        foreach ($typeArray as $typeKey => $typeName) {
            if ($typeKey === '') {
                continue;
            }

            $dataProvider        = CoffeeProductSetup::find()->select($recipeArr[$typeKey])->where(["product_id" => $id, 'recipe_id' => $typeKey])->asArray()->all();
            $setupList[$typeKey] = $dataProvider;
        }
        return $setupList;
    }

    /**
     * 根据id查询name
     * @author  zmy
     * @version 2017-03-16
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public static function getProductNameById($id)
    {
        $model = self::find()->where(['cf_product_id' => $id])->one();
        if ($model) {
            return $model->cf_product_name;
        } else {
            return '';
        }
    }

    /**
     * 获取咖啡默认配方
     * @return array 咖啡默认配方
     */
    private function getSetupArray()
    {
        return array(
            '1' => array(
                'order'  => '1',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '2' => array(
                'order'  => '2',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '3' => array(
                'order'  => '3',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '4' => array(
                'order'  => '4',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '6' => array(
                'order'  => '6',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '7' => array(
                'order'  => '7',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            '8' => array(
                'order'  => '8',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
            'G' => array(
                'order'  => '9',
                'water'  => '0',
                'delay'  => '0',
                'volume' => '0',
                'stir'   => '0',
            ),
        );
    }

    /**
     * 获取所有单品id和name数组
     * @author  zgw
     * @version 2017-03-06
     * @return  [type]     1-有请选择 2-没有请选择
     */
    public static function getProductList($type = 1, $online = 0)
    {
        $query = self::find();
        if ($online == 1) {
            $query->andWhere(['cf_product_status' => self::ONLINE]);
        }
        $productList = $query->all();
        return Tools::map($productList, 'cf_product_id', 'cf_product_name', null, $type);
    }

    /**
     * 添加活动时将原单品中的字段赋值给该活动
     * @author  zgw
     * @version 2017-03-16
     * @param   object     $model 活动对象
     * @return  object            活动对象
     */
    private function processNewData()
    {
        // 获取原单品数据
        $sourceModel = self::findOne($this->cf_source_id);
        // 数据处理
        $this->saveTactics($sourceModel);
        // 单品图片
        $this->cf_product_thumbnail = $this->cf_product_thumbnail ? $this->cf_product_thumbnail : $sourceModel->cf_product_thumbnail;
    }

    /**
     * 保存营销形式
     * @author  zgw
     * @version 2017-03-16
     * @return  [type]            [description]
     */
    private function saveMarket()
    {
        // 获取营销形式内容
        $market = Yii::$app->request->post('market');
        // 保存营销形式
        return ActiveDiscountAssoc::saveMarketType($this->cf_market_type, 2, $this->cf_product_id, $market);
    }

    /**
     * 上传单品图片
     * @param Indexad $model
     * @return CoffeeProduct  单品
     */
    private function uploadFile()
    {
        $this->file  = UploadedFile::getInstance($this, 'file');
        $this->file1 = UploadedFile::getInstance($this, 'file1');
        if ($this->validate()) {
            if ($this->file) {
                $filePath = '../../frontend/web/';
                $fileName = 'uploads/' . time() . rand(10000, 99999) . '.' . $this->file->extension;
                $url      = Yii::$app->params['frontend'] . $fileName;
                $this->file->saveAs($filePath . $fileName);
                $this->cf_product_thumbnail = $url;
            }
            if ($this->file1) {
                $filePath = '../../frontend/web/';
                $fileName = 'uploads/' . time() . rand(10000, 99999) . '.' . $this->file1->extension;
                $url      = Yii::$app->params['frontend'] . $fileName;
                $this->file1->saveAs($filePath . $fileName);
            }
            $this->file = $this->file1 = '';
        }
    }

    /**
     * 验证活动限购是否合法
     * @author  zgw
     * @version 2017-03-29
     * @param   int     $buyNum 购买的商品数量
     * @param   int     $userId 用户id
     * @param   int     $type   类型 1-增加商品的验证 2-加入购物车，提交订单时验证
     * @return  boole           是否合法
     */
    public function restrictionVerify($buyNum = 1, $userId, $type = 1)
    {
        if (!$this->cf_source_id) {
            return true;
        }
        if ($type == 1) {
            $buyNum += 1;
        }
        // 不限购
        if ($this->cf_restriction_type) {
            // 获取限购方式
            $restrictionArr = Json::decode($this->cf_restriction_type, true);
            // 验证限购总数(有总数限购且本次购买数量+已经购买数量大于限购数)
            if ($this->cf_set_total > 0 && $buyNum + $this->cf_buy_total > $this->cf_set_total) {
                return '活动饮品已经被抢购一空，快去其它活动剁手吧！(ノ｀Д´)ノ';
            }
            // 验证每人限购总数
            if (isset($restrictionArr[self::EVERYONE])) {
                // 每人限购的总数
                $userTotal = $restrictionArr[self::EVERYONE];
                // 获取该用户在该活动中已经购买的商品数量
                $userBuyNum = OrderGoods::getActiveGoodsNum($userId, $this->cf_product_id);
                if ($userBuyNum + $buyNum > $userTotal) {
                    return '啊欧，你已达到活动购买上限，快去其它活动剁手吧！(ノ｀Д´)ノ';
                }
            }
            // 验证每天限购总数
            if (isset($restrictionArr[self::EVERYDAY])) {
                // 每天限购总数
                $dayTotal = $restrictionArr[self::EVERYDAY];
                // 该用户今天在该活动中购买商品的数量
                $todayBuyNum = OrderGoods::getTodayActiveGoodsNum($userId, $this->cf_product_id);
                if ($todayBuyNum + $buyNum > $dayTotal) {
                    return '你今天已达到活动购买上限，明天再来剁手吧！(ノ｀Д´)ノ';
                }
            }
        }
        return true;
    }

    /**
     * 查询单品是否有下架
     * @author  zmy
     * @version 2017-08-07
     * @param   [type]     $cartList [一维数组：单品ID ]
     * @return  boolean              [description]
     */
    public static function isDownProduct($cartList)
    {
        $downProduct = '';
        foreach ($cartList as $productID) {
            $product = self::findOne($productID);
            $downProduct .= isset($product->cf_product_status) && $product->cf_product_status == 1 ? $product->cf_product_name . "，" : "";
        }
        return $downProduct;
    }

    /**
     * 获取上线的单品列表
     * @author  zgw
     * @version 2017-06-22
     * @return  array      获取上线的单品列表
     */
    public static function getOnlineProductList()
    {
        return self::findAll(["cf_product_status" => self::ONLINE, 'cf_source_id' => 0]);
    }

    /**
     * 获取单品名称用逗号隔开
     * @author  zgw
     * @version 2017-07-01
     * @param   array     $productID  单品id列表
     * @return  string              单品列表
     */
    public static function getProductNames($productID)
    {
        $productNames = self::find()->select('cf_product_name')->where(['cf_product_id' => $productID])->all();
        return ArrayHelper::getColumn($productNames, 'cf_product_name');
    }

}
