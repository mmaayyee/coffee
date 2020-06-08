<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipDebug;

/**
 * EquipDebugSearch represents the model behind the search form about `backend\models\EquipDebug`.
 */
class EquipDebugSearch extends EquipDebug
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'equip_type_id'], 'integer'],
            [['debug_item'], 'safe'],
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
        // echo "<pre/>";print_r($params);die;
        $query = EquipDebug::find();
        $query->andFilterWhere([
            'is_del' => EquipDebug::DEL_NOT,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
            'equip_type_id' => $this->equip_type_id
        ]);

        $query->andFilterWhere(['like', 'debug_item', $this->debug_item]);

        return $dataProvider;
    }
}
