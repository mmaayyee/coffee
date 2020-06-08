<?php

namespace backend\models;

use backend\models\FormulaAdjustmentLog;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;

/**
 * GroupBeginTeamSearch represents the model behind the search form of `backend\models\GroupBeginTeam`.
 */
class FormulaAdjustmentLogSearch extends FormulaAdjustmentLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'update_time'], 'integer'],
            [['username', 'equipment_code'], 'string', 'max' => 50],
            [['formula_info'], 'string', 'max' => 255],
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

    public function search($params)
    {
        $this->load($params);
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $list           = FormulaAdjustmentLog::getFormulaAdjustmentLog('formula-adjustment-api/get-formula-adjustment-log.html', $params);
        $dataProvider   = [];
        if ($list) {
            foreach ($list['logModel'] as $key => $data) {
                $formulaAdjustmentLog = new FormulaAdjustmentLog();
                $formulaAdjustmentLog->load(['FormulaAdjustmentLog' => $data]);
                $dataProvider[$key] = $formulaAdjustmentLog;
            }
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20, //每页显示条数
            ],
            'totalCount' => isset($list['logCount']) && !empty($list['logCount']) ? $list['logCount'] : 0,
        ]);
        return $list;
    }

}
