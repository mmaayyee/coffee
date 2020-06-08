<?php

namespace backend\models;

use backend\models\EquipLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipLogSearch represents the model behind the search form about `backend\models\EquipLog`.
 */
class EquipLogSearch extends EquipLog
{
    public $type;
    public $startTime;
    public $endTime;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'log_type', 'equip_status', 'create_time', 'type'], 'integer'],
            [['content', 'equip_code', 'startTime', 'endTime'], 'safe'],
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
        $query = EquipLog::find()->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'           => $this->id,
            'log_type'     => $this->log_type,
            'equip_code'   => $this->equip_code,
            'equip_status' => $this->equip_status,
        ]);
        if ($this->startTime) {
            $query->andFilterWhere(['>=', 'create_time', strtotime($this->startTime)]);
        }
        if ($this->endTime) {
            $query->andFilterWhere(['<=', 'create_time', strtotime($this->endTime)]);
        }
        $query->andFilterWhere(['like', 'content', $this->content]);
        return $dataProvider;
    }
}
