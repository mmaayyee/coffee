<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;
use common\models\Api;
/**
 * This is the model class for table "light_belt_program".
 *
 * @property integer $id
 * @property string $program_name
 * ["buildName"]=> "楼宇测试"
     *   ["equipType"]=> "1" 设备类型
     *   ["branch"]=> "2"
     *   ["program_name"]=> "1" 方案名称
     *   ['agent']  => 代理商
     *   ["partner"]=> "13" // 合作商
     *   ["scenario_name"]=> "1" 场景名称
     *   ["strategy_name"]=> "1" 策略名称
     *   ["product_group_name"]=> "1" 饮品组名称
 */
class LightBeltProgram extends \yii\db\ActiveRecord
{
    public $program_name; // 方案名称
    public $buildName; // 楼宇名称
    public $buildType; // 楼宇类型
    public $equipType; // 设备类型
    public $equipCode; // 设备编号
    public $branch; // 分公司
    public $agent;  // 代理商
    public $partner; //合作商
    public $scenario_name; // 场景名称
    public $strategy_name; // 策略名称
    public $product_group_name; // 饮品组名称

    public $is_default;// 默认方案
    public $default_strategy_id;// 默认策略
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        // return 'light_belt_program';
        return "equipments";
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_name' => '方案名称',
            'is_default'   =>  '默认方案',
        ];
    }

    /**
     * 获取所有的灯带方案id=》name
     * @author  zmy
     * @version 2017-09-22
     * @param   $[isSelect] [是否请选择]
     * @return  [Array]     [灯带方案id=》name]
     */
    public static function getProgramNameList($isSelect= '')
    {
        return Json::decode( Api::getProgramNameList($isSelect));
    }

}
