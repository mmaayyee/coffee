<?php

namespace backend\models;

use common\models\Equipments;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentsSearch represents the model behind the search form about `common\models\Equipments`.
 */
class EquipSyncSearch extends Equipments
{
    public $start_time, $end_time;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['start_time', 'end_time'], 'string'],
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
        $query = Equipments::find()->select('org_id,operation_status,equip_type_id,count(id) as syncnum');
        if (isset($params['all']) && $params['all'] == 1) {
            if (!isset($params['org_id']) || empty($params['org_id'])) {
                $query->groupby('operation_status,equip_type_id')->orderby('operation_status desc,equip_type_id');
            }
        } else {
            if (isset($params['org_id']) && $params['org_id']) {
                $query->where(['org_id' => $params['org_id']]);
            }

            $query->groupby('org_id,operation_status,equip_type_id')->orderby('org_id desc,operation_status,equip_type_id');
        }

        if (!($this->load($params) && $this->validate())) {
            return $query->all();
        }

        $query->andFilterWhere(['between', 'create_time', strtotime($this->start_time), strtotime($this->end_time . ' 23:59:59')]);
        return $query->all();
        // echo $query->createCommand()->getRawSql();die;

    }
}
