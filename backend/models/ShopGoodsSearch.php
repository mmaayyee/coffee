<?php

namespace backend\models;

use backend\models\ShopGoods;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;

/**
 * ActivitySearch represents the model behind the search form about `backend\models\Activity`.
 */
class ShopGoodsSearch extends ShopGoods
{
    public $mail;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'status', 'create_time'], 'integer'],
            [['goods_name', 'goods_attribute', 'content', 'image', 'begin_time', 'end_time', 'check_fail_reason','mail'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * 通过接口获取商品信息
     * @author wxl
     * @date 2017-11-11
     * @param $params
     * @return array|ArrayDataProviderSelf|mixed
     */
    public function search($params)
    {
        $this->load($params);
        $goodsList    = ShopGoods::getShopListByParam($params);
        $this->mail=$goodsList['mail'];
        $dataProvider = [];
        if (isset($goodsList['shopGoodsList'])) {
            foreach ($goodsList['shopGoodsList'] as $key => $data) {
                $proGroup = new ShopGoods();
                $proGroup->load(['ShopGoods' => $data]);
                $dataProvider[$data['goods_id']] = $proGroup;
            }
        }
        $goodsList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 10,
            ],
            'totalCount' => !isset($goodsList['total']) ? 0 : $goodsList['total'],
            'sort'       => [
                'attributes' => ['goods_id desc'],
            ],
        ]);
        return $goodsList;

    }

}
