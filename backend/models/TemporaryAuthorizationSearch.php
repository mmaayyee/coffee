<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 16:20
 */
namespace backend\models;

use common\models\Api;
use yii;
use yii\data\ActiveDataProvider;

class TemporaryAuthorizationSearch extends TemporaryAuthorization
{
    public function rules()
    {
        return [
            [['build_name', 'wx_member_name'], 'string'],
            ['state', 'number'],
            [['orgId', 'orgType'], 'safe'],
        ];
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
        $query        = TemporaryAuthorization::find()->alias('ta')->orderBy('application_time DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $orgIdArr = [];
        if ($this->orgId != '') {
            $query->leftJoin('building b', 'b.name = ta.build_name');
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->orgId]);
        }
        if ($this->orgId != '' && $this->orgType != '') {
            $orgIdArr = $this->orgType == 1 ? Api::getOrgIdArray(['parent_path' => $this->orgId, 'org_id_no' => $this->orgId]) : [$this->orgId];
        }
        $query->andFilterWhere(['b.org_id' => $orgIdArr]);
        $query->andFilterWhere(['like', 'build_name', $this->build_name]);
        if ($this->state == self::FAILED) {
            $applicationTime = time() - yii::$app->params['bluetoothLockValidTime'];
            $query->andFilterWhere(['<', 'application_time', $applicationTime]);
            $query->andFilterWhere(['state' => self::TOEXAMINE]);
            $query->orFilterWhere(['state' => $this->state]);

        } elseif ($this->state === (string) self::TOEXAMINE) {
            $applicationTime = time() - yii::$app->params['bluetoothLockValidTime'];
            $query->andFilterWhere(['>', 'application_time', $applicationTime]);
            $query->andFilterWhere(['state' => self::TOEXAMINE]);
        } elseif ($this->state) {
            $query->andFilterWhere(['state' => $this->state]);
        }
        $query->andFilterWhere(['wx_member_name' => $this->wx_member_name]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function exportSearch($params)
    {
        $this->load($params);
        $query = TemporaryAuthorization::find()->alias('ta')->orderBy('application_time DESC');
        if (!$this->validate()) {
            return [];
        }
        $orgIdArr = [];
        if ($this->orgId != '') {
            $query->leftJoin('building b', 'b.name = ta.build_name');
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->orgId]);
        }
        if ($this->orgId != '' && $this->orgType != '') {
            $orgIdArr = $this->orgType == 1 ? Api::getOrgIdArray(['parent_path' => $this->orgId, 'org_id_no' => $this->orgId]) : [$this->orgId];
        }
        $query->andFilterWhere(['b.org_id' => $orgIdArr]);
        $query->andFilterWhere(['like', 'build_name', $this->build_name]);
        if ($this->state == self::FAILED) {
            $applicationTime = time() - yii::$app->params['bluetoothLockValidTime'];
            $query->andFilterWhere(['<', 'application_time', $applicationTime]);
            $query->andFilterWhere(['state' => self::TOEXAMINE]);
            $query->orFilterWhere(['state' => $this->state]);

        } elseif ($this->state === (string) self::TOEXAMINE) {
            $applicationTime = time() - yii::$app->params['bluetoothLockValidTime'];
            $query->andFilterWhere(['>', 'application_time', $applicationTime]);
            $query->andFilterWhere(['state' => self::TOEXAMINE]);
        } elseif ($this->state) {
            $query->andFilterWhere(['state' => $this->state]);
        }
        return $query->andFilterWhere(['wx_member_name' => $this->wx_member_name])->asArray()->all();
    }

    /**
     * 和申请临时开门表(temporary_authorization)进行关联查询
     * @param Array()  $where
     * @return Array()  二维数组
     */
    public static function getJoinAll($where)
    {
        $resule = self::find()
            ->alias('ta')
            ->leftJoin("wx_member as wx", 'wx.userid = ta.userid')
            ->andWhere($where)
            ->asArray()
            ->one();
        return $resule ? $resule : false;
    }
}
