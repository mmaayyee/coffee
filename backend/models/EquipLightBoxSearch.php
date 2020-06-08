<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipLightBox;

/**
 * EquipLightBoxSearch represents the model behind the search form about `backend\models\EquipLightBox`.
 */
class EquipLightBoxSearch extends EquipLightBox
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_del'], 'integer'],
            [['light_box_name'], 'safe'],
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
        $query = EquipLightBox::find();
        $query->andFilterWhere([
            'is_del' => EquipLightBox::DEL_NOT,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'light_box_name', $this->light_box_name]);

        return $dataProvider;
    }
}
