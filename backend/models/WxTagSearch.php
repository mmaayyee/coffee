<?php

namespace backend\models;

use common\models\WxTag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WxTagSearch represents the model behind the search form about `backend\models\WxTag`.
 */
class WxTagSearch extends WxTag
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagid'], 'integer'],
            [['tagname'], 'safe'],
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
        $query = WxTag::find()->orderBy('tagid');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tagid' => $this->tagid,
        ]);

        $query->andFilterWhere(['like', 'tagname', $this->tagname]);

        return $dataProvider;
    }
}
