<?php

namespace backend\models;

use backend\models\EquipmentProductGroup;
use common\models\ArrayDataProviderSelf;
use common\models\EquipProductGroupApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentProductGroupSearch represents the model behind the search form about `backend\models\EquipmentProductGroup`.
 */
class EquipmentProductGroupSearch extends EquipmentProductGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_group_id', 'setup_get_coffee', 'release_version', 'release_status', 'is_update_product', 'is_update_recipe', 'is_update_progress', 'equip_type', 'pro_group_stock_info_id'], 'integer'],
            [['group_name', 'group_desc', 'setup_no_coffee_msg'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $proGroupList = EquipProductGroupApi::getProductGroupList($params);
        $dataProvider = [];
        foreach ($proGroupList['cofProGroupList'] as $key => $data) {
            $proGroup = new EquipmentProductGroup();
            $proGroup->load(['EquipmentProductGroup' => $data]);
            $dataProvider[$data['product_group_id']] = $proGroup;
        }

        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$proGroupList['total'] ? 0 : $proGroupList['total'],
            'sort'       => [
                'attributes' => ['product_group_id desc'],
            ],
        ]);
        return $coffeeProList;
    }

    /**
     * 查看该产品是否可以发布
     * @author sulingling
     * @version 2018-06-22
     * @param $model object
     * @return boolean
     */
    public static function isPublic($model)
    {
        return (!\Yii::$app->user->can('产品组发布') || $model->release_status == $model::RELEASE_YES || ($model->is_update_product == $model::UPDATE_PRODUCT_NO && $model->is_update_recipe == $model::UPDATE_RECIPE_NO && $model->is_update_progress == $model::UPDATE_PROGRESS_NO)) ? false : $model->product_group_id;
    }
}
