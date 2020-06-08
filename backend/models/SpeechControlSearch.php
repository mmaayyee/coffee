<?php

namespace backend\models;

use backend\models\SpeechControl;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SpeechControlSearch represents the model behind the search form of `backend\models\SpeechControl`.
 */
class SpeechControlSearch extends SpeechControl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_time', 'examine_time', 'status'], 'integer'],
            [['speech_control_title', 'speech_control_content', 'start_time', 'end_time'], 'safe'],
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
        $params['page']    = isset($params['page']) ? $params['page'] : 1;
        $speechControlList = SpeechControl::getSpeechList($params);
        $dataProvider      = [];
        if (isset($speechControlList['speechControlList'])) {
            foreach ($speechControlList['speechControlList'] as $key => $data) {
                $speechControl = new SpeechControl();
                $speechControl->load(['SpeechControl' => $data]);
                $dataProvider[$data['id']] = $speechControl;
            }
        }
        $speechList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($speechControlList['total']) ? 0 : $speechControlList['total'],
            'sort'       => [
                'attributes' => ['id asc'],
            ],
        ]);
        return $speechList;
    }
}
