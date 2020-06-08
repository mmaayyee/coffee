<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "materiel_log".
 *
 * @property integer $materiel_log_id
 * @property integer $operaction_type
 * @property string $activity_type
 * @property string $desc
 * @property string $create_at
 */
class MaterielLog extends \yii\db\ActiveRecord
{
    public $operaction_type;
    public $activity_type;
    public $create_at;
    public $desc;
    public $materiel_log_id;
    public $startTime;
    public $endTime;
    public $build_name;
    public $equipment_code;
    public $product_id;
    public $consume_id;

    // 测试
    const OPERACTION_TYPE_TEST = 1;
    // 正式
    const OPERACTION_TYPE_FORMAL = 0;

    /**
     * 合作商类型
     * @var array
     */
    public static $operactionTypeList = array(
        ''                           => '请选择',
        self::OPERACTION_TYPE_TEST   => '工厂模式',
        self::OPERACTION_TYPE_FORMAL => '售卖模式',
    );

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materiel_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operaction_type', 'create_at', 'product_id', 'consume_id'], 'integer'],
            [['desc', 'create_at'], 'required'],
            [['desc', 'activity_type'], 'string'],
            [['startTime', 'endTime', 'equipment_code', 'build_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'materiel_log_id' => 'Materiel Log ID',
            'operaction_type' => '操作类型',
            'activity_type'   => '动作名称',
            'desc'            => 'Desc',
            'create_at'       => 'Create At',
            'startTime'       => '开始时间',
            'endTime'         => '结束时间',
            'build_name'      => '楼宇名称',
            'equipment_code'  => '设备编号',
            'product_id'      => '产品名称',
            'consume_id'      => '消费记录ID',
        ];
    }

    public static function descJsonDecode($desc)
    {
        if (!empty($desc)) {
            $desc = json_decode($desc, 'true');
            $str  = '';
            foreach ($desc as $key => $value) {
                if ($value['materielName'] == '水') {
                    $unin = '毫升';
                } else if ($value['materielName'] == '杯子') {
                    $unin = '个';
                } else {
                    $unin = '克';
                }
                $str .= $value['materielName'] . ':' . round($value['weight'], 2) . $unin . "<br>";
            }
            return $str;
        }
    }
}
