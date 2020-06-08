<?php

namespace backend\models;

use backend\models\MaterielBoxSpeed;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaterielBoxSpeedSearch represents the model behind the search form about `app\models\MaterielBoxSpeed`.
 */
class MaterielBoxSpeedSearch extends MaterielBoxSpeed
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['materiel_box_speed_id', 'equip_type_id', 'material_type_id'], 'integer'],
            [['speed', 'equipment_name', 'material_type_name'], 'safe'],
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
        $params['page']       = isset($params['page']) ? $params['page'] : 0;
        $MaterielBoxSpeedList = Api::getMaterielBoxSpeedList($params);
        $dataProvider         = [];
        if ($MaterielBoxSpeedList) {
            foreach ($MaterielBoxSpeedList['materielBoxSpeedList'] as $key => $data) {
                $MaterielBoxSpeed = new MaterielBoxSpeed();
                $MaterielBoxSpeed->load(['MaterielBoxSpeed' => $data]);
                $dataProvider[$key] = $MaterielBoxSpeed;
            }
        }
        $MaterielBoxSpeedList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($MaterielBoxSpeedList['totalCount']) && !empty($MaterielBoxSpeedList['totalCount']) ? $MaterielBoxSpeedList['totalCount'] : 0,
            'sort'       => [
                'attributes' => ['clear_equip_id desc'],
            ],
        ]);
        return $MaterielBoxSpeedList;
    }
}
