<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;
use common\helpers\Tools;

/**
 * This is the model class for table "service_question".
 *
 * @property int $id
 * @property string $question 问题
 * @property string $answer 答案
 * @property int $static 0为删除不显示的为题,1为下线,2是上线
 * @property int $create_time 创建时间
 * @property int $s_c_id 问题分类的ID
 */
class ServiceQuestion extends \yii\db\ActiveRecord
{
    public $question_key;
    public $id;
    public $question;
    public $answer;
    public $static;
    public $s_c_id;
    public $create_time;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question','answer','static', 'create_time', 's_c_id'], 'required'],
            [['static', 'create_time', 's_c_id'], 'integer'],
            [['question'], 'string', 'max' => 80],
            [['answer'], 'string', 'max' => 200],
            [['id'],'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => '问题',
            'answer' => '答案',
            'static' => '状态',
            'create_time' => '创建时间',
            's_c_id' => '问题类别',
            'question_key' => '关键词'
        ];
    }

    /**
     *  获取修改的问题
     */
    public static function getQuestionInfo($questionID = 0)
    {
        $questionModel = self::getBase("service-api/get-question-details","&question_id=".$questionID);
        return $questionModel ? Json::decode($questionModel) : [];
    }
    /**
     * 获取问题下的分类
     */
    public function getQuestionCategoryQuestionID($question_id = 0){
        return self::getBase('service-api/question-category',"&question_id=".$question_id);
    }
    /**
     *  获取全部问题
     * @return array
     */
    public static function getQuestionList($params)
    {
        $page = isset($params['page']) ? $params['page'] : 0;
        $list = self::postBase("service-api/question-list",$params, '&page=' . $page);
        return !$list ? [] : Json::decode($list);
    }
    /**
     *  获取问题详情
     * @param int $questionid
     * @return array|mixed
     */
    public static function getQuesitonByID($questionid = 0)
    {
        $question = Json::decode(self::getBase('service-api/question-info',"&question_id=".$questionid));
        return !$question ? [] : $question;
    }
    /**
     * 删除指定问题
     * @param  [type] $questionid [description]
     * @return [type]             [description]
     */
    public static function getDeleteQuestionID($questionId = [])
    {
        return self::postBase('service-api/delete-question',$questionId);
    }
    public static function postBase($action,$data = [],$params = '')
    {
       //echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;exit;
         return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);

    }
    public function getStatus($static = ''){
        $statusList = ['1' => '上线', '2' => '下线'];
        return $static ? $statusList[$static] : $statusList;
    }
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }
     public  function getKeys($questionid)
    {
        $questionInfo =  ServiceQuestion::getQuesitonByID($questionid);
        $question =$questionInfo['data'];
        $keys = isset($questionInfo['data']['key']) ?$questionInfo['data']['key'] : [];
        $key = implode(',',$keys);
        return $key;
    }
}
