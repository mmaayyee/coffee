<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EstimateStatistics;

/**
 * EstimateStatisticsSearch represents the model behind the search form of `backend\models\EstimateStatistics`.
 */
class EstimateStatisticsSearch extends EstimateStatistics
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'org_id', 'status', 'type'], 'integer'],
            [['material_info', 'date','send_date','distribution_date','startTime','endTime'], 'safe'],
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
        $query = EstimateStatistics::find()->orderBy('date DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $orgId = Manager::getManagerBranchID();
        if($orgId > 1){
            $query->andFilterWhere([
                'org_id' => $orgId
            ]);
        }
        if(!empty($params['EstimateStatisticsSearch'])){
            $param = $params['EstimateStatisticsSearch'];
            if(!empty($param['startTime'])){
                $query->andFilterWhere(['>=','date',$param['startTime']]);
            }
            if(!empty($param['endTime'])){
                $query->andFilterWhere(['<=','date',$param['endTime']]);
            }
            if(!empty($param['org_id'])&&$orgId <= 1){
                $query->andFilterWhere(['org_id'=>$param['org_id']]);
            }
        }
        return $dataProvider;
    }
}
