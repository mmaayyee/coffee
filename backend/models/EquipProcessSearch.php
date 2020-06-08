<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipProcess;
use common\models\EquipProductGroupApi;
use common\models\ArrayDataProviderSelf;


/**
 * EquipProcessSearch represents the model behind the search form about `backend\models\EquipProcess`.
 */
class EquipProcessSearch extends EquipProcess
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['process_name', 'process_english_name', 'process_color'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $proList     = EquipProductGroupApi::getEquipProcessList($params);
        
        $dataProvider = [];
        foreach ($proList['equipProcessList'] as $key => $data) {
            $equipProcess = new EquipProcess();
            $equipProcess->load(['EquipProcess' => $data]);
            $dataProvider[$data['id']] = $equipProcess;
        }
        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$proList['total'] ? 0 : $proList['total'],
            'sort'       => [
                'attributes' => ['id DESC'],
            ],
        ]);
        return $coffeeProList;
    }
}
