<?php

namespace backend\models;

use backend\models\LotteryWinningHint;
use common\models\ActivityApi;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LotteryWinningHintSearch represents the model behind the search form about `backend\models\LotteryWinningHint`.
 */
class LotteryWinningHintSearch extends LotteryWinningHint
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hint_id', 'hint_type', 'activity_type_id'], 'integer'],
            [['hint_text', 'hint_photo', 'second_button_photo', 'thank_participate_photo'], 'safe'],
            // [['activity_type_id'], 'integer'],
            // [['hint_success_text', 'hint_error_text', 'hint_success_photo', 'hint_error_photo', 'second_button_photo', 'thank_participate_photo'], 'safe'],
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
        $lotteryWinningHintList = ActivityApi::getLotteryWinningHintList($params);
        $dataProvider           = [];
        // if (!empty($lotteryWinningHintList['lotteryWinningHintList'])) {
        foreach ($lotteryWinningHintList['lotteryWinningHintList'] as $key => $data) {
            $lotteryWinningHint = new LotteryWinningHint();
            $lotteryWinningHint->load(['LotteryWinningHint' => $data]);
            $dataProvider[$data['hint_id']] = $lotteryWinningHint;
        }
        // }
        $lotteryWinningHintList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => empty($lotteryWinningHintList['total']) ? 0 : $lotteryWinningHintList['total'],
            'sort'       => [
                'attributes' => ['hint_id desc'],
            ],
        ]);
        return $lotteryWinningHintList;
    }
}
