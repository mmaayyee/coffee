<?php

namespace backend\models;

use backend\models\EquipBrew;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipBrewSearch represents the model behind the search form about `backend\models\EquipBrew`.
 */
class EquipBrewSearch extends EquipBrew
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'brew_time', 'create_time'], 'integer'],
            [['equip_code', 'build_name'], 'safe'],
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
        $query = EquipBrew::find()
            ->orderBy("create_time DESC")
            ->alias('v')
            ->joinWith('equip e')
            ->leftJoin('building b', 'b.id = e.build_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'v.equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'b.name', $this->build_name]);

        return $dataProvider;
    }
}
