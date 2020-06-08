<?php

namespace backend\models;

use Yii;
use common\models\Api;
/**
 * This is the model class for table "light_belt_scenario".
 *
 * @property integer $id
 * @property string $scenario_name
 * @property string $equip_scenario_id
 * @property string $product_group_name
 * @property string $strategy_name
 * @property string $start_time
 * @property string $end_time
 */
class LightBeltScenario extends \yii\db\ActiveRecord
{
    // 添加
    public $product_group_id;
    public $strategy_id;
    // 搜索
    public $scenario_name;
    public $equip_scenario_name;
    public $product_group_name;
    public $strategy_name;
    public $start_time;
    public $end_time;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_group_id', 'strategy_id'], 'required','on' => ['create', 'update'] ],
            [['scenario_name', 'equip_scenario_name', 'strategy_name', 'start_time', 'end_time'], 'required', 'on' => ['create', 'update'] ],
            // ['end_time', 'compare', 'compareAttribute' => 'start_time','operator' => '>', 'on' => ['create', 'update'] ],
            [['start_time', "end_time"], "integer", 'min'=>0, 'max'=> 24, 'message'=>"请在0-24范围内选择", 'on' => ['create', 'update'] ],
            [['scenario_name', 'equip_scenario_name', 'product_group_name', 'strategy_name'], 'string', 'max' => 50, 'on' => ['create', 'update'] ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'scenario_name'     => '场景名称',
            'equip_scenario_name' => '流程场景',
            'product_group_id'  => '饮品组名称',
            'strategy_id'       => '策略名称',
            'start_time'        => '开始时间',
            'end_time'          => '结束时间',
        ];
    }

    public static $equipScenarioNameArr = [
        ''  =>  '请选择',
        'startMake'=>   '开始制作',
        'makeOver' =>   '结束制作',
        'standby'  =>   '待机',
    ];


    /**
     * 修改时，提前赋值到model字段中
     * @author  zmy
     * @version 2017-06-29
     * @param   [type]     $scenarioList [根据ID，接口返回的场景数据]
     * @return  [type]                   [description]
     */
    public static function getUpScenarioModel($scenarioList)
    {
        $model = new LightBeltScenario();
        $model->scenario_name       =   $scenarioList['scenario_name'];
        $model->equip_scenario_name =   $scenarioList['equip_scenario_name'];
        $model->product_group_name  =   $scenarioList['product_group_name'];
        $model->product_group_id    =   $scenarioList['product_group_id'];
        $model->strategy_name       =   $scenarioList['strategy_name'];
        $model->strategy_id         =   $scenarioList['strategy_id'];
        $model->start_time          =   $scenarioList['start_time'];
        $model->end_time            =   $scenarioList['end_time'];
        return $model;
    }

}
