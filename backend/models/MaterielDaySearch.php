<?php

namespace backend\models;

use backend\models\MaterielDay;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use common\models\Building;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaterielDaySearch represents the model behind the search form about `backend\models\MaterielDay`.
 */
class MaterielDaySearch extends MaterielDay
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['materiel_id', 'build_id', 'material_type_id', 'create_at'], 'integer'],
            [['equipment_code', 'consume_total_all', 'material_type_name', 'orgId', 'online', 'build_type', 'build_name', 'equip_type_id', 'startTime', 'endTime', 'userId'], 'safe'],
            [['consume_total', 'payment_state'], 'number'],
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
        //判断时间
        if (!empty($this->startTime) || !empty($this->endTime)) {
            $startTime = strtotime(date('Y-m', strtotime($this->startTime)));
            $endTime   = strtotime(date('Y-m', strtotime($this->endTime)));
            if ($startTime != $endTime) {
                $params['MaterielDaySearch']['startTime'] = 0;
                $params['MaterielDaySearch']['endTime']   = 0;
                $this->addError('startTime', "不可以跨月查询");
                $this->addError('endTime', "不可以跨月查询");
            } else {
                $params['MaterielDaySearch']['startTime'] = strtotime($this->startTime);
                $params['MaterielDaySearch']['endTime']   = strtotime($this->endTime);
            }
        }
        $params['page']            = isset($params['page']) ? $params['page'] : 0;
        $materielDayList           = Api::getMaterielDayList($params);
        $this->countByMaterialType = $materielDayList['countByMaterialType'];
        $dataProvider              = [];
        if ($materielDayList) {
            foreach ($materielDayList['materielDayList'] as $key => $data) {
                $materielDay = new MaterielDay();
                $materielDay->load(['MaterielDay' => $data]);
                $dataProvider[$key] = $materielDay;
            }
        }
        $materielDayList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($materielDayList['total']) && !empty($materielDayList['total']) ? $materielDayList['total'] : 0,
            'sort'       => [
                'attributes' => ['materiel_id desc'],
            ],
        ]);
        return $materielDayList;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchBuild($params)
    {
        $this->load($params);
        //判断时间
        if (!empty($this->startTime) || !empty($this->endTime)) {
            $startTime = strtotime(date('Y-m', strtotime($this->startTime)));
            $endTime   = strtotime(date('Y-m', strtotime($this->endTime)));
            if ($startTime != $endTime) {
                $params['MaterielDaySearch']['startTime'] = 0;
                $params['MaterielDaySearch']['endTime']   = 0;
                $this->addError('startTime', "不可以跨月查询");
                $this->addError('endTime', "不可以跨月查询");
            } else {
                $params['MaterielDaySearch']['startTime'] = strtotime($this->startTime);
                $params['MaterielDaySearch']['endTime']   = strtotime($this->endTime);
            }
        }
        if ($this->userId != '') {
            if ($this->orgId == 1 || $this->orgId == '') {
                $where = ['distribution_userid' => $this->userId];
            } else {
                $where = ['org_id' => $this->orgId, 'distribution_userid' => $this->userId];
            }
            $params['MaterielDaySearch']['buildList'] = Building::find()->where($where)->select('build_number')->column();
        }
        $params['page']  = isset($params['page']) ? $params['page'] : 0;
        $materielDayList = Api::getMaterielDayBuildingList($params);
        $this->countByMaterialType = $materielDayList['countByMaterialType'];
        $dataProvider              = [];
        if ($materielDayList) {
            foreach ($materielDayList['materielDayList'] as $key => $data) {
                $materielDay = new MaterielDay();
                $materielDay->load(['MaterielDay' => $data]);
                $dataProvider[$key] = $materielDay;
            }
        }
        $materielDayList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($materielDayList['total']) && !empty($materielDayList['total']) ? $materielDayList['total'] : 0,
            'sort'       => [
                'attributes' => ['materiel_id desc'],
            ],
        ]);
        return $materielDayList;
    }

}
