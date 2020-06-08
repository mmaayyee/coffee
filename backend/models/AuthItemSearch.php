<?php

namespace backend\models;

use backend\models\AuthItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * AuthItemSearch represents the model behind the search form about `app\models\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'rule_name', 'data', 'role'], 'safe'],
            [['type', 'created_at', 'updated_at'], 'integer'],
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
        $query = AuthItem::find()
            ->select('name,description');
        $query->andFilterWhere([
            'type' => 1,
        ]);
        if (isset($params['AuthItemSearch']['role'])) {
            $query->andFilterWhere(['name' => $params['AuthItemSearch']['role']]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->asArray();
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    /**
     * 获取角色名称列表
     * @author zhenggangwei
     * @date   2020-01-10
     * @return array
     */
    public static function getRoleNameList()
    {
        $roleNameList = AuthItem::find()->select('name')->where(['type' => 1])->asArray()->all();
        return ArrayHelper::map($roleNameList, 'name', 'name');
    }
}
