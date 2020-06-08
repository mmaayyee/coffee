<?php

namespace backend\models;

use Yii;
use common\models\Api;
/**
 * This is the model class for table "light_belt_strategy".
 *
 * @property integer $id
 * @property string $strategy_name
 * @property string $total_length_time
 * @property integer $light_belt_type
 * @property integer $light_status
 * @property double $flicker_frequency
 * @property string $light_belt_color
 */
class LightBeltStrategy extends \yii\db\ActiveRecord
{
    public $strategy_name;      // 策略名称
    public $total_length_time;  // 灯带总时长
    public $light_belt_type;    // 灯带显示类型
    public $light_status;       // 亮灯状态
    public $flicker_frequency;
    public $light_belt_color;

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
    // public function rules()
    // {
    //     return [
    //         [['total_length_time', 'light_belt_type', 'light_status'], 'integer'],
    //         [['flicker_frequency'], 'number'],
    //         [['strategy_name'], 'string', 'max' => 255],
    //         [['light_belt_color'], 'string', 'max' => 50],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'strategy_name' => '策略名称',
            'total_length_time' => '灯带总时长',
            'light_belt_type' => '灯带显示类型',
            'light_status' => '是否亮灯',
            'flicker_frequency' => '闪烁频率',
            'light_belt_color'  => '灯带颜色',
        ];
    }

    /**
     * 获取控制类型
     * @var [type]
     */
    public static $lightBeltTypeArr = [
        ''  =>  '请选择', 
        0   =>  "整体控制",
        1   =>  "灯带控制",
    ];

    /**
     *  选择灯带亮灯类型
     * @var [type]
     */
    public static $lightStatusArr   = [
        ''  =>  '请选择',
        0   =>  '不亮',
        1   =>  '亮（不闪烁）',
        2   =>  '亮（闪烁）',
    ];

    /**
     * 获取策略数组 ，id=>name
     * @author  zmy
     * @version 2017-06-29
     * @return  [type]     [description]
     */
    public static function getStrategyNameList()
    {
        $strategyNameList = json_decode(Api::getStrategyNameList(), true);
        $strategyNameList['']='请选择';
        ksort($strategyNameList);
        return $strategyNameList;
    }

    
    /**
     * 组合12条灯带
     * @author  zmy
     * @version 2017-07-28
     * @return  [type]     [description]
     */
    public static function getLightBeltList()
    {
        return array(
            '1号灯带', '2号灯带', '3号灯带', '4号灯带', '5号灯带', '6号灯带', '7号灯带', '8号灯带', '9号灯带', '10号灯带', '11号灯带', '12号灯带',
        );
    }


}
