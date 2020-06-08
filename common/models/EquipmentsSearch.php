<?php

namespace common\models;

use backend\models\EquipExtraLog;
use backend\models\Manager;
use common\models\Api;
use common\models\Equipments;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentsSearch represents the model behind the search form about `common\models\Equipments`.
 */
class EquipmentsSearch extends Equipments
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equip_type_id', 'equipment_status', 'operation_status', 'create_time', 'equip_operation_time', 'org_id', 'batch', 'warehouse_id', 'is_lock', 'org_type', 'organization_type'], 'integer'],
            [['build_id', 'factory_code', 'equip_code', 'factory_equip_model', 'card_number', 'pro_group_id', 'equip_extra_id'], 'safe'],
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
    public function search($params, $pagination = true)
    {
        $query = Equipments::find()
            ->leftJoin('building', 'building.id=equipments.build_id')
            ->orderBy('weight DESC,equipments.id DESC');
        if (!$pagination) {
            $dataProvider = new ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }
        $this->load($params);
        $this->orgArr = Api::getOrgIdNameArray();
        $this->org_id = $this->org_id ? $this->org_id : Manager::getManagerBranchID();
        //设备附件搜索
        if ($this->equip_extra_id) {
            $query->leftJoin('equip_extra_log l', 'l.equip_id = equipments.id');
            $query->andFilterWhere(['l.equip_extra_id' => $this->equip_extra_id, 'l.status' => EquipExtraLog::USING]);
        }
        if ($this->org_id > 1) {
            if ($this->org_type === '0') {
                $query->andWhere(['equipments.org_id' => $this->org_id]);
            } else if ($this->org_type == '1') {
                $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->org_id, 'org_id_no' => $this->org_id]);
                $query->andWhere(['equipments.org_id' => $orgIdArr]);
            } else {
                $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->org_id]);
                $query->andWhere(['equipments.org_id' => $orgIdArr]);
            }
        }
        if ($this->organization_type !== '') {
            $orgIdArr = Api::getOrgIdArray(['organization_type' => $this->organization_type]);
            $query->andWhere(['equipments.org_id' => $orgIdArr]);
        }
        $query->andFilterWhere([
            'id'                   => $this->id,
            'equip_type_id'        => $this->equip_type_id,
            'equipment_status'     => $this->equipment_status,
            'operation_status'     => $this->operation_status,
            'create_time'          => $this->create_time,
            'equip_operation_time' => $this->equip_operation_time,
            'batch'                => $this->batch,
            'is_lock'              => $this->is_lock,
            'warehouse_id'         => $this->warehouse_id,
            'pro_group_id'         => $this->pro_group_id,
        ]);
        $query->andFilterWhere(['like', 'factory_code', $this->factory_code])
            ->andFilterWhere(['like', 'equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'card_number', $this->card_number])
            ->andFilterWhere(['like', 'factory_equip_model', $this->factory_equip_model])
            ->andFilterWhere(['like', 'building.name', $this->build_id]);
        return $dataProvider;
    }
}
