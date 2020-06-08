<?php

namespace backend\models;

use common\models\WxMember;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Api;
use backend\models\TemporaryAuthorization;
/**
 * WxMemberSearch represents the model behind the search form about `backend\models\WxMember`.
 */
class WxMemberSearch extends WxMember
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'name', 'position', 'mobile', 'email', 'weixinid', 'avatar_mediaid', 'extattr', 'department_id', 'org_id'], 'safe'],
            [['gender', 'create_time', 'is_del'], 'integer'],
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
        $query = WxMember::find()->orderBy('create_time');
        $this->orgArr = Api::getOrgIdNameArray();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere(['is_del' => 1]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'gender'        => $this->gender,
            'create_time'   => $this->create_time,
            'position'      => $this->position,
            'org_id'        => $this->org_id ? $this->org_id : "",
            'department_id' => $this->department_id,
        ]);

        $query->andFilterWhere(['like', 'userid', $this->userid])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'weixinid', $this->weixinid])
            ->andFilterWhere(['like', 'avatar_mediaid', $this->avatar_mediaid])
            ->andFilterWhere(['like', 'extattr', $this->extattr]);
        // echo $query->createCommand()->getRawSql();die;
        return $dataProvider;
    }

    /**
     * 查询单条数据
     * @param Array() $where
     * @return boolean
     */
    public static function getOne($where)
    {
        return self::find()->where($where)->one();
    }
}
