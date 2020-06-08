<?php

namespace backend\models;

use backend\models\EquipRfidCardRecord;
use common\models\Api;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipRfidCardRecordSearch represents the model behind the search form about `backend\models\EquipRfidCardRecord`.
 */
class EquipRfidCardRecordSearch extends EquipRfidCardRecord
{
    public $startTime;
    public $endTime;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rfid_card_code', 'create_time', 'build_id', 'open_type', 'is_open_success'], 'integer'],
            [['equip_code', 'open_people', 'orgId', 'orgType', 'startTime', 'endTime'], 'safe'],
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
        $query = EquipRfidCardRecord::find()
            ->alias('ercr')
            ->orderBy('id DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'rfid_card_code'  => $this->rfid_card_code,
            'build_id'        => $this->build_id,
            'is_open_success' => $this->is_open_success,
            'open_type'       => $this->open_type,
            'equip_code'      => $this->equip_code,
            'open_people'     => $this->open_people,
        ]);
        $orgIdArr = [];
        if ($this->orgId != '') {
            $query->leftJoin('building b', 'b.id = ercr.build_id');
            if ($this->orgType != '') {
                $orgIdArr = $this->orgType == 1 ? Api::getOrgIdArray(['parent_path' => $this->orgId, 'org_id_no' => $this->orgId]) : [$this->orgId];
            } else {
                $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->orgId]);
            }
            $query->andFilterWhere(['b.org_id' => $orgIdArr]);
        }
        //查询日期操作
        if ($this->startTime) {
            $query->andWhere(['>=', 'ercr.create_time', strtotime($this->startTime)]);
        }
        if ($this->endTime) {
            $query->andWhere(['<=', 'ercr.create_time', strtotime($this->endTime . ' 23:59:59')]);
        }
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function exportSearch($params)
    {

        $query = EquipRfidCardRecord::find()
            ->alias('ercr')
            ->orderBy('id DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'rfid_card_code'  => $this->rfid_card_code,
            'build_id'        => $this->build_id,
            'is_open_success' => $this->is_open_success,
            'open_type'       => $this->open_type,
            'equip_code'      => $this->equip_code,
            'open_people'     => $this->open_people,
        ]);
        $orgIdArr = [];
        if ($this->orgId != '') {
            $query->leftJoin('building b', 'b.id = ercr.build_id');
            if ($this->orgType != '') {
                $orgIdArr = $this->orgType == 1 ? Api::getOrgIdArray(['parent_path' => $this->orgId, 'org_id_no' => $this->orgId]) : [$this->orgId];
            } else {
                $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->orgId]);
            }
            $query->andFilterWhere(['b.org_id' => $orgIdArr]);
        }
        //查询日期操作
        if ($this->startTime) {
            $query->andWhere(['>=', 'ercr.create_time', strtotime($this->startTime)]);
        }
        if ($this->endTime) {
            $query->andWhere(['<=', 'ercr.create_time', strtotime($this->endTime . ' 23:59:59')]);
        }
        return $query->asArray()->all();
    }
}
