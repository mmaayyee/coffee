<?php

namespace backend\models;

use backend\models\BuildingRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BuildingRecordSearch represents the model behind the search form of `backend\models\BuildingRecord`.
 */
class BuildingRecordSearch extends BuildingRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'creator_id', 'org_id', 'build_type_id', 'building_status', 'floor', 'created_at'], 'integer'],
            [['building_name', 'province', 'city', 'area', 'address', 'contact_name', 'contact_tel', 'build_public_info', 'build_special_info', 'build_appear_pic', 'build_hall_pic'], 'safe'],
            [['build_longitude', 'build_latitude'], 'number'],
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

        $query = BuildingRecord::find();

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
            'id'              => $this->id,
            'creator_id'      => $this->creator_id,
            'org_id'          => $this->org_id,
            'build_type_id'   => $this->build_type_id,
            'building_status' => $this->building_status,
            'floor'           => $this->floor,
            'build_longitude' => $this->build_longitude,
            'build_latitude'  => $this->build_latitude,
            'created_at'      => $this->created_at,
        ]);

        $query
            ->andFilterWhere(['like', 'building_name', $this->building_name])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'contact_name', $this->contact_name])
            ->andFilterWhere(['like', 'contact_tel', $this->contact_tel])
            ->andFilterWhere(['like', 'build_public_info', $this->build_public_info])
            ->andFilterWhere(['like', 'build_special_info', $this->build_special_info])
            ->andFilterWhere(['like', 'build_appear_pic', $this->build_appear_pic])
            ->andFilterWhere(['like', 'build_hall_pic', $this->build_hall_pic]);

        return $dataProvider;
    }
}
