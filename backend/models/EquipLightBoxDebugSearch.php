<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipLightBoxDebug;

/**
 * EquipLightBoxDebugSearch represents the model behind the search form about `backend\models\EquipLightBoxDebug`.
 */
class EquipLightBoxDebugSearch extends EquipLightBoxDebug
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'is_del', 'light_box_id'], 'integer'],
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
        $query = EquipLightBoxDebug::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'is_del' => EquipLightBoxDebug::DEL_NOT,
            'light_box_id' => $this->light_box_id
        ]);
        $query->andFilterWhere(['like', 'debug_item', $this->debug_item]);

        return $dataProvider;
    }
}
