<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipTypeProgressProductAssoc;
use common\models\EquipProductGroupApi;
use common\models\ArrayDataProviderSelf;

/**
 * EquipTypeProgressProductAssocSearch represents the model behind the search form about `backend\models\EquipTypeProgressProductAssoc`.
 */
class EquipTypeProgressProductAssocSearch extends EquipTypeProgressProductAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'process_id','equip_type_id', 'enter_time', 'enter_sort'], 'integer'],
            [['process_name','product_name'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $progressAssocList = EquipProductGroupApi::getEquipTypeProgressAssocList($params);
        $dataProvider      = [];
        if($progressAssocList){
            foreach ($progressAssocList['progressAssocList'] as $key => $data) {
                $equipTypeProgressAssoc = new EquipTypeProgressProductAssoc();
                $equipTypeProgressAssoc->load(['EquipTypeProgressProductAssoc' => $data]);
                $dataProvider[$data['product_id']] = $equipTypeProgressAssoc;
            }
        }
        
        $equipTypeProgressAssocList = new ArrayDataProviderSelf([
            'allModels'      => $dataProvider,
            'pagination'     => [
                'pageSize'   => 20,
            ],
            'totalCount'     => !isset($progressAssocList['total']) ? 0 : $progressAssocList['total'],
            'sort'           => [
                'attributes' => ['product_id DESC'],
            ],
        ]);
        return $equipTypeProgressAssocList;
    }
}
