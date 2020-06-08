<?php

namespace backend\models;

use backend\models\Organization;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrganizationSearch represents the model behind the search form about `backend\models\Organization`.
 */
class OrganizationSearch extends Organization
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id', 'parent_id', 'organization_type'], 'integer'],
            [['org_name', 'org_number', 'org_city', 'parent_path', 'org_pass', 'is_replace_maintain'], 'safe'],
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
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $this->load($params);
        $orgList      = Api::getSearchOrgErp($params);
        $dataProvider = [];
        if ($orgList) {
            foreach ($orgList['orgList'] as $key => $data) {
                $organization = new Organization();
                $organization->load(['Organization' => $data]);
                $dataProvider[$key] = $organization;
            }
        }
        $orgList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($orgList['totalCount']) && !empty($orgList['totalCount']) ? $orgList['totalCount'] : 0,
            'sort'       => [
                'attributes' => ['org_id desc'],
            ],
        ]);
        return $orgList;
    }
}
