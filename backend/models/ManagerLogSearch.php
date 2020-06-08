<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ManagerLog;

/**
 * ManagerLogSearch represents the model behind the search form about `backend\models\ManagerLog`.
 */
class ManagerLogSearch extends ManagerLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'manager_id', 'operate_type', 'created_at'], 'integer'],
            [['operate_content', 'module_name' ,'realname' ,'createdTo', 'createdFrom'], 'safe'],
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
        $query = ManagerLog::find()->leftJoin('manager',"manager_id=manager.id");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        //增加默认排序
        $dataProvider->sort->attributes['manager_log.created_at'] = [
             'asc' => ['manager_log.created_at' => SORT_ASC],
             'desc' => ['manager_log.created_at' => SORT_DESC],
        ];
        $dataProvider->sort->defaultOrder = ['manager_log.created_at' => SORT_DESC];          
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
          
        
        $query->andFilterWhere([
            'log_id' => $this->log_id,
            'manager_id' => $this->manager_id,
            'module_name' => $this->module_name,
            'operate_type' => $this->operate_type,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'operate_content', $this->operate_content]);
        
        //起始日期
        if(!empty($params["ManagerLogSearch"]["createdFrom"])){
            $from = strtotime($params["ManagerLogSearch"]["createdFrom"]);
            $this->createdFrom = $params["ManagerLogSearch"]["createdFrom"];
            $query->andFilterWhere(['>=', 'manager_log.created_at', $from]);
        }
        //截止日期
        if(!empty($params["ManagerLogSearch"]["createdTo"])){
            $to = strtotime($params["ManagerLogSearch"]["createdTo"]) + 3600*24;
            $this->createdTo = $params["ManagerLogSearch"]["createdTo"];
            $query->andFilterWhere(['<=', 'manager_log.created_at', $to]);
        }         
        
        //真名
        if(!empty($params["ManagerLogSearch"]["realname"])){
            $this->realname = trim($params["ManagerLogSearch"]["realname"]);
            $query->andFilterWhere(['manager.realname'=> $this->realname]);
        }        

        return $dataProvider;
    }
}
