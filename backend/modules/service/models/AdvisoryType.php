<?php

namespace backend\modules\service\models;

use Yii;

/**
 * 处理客服咨询类型表单验证
 * @author wlw
 * @date 2018-09-13
 */

class AdvisoryType extends \yii\base\Model
{
    public $advisory_type_id; //咨询类型ID
    public $advisory_type_name; //类型名称
    public $is_show; //是否展示。1:显示 0:隐藏
    public $update_time; //更新时间

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advisory_type_name'], 'required'],
            [['is_show', 'update_time', 'advisory_type_id'], 'integer'],
            [['advisory_type_name'], 'string', 'max' => 64],
        ];
    }

    public function attributeLabels()
    {
        return [
            'advisory_type_name' => '名称',
            'is_show'            => '状态',
        ];
    }
}
