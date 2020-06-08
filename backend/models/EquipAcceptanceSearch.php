<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipAcceptance;

/**
 * EquipAcceptanceSearch represents the model behind the search form about `backend\models\EquipAcceptance`.
 */
class EquipAcceptanceSearch extends EquipAcceptance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'build_id', 'accept_time', 'accept_result'], 'integer'],
            [['reason', 'accept_renson'], 'safe'],
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
    public function search($params, $delivery_id)
    {
        $query = EquipAcceptance::find()->where(['delivery_id'=>$delivery_id])->orderBy('Id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
            'build_id' => $this->build_id,
            'accept_time' => $this->accept_time,
            'accept_result' => $this->accept_result,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'accept_renson', $this->accept_renson]);

        return $dataProvider;
    }
}
