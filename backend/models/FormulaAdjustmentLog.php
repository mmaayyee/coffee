<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "formula_adjustment_log".
 *
 * @property string $id 配方日志ID
 * @property string $username
 * @property int $update_time 更新时间
 * @property string $formula_info 设备配方信息
 * @property string $equipment_code 设备编号
 */
class FormulaAdjustmentLog extends \yii\db\ActiveRecord
{

    public $id;
    public $username;
    public $update_time;
    public $begin_date;
    public $end_date;
    public $formula_info;
    public $equipment_code;

    public static function tableName()
    {
        return 'formula_adjustment_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['update_time', 'id'], 'integer'],
            [['username', 'equipment_code'], 'string', 'max' => 50],
            [['formula_info'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'username'       => 'Username',
            'update_time'    => 'Update Time',
            'formula_info'   => 'Formula Info',
            'equipment_code' => 'Equipment Code',
            'begin_date'     => '开始时间',
            'end_date'       => '结束时间',
        ];
    }

    /**
     * 获取设备配方调整日志
     * @author  wangxiwen
     * @version 2018-07-27
     * @return  [type]     [description]
     */
    public static function getFormulaAdjustmentLog($action, $params = [])
    {
        $formuladLogList = Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, Json::encode($params));
        return empty($formuladLogList) ? [] : Json::decode($formuladLogList);
    }
}
