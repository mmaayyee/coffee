<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipDeliveryRead;

/**
 * EquipDeliveryReadSearch represents the model behind the search form about `backend\models\EquipDeliveryRead`.
 */
class EquipDeliveryReadSearch extends EquipDeliveryRead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'read_status', 'read_time', 'delivery_id', 'read_type'], 'integer'],
            [['userId', 'read_feedback'], 'safe'],
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
        $query = EquipDeliveryRead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
            'read_status' => $this->read_status,
            'read_time' => $this->read_time,
            'delivery_id' => $this->delivery_id,
            'read_type' => $this->read_type,
        ]);

        $query->andFilterWhere(['like', 'userId', $this->userId])
            ->andFilterWhere(['like', 'read_feedback', $this->read_feedback]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchSign($params, $sign, $delivery_id)
    {
        if($sign == 0){
            $cond = ['read_type' => 0, 'delivery_id' => $delivery_id];
        }else if($sign == 1){
            $cond = ['read_type' => 1, 'delivery_id' => $delivery_id];
        }else{
            $cond = ['read_type' => 'false', 'delivery_id' => $delivery_id];
        }

        $query = EquipDeliveryRead::find()->where($cond)->orderBy('read_time DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
            'read_status' => $this->read_status,
            'read_time' => $this->read_time,
            'delivery_id' => $this->delivery_id,
            'read_type' => $this->read_type,
        ]);

        $query->andFilterWhere(['like', 'userId', $this->userId])
            ->andFilterWhere(['like', 'read_feedback', $this->read_feedback]);

        return $dataProvider;
    }




}
