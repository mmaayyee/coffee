<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GrindEquipAssoc;

/**
 * GrindEquipAssocSearch represents the model behind the search form about `app\models\GrindEquipAssoc`.
 */
class GrindEquipAssocSearch extends GrindEquipAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grind_equip_id', 'grind_id'], 'integer'],
            [['equipment_code'], 'safe'],
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
        $query = GrindEquipAssoc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'grind_equip_id' => $this->grind_equip_id,
            'grind_id' => $this->grind_id,
        ]);

        $query->andFilterWhere(['like', 'equipment_code', $this->equipment_code]);

        return $dataProvider;
    }
}
