<?php

namespace common\models;

use common\models\BaseApi;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @property CoffeeGroupProduct[] $coffeeGroupProducts
 */
class LightFoodProduct extends \yii\base\Model
{
    /**
     * 获取轻食单品ID和name对应的数组
     * @author zhenggangwei
     * @date   2019-04-09
     * @return array
     */
    public static function getStatusProductList()
    {
        $productList = self::getProductList();
        return ArrayHelper::map($productList, 'lf_product_id', 'lf_product_name');
    }

    /**
     * 获取轻食单品列表
     * @author zhenggangwei
     * @date   2019-04-09
     * @return array
     */
    public static function getProductList()
    {
        $products    = BaseApi::getBase('light-food-product-api/get-product-list');
        $productList = Json::decode($products);
        return $productList['data'];
    }
    /**
     * 更新产品状态
     * @author zhenggangwei
     * @date   2019-04-09
     * @return boolen     true-成功 false-失败
     */
    public static function changeProductStutus($data)
    {
        $res  = BaseApi::postBase('light-food-product-api/change-product-status', $data);
        $list = Json::decode($res);
        if ($list['error_code'] == 0) {
            return true;
        }
        return false;
    }
}
