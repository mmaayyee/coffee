<?php

namespace backend\models;

use backend\models\MaterielLog;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaterielLogSearch represents the model behind the search form about `app\models\MaterielLog`.
 */
class MaterielLogSearch extends MaterielLog
{
    public $countByMaterialType;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['materiel_log_id', 'operaction_type', 'create_at', 'product_id', 'consume_id'], 'integer'],
            [['desc', 'activity_type', 'startTime', 'endTime', 'equipment_code', 'build_name'], 'safe'],
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
        if (empty($params['MaterielLogSearch']['startTime'])) {
            $params['MaterielLogSearch']['startTime'] = date("Y-m-d", time());
        }
        if (empty($params['MaterielLogSearch']['endTime'])) {
            $params['MaterielLogSearch']['endTime'] = date("Y-m-d", strtotime('1 days'));
        }
        $params['page']            = isset($params['page']) ? $params['page'] : 0;
        $materielLogList           = Api::getMaterielLogList($params);
        $dataProvider              = [];
        $this->countByMaterialType = $materielLogList['countByMaterialType'];
        if ($materielLogList) {
            foreach ($materielLogList['materielLogList'] as $key => $data) {
                $MaterielLog = new MaterielLog();
                $MaterielLog->load(['MaterielLog' => $data]);
                $dataProvider[$key] = $MaterielLog;
            }
        }
        $materielLogList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !empty($materielLogList['totalCount']) ? $materielLogList['totalCount'] : 0,
            'sort'       => [
                'attributes' => ['materiel_log_id desc'],
            ],
        ]);
        return $materielLogList;
    }
}
