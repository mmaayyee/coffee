<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WxDepartment;
use common\models\Api;

/**
 * WxDepartmentSearch represents the model behind the search form about `backend\models\WxDepartment`.
 */
class WxDepartmentSearch extends WxDepartment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parentid', 'sort', 'org_id', 'headquarter'], 'integer'],
            [['name'], 'safe'],
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
        $this->orgArr = Api::getOrgIdNameArray();
        $query = WxDepartment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parentid' => $this->parentid,
            'sort' => $this->sort,
            'org_id' => $this->org_id ? $this->org_id : '',
            'headquarter' => $this->headquarter,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        return $dataProvider;
    }
}
