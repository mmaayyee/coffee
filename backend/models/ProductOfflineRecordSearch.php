<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProductOfflineRecord;

/**
 * ProductOfflineRecordSearch represents the model behind the search form about `backend\models\ProductOfflineRecord`.
 */
class ProductOfflineRecordSearch extends ProductOfflineRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['equip_code', 'build_id', 'product_name','operator', 'create_time', 'product_id'], 'safe'],
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
        $query = ProductOfflineRecord::find()->alias('p')->orderBy('id DESC');
        $query->leftJoin('equipments e', 'p.equip_code = e.equip_code');
        $query->leftJoin('building b', 'e.build_id = b.id');
        $query->leftJoin('manager m', 'p.operator = m.username');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
        ]);
        $query->andFilterWhere(['like', 'p.equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'name',  $this->build_id])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'realname', $this->operator]);

        //起始日期
        if(!empty($params["ProductOfflineRecordSearch"]["start_time"])){
            $from = strtotime($params["ProductOfflineRecordSearch"]["start_time"]);
            $this->start_time = $params["ProductOfflineRecordSearch"]["start_time"];
            $query->andFilterWhere(['>=', 'p.create_time', $from]);
        }
        //截止日期
        if(!empty($params["ProductOfflineRecordSearch"]["end_time"])){
            $to = strtotime($params["ProductOfflineRecordSearch"]["end_time"]) + 3600*24;
            $this->end_time = $params["ProductOfflineRecordSearch"]["end_time"];
            $query->andFilterWhere(['<=', 'p.create_time', $to]);
        }

        return $dataProvider;
    }
}
