<?php

namespace backend\models;

use Yii;
use common\models\ActivityApi;
/**
 * This is the model class for table "lottery_winning_hint".
 *
 * @property string $hint_id
 * @property string $hint_text
 * @property string $hint_photo
 * @property string $second_button_photo
 * @property string $thank_participate_photo
 * @property integer $hint_type
 * @property string $activity_type_id
 */
class LotteryWinningHint extends \yii\db\ActiveRecord
{
    public $hint_id;   // 提示ID
    public $hint_success_text;  // 成功文本
    public $hint_error_text;    // 失败文本
    public $hint_success_photo; // 成功图片
    public $hint_error_photo;   // 失败图片
    public $activity_type_id;   // 活动类型
    public $second_button_photo;// 二次按钮
    public $thank_participate_photo; // 谢谢参与
    public $files;              // 图片

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hint_success_text', 'hint_error_text'], 'required'],
            [['activity_type_id'], 'required', 'on' => 'create'],
            [['hint_id', 'activity_type_id'], 'integer'],
            [[ 'hint_success_text', 'hint_error_text', 'hint_success_photo', 'hint_error_photo', 'second_button_photo', 'thank_participate_photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hint_id'           => 'Hint ID',
            'activity_type_id'  => '活动类型',
            'hint_success_text' => '成功文本提示',
            'hint_error_text'   =>  '失败文本提示',

        ];
    }

    /**
     * 获取活动类型数组
     * @author  zmy
     * @version 2017-12-09
     * @param   integer    $level        [查询活动级别]
     * @param   integer    $type         [是否加请选择]
     * @param   integer    $isPromptMode [是否过滤已有的活动]
     * @return  [type]                   [description]
     */
    public static function getActivityTypeList($level=0, $type=2, $isPromptMode =0)
    {
        $activityTypeList = ActivityApi::getActivityTypeList(2, 1, 1);
        if(!$activityTypeList){
            $activityTypeList = [''=>'请选择'];
        }
        return $activityTypeList;
    }
}
