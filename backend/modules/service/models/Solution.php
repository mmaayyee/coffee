<?php

namespace backend\modules\service\models;

use Yii;

/**
 * 解决方案表单验证
 * @author wlw
 *
 */
class Solution extends \yii\db\ActiveRecord
{
    public $solution_id; //协商解决方案id
    public $solution_name; //协商解决方案名称
    public $is_show; //是否可用。1可用，0不可用
    public $update_time; //更新时间

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_show', 'update_time', 'solution_id'], 'integer'],
            [['solution_name'], 'required'],
            [['solution_name'], 'string', 'max' => 255],
        ];
    }

    public function attributes()
    {
        return [
            'is_show'       => '是否在线',
            'update_time'   => '更新时间',
            'solution_name' => '解决方案',
        ];
    }
}
