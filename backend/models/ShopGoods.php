<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/15
 * Time: 上午9:37
 */
namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class ShopGoods extends \yii\base\Model
{
    //商品ID
    public $goods_id;
    //商品名称
    public $goods_name;
    //商品属性
    public $goods_attribute;
    //净含量
    public $suttle;
    //规格
    public $specification;
    //保质期
    public $expiration;

    //生产商
    public $producter;
    //状态
    public $status;
    //添加时间
    public $create_time;
    //详情
    public $content;
    //通用图片
    public $image;
    //开始时间
    public $begin_time;
    //截止时间
    public $end_time;
    //商品sku属性
    public $sku_attr;
    //商品sku列表
    public $sku_list;
    //删除的图片信息
    public $delete_image;
    //删除的图片信息
    public $check_fail_reason;
    //下线
    const OFFLINE = 1;
    //待审核
    const WAIT_CHECK = 2;
    //未通过
    const CHECK_NO = 3;
    //上线
    const ONLINE = 4;
    // 已删除
    const DELETE = 5;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id'          => '商品Id',
            'goods_name'        => '商品名称',
            'goods_attribute'   => '商品属性',
            'suttle'            => '净含量',
            'specification'     => '规格',
            'expiration'        => '保质期',
            'create_time'       => '创建时间',
            'status'            => '状态',
            'producter'         => '出品方',
            'content'           => '详情',
            'image'             => '通用图片',
            'check_fail_reason' => '审核未通过原因',
        ];
    }

    public function rules()
    {
        return [
            [['goods_name'], 'unique', 'on' => ['create']],
            [['goods_name'], 'required', 'on' => ['create', 'update']],
            [['status', 'create_time'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['goods_name'], 'string', 'max' => 100],
            [['goods_attribute'], 'string', 'max' => 500],
            [['image'], 'string', 'max' => 800],
            [['check_fail_reason'], 'string', 'max' => 200],
            [['goods_id', 'specification', 'expiration', 'suttle', 'producter','check_fail_reason'], 'safe'],
        ];
    }

    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  wangxl
     * @version 2017-12-21
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;var_dump($data);exit();
         return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }

    /**
     * get提交数据共用方法
     * @author  wangxl
     * @version 2017-12-21
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);
    }

    /**
     * 获取商品列表
     * @author wxl
     * @date 2017-11-11
     * @param $params
     * @return array|mixed
     */
    public static function getShopListByParam($params)
    {
        $page      = isset($params['page']) ? $params['page'] : 0;
        $goodsList = self::postBase("shop-api/filter-shop-goods", $params, '&page=' . $page);
        return !$goodsList ? [] : Json::decode($goodsList);
    }

    public function getPostType()
    {
        return [
            '' => '请选择',
            1  => '商品金额',
            2  => '商品数量',
        ];
    }

    /**
     * 获取商品状态
     * @author wxl
     * @date 2017-11-11
     * @param string $status
     * @return array
     */
    public function getStatus($status = '')
    {
        $statusList = [
            self::OFFLINE    => '下架',
            self::WAIT_CHECK => '待审核',
            self::CHECK_NO   => '未通过',
            self::ONLINE     => '上架',
            //self::DELETE     => '已删除',b
        ];
        return $status ? $statusList[$status] : $statusList;
    }
    /**
     * 修改添加商品的状态
     */
    public function getStatic($status = '')
    {
        $statusList = ['1' => '下架', '2' => '上架'];
        return $status ? $statusList[$status] : $statusList;
    }
    /**
     * 获取图片的信息
     * @author wxl
     * @date 2017-11-11
     * @param string $imageStr
     * @return string
     */
    public function getImage($imageStr = '')
    {
        $image = '';
        if ($position = strpos($imageStr, ',')) {
            $imageArray = explode(',', trim($imageStr, ','));
            foreach ($imageArray as $key => $images) {
                $image .= '<img src="' . Yii::$app->params['fcoffeeUrl'] . $images . '" alt="商品图片" width="80">';
            }
        } else {
            $image = '<img src="' . Yii::$app->params['fcoffeeUrl'] . $imageStr . '" alt="商品图片" width="80">';
        }
        return $image;
    }

    public function getGoodsAttr()
    {
        $goodsDetail = self::getShopGoodsDetail($this->goods_id);
        $data        = [];
        foreach ($goodsDetail as $key) {
            if (is_array($key)) {
                array_push($data, $key);
            }
        }
      $attrStr = '<div class="bs-example" data-example-id="bordered-table">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>商品价格</th>';
        if (!empty($data[0]["attribute"])) {
            $attrStr .= '<th>' . $data[0]["attribute"] . '</th>';
        }
        if (!empty($data[0]["attribute1"])) {
            $attrStr .= '<th>' . $data[0]["attribute1"] . '</th>';
        }
        $attrStr .= '<th>商品库存</th>
            </tr>
            </thead>
           <tbody>';
        foreach ($data as $key => $val) {
            $attrStr .= "<tr>
                        <th scope='row'>{$val['price']}</th>";
            if (!empty($val["col1"])) {
                $attrStr .= " <td>{$val["col1"]}</td>";
            }
            if (!empty($val["col2"])) {
                $attrStr .= " <td>{$val["col2"]}</td>";
            }
            $attrStr .= "<td>{$val['stock']}</td>
                    </tr>";
        }
        $attrStr .= "</tbody></table></div>";
        return $attrStr;
    }

    /**
     * 获取商品的详情
     * @author wxl
     * @date 2017-11-11
     * @param int $id
     * @return array
     */
    public static function getShopGoodsDetail($id = 0)
    {
        $goodDetail = self::getBase('shop-api/shop-goods-detail', "&goods_id=" . $id);
        return !$goodDetail ? [] : Json::decode($goodDetail);
    }

    /**
     * 删除商品
     * @author wxl
     * @date 2017-11-11
     * @param array $idList
     */
    public function delete($idList = [])
    {
        $list = self::postBase("shop-api/delete-shop-goods", $idList);
        return $list;
    }

    /**
     * 审核商品
     * @author wxl
     * @date 2017-11-11
     * @param array $idList
     * @return boole
     */
    public static function check($shopStore = [])
    {
        $list = self::postBase("shop-api/check-shop-goods", $shopStore);
        return isset($list) ? $list : [];

    }
    /**
     * 修改邮费设置
     * @author wxl
     * @date 2017-11-11
     * @param int $postType
     * @param int $amount
     * @return string
     */
    public static function addMailMethod($postType = 0, $amount = 0)
    {
        $params = ['postType' => $postType, 'amount' => $amount];
        return self::postBase("shop-api/mail-method", $params);
    }

    /**
     * 获取默认的地址信息
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public static function getMailMethod()
    {
        $data = self::getBase("shop-api/get-mail-method", "&methodId=1");
        return $data ? $data : [];
    }

    /**
     * 获取修改商品的信息
     * @author wxl
     * @date 2017-11-11
     * @param int $goodsId 商品ID
     * @return array|int
     */
    public static function getShopGoodsInfo($goodsId = 0)
    {
        $shopGoodsModel = self::getBase("shop-api/get-shop-goods-model", "&goods_id=" . $goodsId);
        return $shopGoodsModel ? $shopGoodsModel : [];
    }

    /**
     * 获取商品的sku信息
     * @author wxl
     * @date 2017-11-11
     * @param int $goodsId 商品ID
     * @return array
     */
    public static function getShopGoodsSkuInfo($goodsId = 0)
    {
        $skuInfo = self::getBase("shop-api/get-shop-goods-sku-info", "&goods_id=" . $goodsId);
        return $skuInfo ? $skuInfo : '';
    }

    /**
     * 获取商品sku列表
     * @author wxl
     * @date 2017-11-11
     * @param int $goodsId 商品ID
     * @return array|int
     */
    public static function getShopGoodsSkuList($goodsId = 0)
    {
        $skuList = self::getBase("shop-api/get-shop-goods-sku-list", "&goods_id=" . $goodsId);
        return $skuList ? $skuList : '';
    }
}
