<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AppVersionManagement;

/**
 * AppVersionManagementSearch represents the model behind the search form about `backend\models\AppVersionManagement`.
 */
class AppVersionManagementSearch extends AppVersionManagement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'equip_type_id'], 'integer'],
            [['big_screen_version', 'small_screen_version'], 'safe'],
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
        $query = AppVersionManagement::find()->orderBy("Id DESC");

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
            'Id' => $this->Id,
            'equip_type_id' => $this->equip_type_id,
        ]);

        $query->andFilterWhere(['like', 'big_screen_version', $this->big_screen_version])
            ->andFilterWhere(['like', 'small_screen_version', $this->small_screen_version]);

        return $dataProvider;
    }
}
