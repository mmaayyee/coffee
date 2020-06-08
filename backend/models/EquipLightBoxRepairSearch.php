<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EquipLightBoxRepair;

/**
 * EquipLightBoxRepairSearch represents the model behind the search form about `common\models\EquipLightBoxRepair`.
 */
class EquipLightBoxRepairSearch extends EquipLightBoxRepair
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equip_id', 'supplier_id', 'process_result', 'process_time', 'create_time'], 'integer'],
            [['remark'], 'safe'],
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
        $query = EquipLightBoxRepair::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'equip_id' => $this->equip_id,
            'supplier_id' => $this->supplier_id,
            'process_result' => $this->process_result,
            'process_time' => $this->process_time,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
