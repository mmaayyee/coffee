<?php

namespace backend\models;

use backend\models\MaterielMonth;
use common\models\Api;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaterielMonthSearch represents the model behind the search form about `backend\models\MaterielMonth`.
 */
class MaterielMonthSearch extends MaterielMonth
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['materiel_id', 'build_id', 'material_type_id', 'create_at'], 'integer'],
            [['equipment_code', 'material_type_name', 'orgId', 'build_type', 'build_name', 'equip_type_id', 'change_value', 'startTime'], 'safe'],
            [['consume_total', 'consume_total_all'], 'number'],
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
        $params['page']    = isset($params['page']) ? $params['page'] : 0;
        $this->page = $params['page'];
        $materielMonthList = Api::getMaterielMonthList($params);
        return $materielMonthList;
    }
}
