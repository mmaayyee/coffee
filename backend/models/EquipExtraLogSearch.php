<?php

namespace backend\models;

use common\models\Building;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipExtraLog;

/**
 * EquipExtraLogSearch represents the model behind the search form about `backend\models\EquipExtraLog`.
 */
class EquipExtraLogSearch extends EquipExtraLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equip_id', 'equip_extra_id', 'status', 'create_time'], 'integer'],
            [['create_user', 'build_id'], 'safe'],
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
        $query = EquipExtraLog::find()->OrderBy('id DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        /*if($this->build_id){
            $buildId = Building::getField('id',['name' => $this->build_id]);
            $query->andFilterWhere(['build_id' => $buildId]);
        }*/
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'build_id' => $this->build_id,
            'equip_id' => $this->equip_id,
            'equip_extra_id' => $this->equip_extra_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'create_user', $this->create_user]);

        return $dataProvider;
    }
}
