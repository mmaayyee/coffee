<?php

namespace backend\models;

use backend\models\EquipVersion;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipVersionSearch represents the model behind the search form about `backend\models\EquipVersion`.
 */
class EquipVersionSearch extends EquipVersion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code', 'app_version', 'main_control_version', 'io_version', 'build_name','group_version'], 'safe'],
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
    public function search($params,$groupList = [])
    {
        $query = EquipVersion::find()
            ->orderBy('create_time DESC')
            ->alias('v')
            ->joinWith('equip e')
            ->leftJoin('building b', 'b.id = e.build_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        //搜索的关键字
        $groupKey = isset($params['EquipVersionSearch']['group_id']) ? $params['EquipVersionSearch']['group_id'] : '';
        if ($groupKey) {

            //匹配关键词
            $keyArray = array_filter($groupList, function ($val) use ($groupKey) {
                if (strpos($val, $groupKey) !== false) {
                    return true;
                }
            });
            if (array_keys($keyArray)) {
                $query->andFilterWhere(['in', 'group_id', array_keys($keyArray)]);
            } else {
                //没有匹配的项
                $query->andFilterWhere(['group_id' => '-1']);
            }
            $this->group_id = $params['EquipVersionSearch']['group_id'];
        }


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'create_time' => $this->create_time,
        ]);
        $query->andFilterWhere(['like', 'v.equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'app_version', $this->app_version])
            ->andFilterWhere(['like', 'main_control_version', $this->main_control_version])
            ->andFilterWhere(['like', 'b.name', $this->build_name])
            ->andFilterWhere(['like', 'group_version', $this->group_version])
            ->andFilterWhere(['like', 'io_version', $this->io_version]);

        return $dataProvider;
    }
}
