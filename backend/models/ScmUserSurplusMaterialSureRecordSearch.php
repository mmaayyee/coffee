<?php

namespace backend\models;

use backend\models\ScmUserSurplusMaterialSureRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmUserSurplusMaterialSureRecordSearch represents the model behind the search form about `backend\models\ScmUserSurplusMaterialSureRecord`.
 */
class ScmUserSurplusMaterialSureRecordSearch extends ScmUserSurplusMaterialSureRecord {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'material_id', 'add_reduce', 'material_num', 'createTime', 'is_sure', 'sure_time'], 'integer'],
            [['author', 'date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
        $query = ScmUserSurplusMaterialSureRecord::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->author) {
            $orgId = Manager::getManagerBranchID();

            if ($orgId > 1) {
                $query->joinWith('user u')->andFilterWhere(['u.org_id' => $orgId]);
            }
        }
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'           => $this->id,
            'material_id'  => $this->material_id,
            'add_reduce'   => $this->add_reduce,
            'material_num' => $this->material_num,
            'createTime'   => $this->createTime,
            'is_sure'      => $this->is_sure,
            'sure_time'    => $this->sure_time,
            'author'       => $this->author,
            'date'         => $this->date,
        ]);

        return $dataProvider;
    }
}
