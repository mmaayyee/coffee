<?php

namespace backend\modules\service\models;

use Yii;

/**
 * 处理客服问题类型表单验证
 * @author wlw
 * @date 2018-09-13
 */

class QuestionType extends \yii\base\Model
{
    public $question_type_id; //问题类型ID
    public $question_type_name; //类型名称
    public $advisory_type_id; //咨询类型id
    public $is_show; //是否展示。1:显示 0:隐藏
    public $update_time; //更新时间

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advisory_type_id', 'question_type_name'], 'required'],
            [['advisory_type_id', 'is_show', 'update_time', 'question_type_id'], 'integer'],
            [['question_type_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'question_type_name' => '名称',
            'advisory_type_id'   => 'ID',
            'question_type_id'   => '咨询类型',
            'update_time'        => '更新时间',
            'is_show'            => '状态',
        ];
    }
}
