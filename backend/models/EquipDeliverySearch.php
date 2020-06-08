<?php

namespace backend\models;

use backend\models\EquipDelivery;
use common\models\Api;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipDeliverySearch represents the model behind the search form about `backend\models\EquipDelivery`.
 */
class EquipDeliverySearch extends EquipDelivery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'equip_type_id', 'delivery_result', 'delivery_time', 'delivery_status', 'create_time', 'delivery_number', 'is_ammeter', 'is_lightbox', 'update_time'], 'integer'],
            [['sales_person', 'build_id', 'reason', 'remark', 'special_require', 'grounds_refusal', 'orgId', 'orgType'], 'safe'],
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
        $query = EquipDelivery::find()
            ->alias('ed')
            ->orderBy('Id DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        //当前登录用户所属分公司ID
        $orgId = Manager::getManagerBranchID();
        if ($this->orgId || $orgId > 1 || $this->build_id) {
            $query->joinWith('build b');
        }
        $orgIdArr = [];
        if ($this->orgId) {
            if ((string) $this->orgType == '0') {
                $orgIdArr = [$this->orgId];
            } else {
                $where = ['parent_path' => $this->orgId];
                if ($this->orgType == 1) {
                    $where['org_id_no'] = $this->orgId;
                }
                $orgIdArr = Api::getOrgIdArray($where);
            }
        }
        if (empty($this->orgId) && $orgId > 1) {
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $orgId]);
        }
        $query->andFilterWhere([
            'equip_type_id'   => $this->equip_type_id,
            'delivery_result' => $this->delivery_result,
            'delivery_time'   => $this->delivery_time,
            'delivery_status' => $this->delivery_status,
            'create_time'     => $this->create_time,
            'is_ammeter'      => $this->is_ammeter,
            'is_lightbox'     => $this->is_lightbox,
            'update_time'     => $this->update_time,
            'b.org_id'        => $orgIdArr,
        ]);
        $query->andFilterWhere(['like', 'sales_person', $this->sales_person])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'special_require', $this->special_require])
            ->andFilterWhere(['like', 'grounds_refusal', $this->grounds_refusal])
            ->andFilterWhere(['like', 'b.name', $this->build_id]);

        return $dataProvider;
    }

    public function searchcheck($params)
    {
        $cond  = $cond  = ['not', ['delivery_status' => 0]];
        $query = EquipDelivery::find()->where($cond)->orderBy('Id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id'              => $this->Id,
            'build_id'        => $this->build_id,
            'equip_type_id'   => $this->equip_type_id,
            'delivery_result' => $this->delivery_result,
            'delivery_time'   => $this->delivery_time,
            'delivery_status' => $this->delivery_status,
            'create_time'     => $this->create_time,
            'delivery_number' => $this->delivery_number,
            'is_ammeter'      => $this->is_ammeter,
            'is_lightbox'     => $this->is_lightbox,
            'update_time'     => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'sales_person', $this->sales_person])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'special_require', $this->special_require])
            ->andFilterWhere(['like', 'grounds_refusal', $this->grounds_refusal]);

        return $dataProvider;

    }

}
