<?php

namespace backend\models;

use backend\models\EquipRfidCard;
use common\models\Api;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipRfidCardSearch represents the model behind the search form about `backend\models\EquipRfidCard`.
 */
class EquipRfidCardSearch extends EquipRfidCard
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'org_id', 'area_type', 'create_time', 'rfid_state'], 'integer'],
            [['rfid_card_code', 'rfid_card_pass', 'member_id'], 'safe'],
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
        $query        = EquipRfidCard::find()->orderBy("id DESC");
        $this->orgArr = Api::getOrgIdNameArray();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->org_id && $this->org_id != 1) {
            $query->andFilterWhere(['like', 'org_id', ["," . $this->org_id . ","]]);
            $query->orFilterWhere(['like', 'org_id', ["," . 1 . ","]]);
        }

        $query->andFilterWhere([
            'id'         => $this->id,
            'member_id'  => $this->member_id,
            'area_type'  => $this->area_type,
            'rfid_state' => $this->rfid_state,
        ]);

        //查询日期操作
        if (!empty($params["EquipRfidCardSearch"]["startTime"]) && empty($params["EquipRfidCardSearch"]["endTime"])) {
            $startTime       = strtotime($params["EquipRfidCardSearch"]["startTime"]);
            $this->startTime = $params["EquipRfidCardSearch"]["startTime"];
            $query->andFilterWhere(['>=', 'create_time', $startTime]);
        }
        if (!empty($params["EquipRfidCardSearch"]["startTime"]) && !empty($params["EquipRfidCardSearch"]["endTime"])) {
            $startTime       = strtotime($params["EquipRfidCardSearch"]["startTime"]);
            $endTime         = strtotime($params["EquipRfidCardSearch"]["endTime"]) + (23 * 60 + 59) * 60;
            $this->startTime = $params["EquipRfidCardSearch"]["startTime"];
            $this->endTime   = $params["EquipRfidCardSearch"]["endTime"];
            $query->andFilterWhere(['>=', 'create_time', $startTime]);
            $query->andFilterWhere(['<=', 'create_time', $endTime]);
        }
        if (empty($params["EquipRfidCardSearch"]["startTime"]) && !empty($params["EquipRfidCardSearch"]["endTime"])) {
            $endTime       = strtotime($params["EquipRfidCardSearch"]["endTime"]) + (23 * 60 + 59) * 60;
            $this->endTime = $params["EquipRfidCardSearch"]["endTime"];
            $query->andFilterWhere(['<=', 'create_time', $endTime]);
        }

        $query->andFilterWhere(['like', 'rfid_card_code', $this->rfid_card_code]);
        // echo $query->createCommand()->getRawSql();exit();
        return $dataProvider;
    }
}
