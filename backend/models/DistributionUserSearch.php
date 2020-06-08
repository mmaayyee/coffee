<?php

namespace backend\models;

use backend\models\DistributionUser;
use backend\models\Manager;
use common\models\Building;
use common\models\WxMember;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DistributionUserSearch represents the model behind the search form about `backend\models\DistributionUser`.
 */
class DistributionUserSearch extends DistributionUser
{
    public $build_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'leader_id'], 'safe'],
            [['user_status', 'is_leader', 'build_id', 'orgId'], 'integer'],
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
        $query  = DistributionUser::find();
        $org_id = Manager::getManagerBranchID();

        if ($org_id > 1) {
            $query->joinWith('user')->andFilterWhere(['wx_member.org_id' => $org_id, 'wx_member.is_del' => WxMember::DEL_NO]);
        } else {
            if (isset($params['DistributionUserSearch']['orgId']) && $params['DistributionUserSearch']['orgId']) {
                $query->joinWith('user')->andFilterWhere(['wx_member.org_id' => $params['DistributionUserSearch']['orgId'], 'wx_member.is_del' => WxMember::DEL_NO]);
                $this->orgId = $params['DistributionUserSearch']['orgId'];
            } else {
                $query->joinWith('user')->andFilterWhere(['wx_member.is_del' => WxMember::DEL_NO]);
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->build_id) {
            $buildingModel = Building::getBuildDetail(['id' => $this->build_id]);
            $this->userid  = $buildingModel->distribution_userid ? $buildingModel->distribution_userid : '';
        }

        $query->andFilterWhere([
            'user_status' => $this->user_status,
            'is_leader'   => $this->is_leader,
        ]);
        $query->andFilterWhere([
            'distribution_user.userid' => $this->userid,
        ]);

        $query->andFilterWhere([
            'leader_id' => $this->leader_id,
        ]);
        return $dataProvider;
    }
}
