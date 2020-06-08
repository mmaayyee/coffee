<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Manager;

/**
 * ManagerSearch represents the model behind the search form about `backend\models\Manager`.
 */
class ManagerSearch extends Manager
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'role', 'branch', 'realname'], 'safe'],
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
        $query = Manager::find();//->leftJoin('organization',"organization.org_id=manager.branch");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $parentPath = \backend\models\Organization::getManagerBranchPath();
        $query -> andFilterWhere(['branch' => Organization::getOrgByWhereIdList(['like','parent_path',$parentPath])]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'branch'=>$this->branch ? $this->branch : "",
        ]);

        $query->andFilterWhere(['like', 'realname', $this->realname]);
        $query->andFilterWhere(['like', 'role', $this->role]);
        return $dataProvider;
    }
}
