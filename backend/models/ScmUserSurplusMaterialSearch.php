<?php

namespace backend\models;

use backend\models\ScmUserSurplusMaterial;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ScmUserSurplusMaterialGram;

/**
 * ScmUserSurplusMaterialSearch represents the model behind the search form about `backend\models\ScmUserSurplusMaterial`.
 */
class ScmUserSurplusMaterialSearch extends ScmUserSurplusMaterial {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'material_id', 'material_num'], 'integer'],
            [['author', 'date'], 'safe'],
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
        $query = ScmUserSurplusMaterial::find();

        // add conditions that should always apply here

        $query->andFilterWhere(['>','material_num','0']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'           => $this->id,
            'material_id'  => $this->material_id,
            'material_num' => $this->material_num,
            'author'       => $this->author,
            'date'         => $this->date,
        ]);

        return $dataProvider;
    }

    public function userSearch($author) {
        $query = ScmUserSurplusMaterial::find();
        $query->andFilterWhere(['author' => $author]);
        return $query->all();
    }

    /**
     * 获取散料数据
     * @author wxl
     * @param $author
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getGramList($author){
        $query = ScmUserSurplusMaterialGram::find();
        $query->andFilterWhere(['author' => $author]);
        return $query->all();
    }
}
