<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ServiceQuestion;
use common\models\ArrayDataProviderSelf;

/**
 * ServiceQuestionSearch represents the model behind the search form of `backend\models\ServiceQuestion`.
 */
class ServiceQuestionSearch extends ServiceQuestion
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'static', 'create_time', 's_c_id'], 'integer'],
            [['question', 'answer','question_key'], 'safe'],
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
        $this->load($params);
        $QuestionList = ServiceQuestion::getQuestionList($params);
        $dataProvider = [];
        if(isset($QuestionList['QuestionList'])){
            foreach ($QuestionList['QuestionList'] as $key => $data) {
                $proGroup = new ServiceQuestion();
                $proGroup->load(['ServiceQuestion' => $data]);
                $dataProvider[$data['id']] = $proGroup;
            }
        }
        $QuestionList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 10,
            ],
            'totalCount' => !isset($QuestionList['total']) ? 0 : $QuestionList['total'],
        ]);
        return $QuestionList;
    }
}
