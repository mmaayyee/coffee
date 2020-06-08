<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role', 'status', 'created_at', 'updated_at', 'sex', 'is_master'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'openid', 'nickname', 'realname', 'province', 'mobile'], 'safe'],
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
        $query = User::find();
         $query->orderBy("id desc");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'role' => $this->role,
            'status' => $this->status,
            'mobile'  => $this->mobile,
            'province'  => $this->province,
            'realname'  => $this->realname,
            'is_master' => $this->is_master,
        ]);
        //起始日期
        if(!empty($params["UserSearch"]["createFrom"])){
            $from = strtotime($params["UserSearch"]["createFrom"]);
            $this->createFrom = $params["UserSearch"]["createFrom"];
            $query->andFilterWhere(['>=', 'created_at', $from]);
        }
        //截止日期
        if(!empty($params["UserSearch"]["createTo"])){
            $to = strtotime($params["UserSearch"]["createTo"]) + 3600*24;
            $this->createTo = $params["UserSearch"]["createTo"];
            $query->andFilterWhere(['<=', 'created_at', $to]);
        }
       
        return $dataProvider;
    }
}
