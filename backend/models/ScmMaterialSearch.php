<?php

namespace backend\models;

use backend\models\ScmMaterial;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmMaterialSearch represents the model behind the search form about `backend\models\ScmMaterial`.
 */
class ScmMaterialSearch extends ScmMaterial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'weight', 'create_time', 'material_type','is_operation'], 'integer'],
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
        $query = ScmMaterial::find()->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'            => $this->id,
            'supplier_id'   => $this->supplier_id,
            'weight'        => $this->weight,
            'material_type' => $this->material_type,
            'create_time'   => $this->create_time,
            'is_operation'  => $this->is_operation
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
