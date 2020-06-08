<?php

namespace backend\models;

use backend\models\ClearEquip;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ClearEquipSearch represents the model behind the search form about `app\models\ClearEquip`.
 */
class ClearEquipSearch extends ClearEquip
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clear_equip_id', 'equip_type_id'], 'integer'],
            [['code', 'remark', 'clear_code_name', 'equipment_name'], 'safe'],
            [['consum_total'], 'number'],
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
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $clearEquipList = Api::getClearEquipList($params);
        $dataProvider   = [];
        if ($clearEquipList) {
            foreach ($clearEquipList['clearList'] as $key => $data) {
                $clearEquip = new ClearEquip();
                $clearEquip->load(['ClearEquip' => $data]);
                $dataProvider[$key] = $clearEquip;
            }
        }
        $clearEquipList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($clearEquipList['totalCount']) && !empty($clearEquipList['totalCount']) ? $clearEquipList['totalCount'] : 0,
            'sort'       => [
                'attributes' => ['clear_equip_id desc'],
            ],
        ]);
        return $clearEquipList;
    }
}
