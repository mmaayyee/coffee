<?php
namespace backend\models;

use backend\models\ScmWarehouseOut;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmWarehouseOutSearch represents the model behind the search form about `backend\models\ScmWarehouseOut`.
 */
class ScmWarehouseOutSearch extends ScmWarehouseOut {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status'], 'integer'],
            [['author', 'date', 'startTime', 'endTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = ScmWarehouseOut::find()->groupBy('author, date')->orderby('date desc, status');
        $query->andFilterWhere(['>', 'status', 1]);
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->joinWith('user as u')->andFilterWhere(['u.org_id' => $orgId]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $query;
        }
        $query->andFilterWhere([
            'author' => $this->author,
        ]);
        if ($this->startTime) {
            $query->andFilterWhere(['>=', 'date', $this->startTime]);
        }
        if ($this->endTime) {
            $query->andFilterWhere(['<=', 'date', $this->endTime]);
        }
        $query->andFilterWhere(['like', 'author', $this->author]);

        return $query;
    }

    public function sendSearch($params) {
        $query = ScmWarehouseOut::find()->groupBy('author, date')->orderby('date desc, status');
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->joinWith('user as u')->andFilterWhere(['u.org_id' => $orgId]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $query;
        }
        $query->andFilterWhere([
            'author' => $this->author,
        ]);
        if ($this->startTime) {
            $query->andFilterWhere(['>=', 'date', $this->startTime]);
        }
        if ($this->endTime) {
            $query->andFilterWhere(['<=', 'date', $this->endTime]);
        }
        $query->andFilterWhere(['like', 'author', $this->author]);

        return $query;
    }
}
