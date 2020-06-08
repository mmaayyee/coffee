<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipSurplusMaterial;

/**
 * EquipSurplusMaterialSearch represents the model behind the search form about `backend\models\EquipSurplusMaterial`.
 */
class EquipSurplusMaterialSearch extends EquipSurplusMaterial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code', 'material_stock_code', 'date'], 'safe'],
            [['surplus_material'], 'number'],
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
        $query = EquipSurplusMaterial::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'surplus_material' => $this->surplus_material,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'material_stock_code', $this->material_stock_code]);

        return $dataProvider;
    }

    public function searchByEquipCode($params, $equipCode)
    {
        $query = EquipSurplusMaterial::find()->where(['equip_code'=>$equipCode]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        return $dataProvider;
    }

}
