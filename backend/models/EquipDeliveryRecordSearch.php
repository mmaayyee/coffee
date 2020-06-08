<?php

namespace backend\models;

use common\models\Api;
use common\models\EquipDeliveryRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipDeliveryRecordSearch represents the model behind the search form about `common\models\EquipDeliveryRecord`.
 */
class EquipDeliveryRecordSearch extends EquipDeliveryRecord
{
    public $type = 1, $start_time, $end_time, $equip_code, $factory_code;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'equip_id', 'build_id', 'delivery_id', 'bind_status', 'create_time', 'delivery_record_status', 'un_bind_time', 'type'], 'integer'],
            [['start_time', 'end_time', 'factory_code', 'equip_code'], 'string'],
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
        $query        = EquipDeliveryRecord::find();
        $query->orderby('equip_delivery_record.create_time desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->joinWith('build b')->andFilterWhere([
                'b.org_id' => $orgId,
            ]);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->equip_code || $this->factory_code) {
            $query->joinWith('equip e')
                ->andFilterWhere(['e.equip_code' => $this->equip_code])
                ->andFilterWhere(['e.factory_code' => $this->factory_code]);
        }
        $query->andFilterWhere([
            'Id'                     => $this->Id,
            'equip_id'               => $this->equip_id,
            'build_id'               => $this->build_id,
            'delivery_id'            => $this->delivery_id,
            'bind_status'            => $this->bind_status,
            'delivery_record_status' => $this->delivery_record_status,
        ]);
        if ($this->start_time) {
            $query->andFilterWhere(['>=', 'equip_delivery_record.create_time', strtotime($this->start_time)]);
        }
        if ($this->end_time) {
            $query->andFilterWhere(['<=', 'equip_delivery_record.create_time', strtotime($this->end_time . ' 23:59:59')]);
        }
        if (!empty($params['export-btn'])) {
            return $query->all();
        }
        return $dataProvider;
    }
}
