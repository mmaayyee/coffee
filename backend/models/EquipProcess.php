<?php

namespace backend\models;
use common\models\EquipProductGroupApi;
use Yii;

/**
 * This is the model class for table "equip_process".
 *
 * @property integer $id
 * @property string $process_name
 * @property string $process_english_name
 * @property string $process_color
 */
class EquipProcess extends \yii\db\ActiveRecord
{
    public $id;                     // 工序ID
    public $process_name;           // 工序名称
    public $process_english_name;   // 工序英文名称
    public $process_color;          // 色块
    public $isNewRecord;            // 添加、修改、

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'safe'],
            [['process_name', 'process_english_name', 'process_color'], 'required'],
            ['process_name', 'string', 'max' => 12],
            ['process_color', 'string', 'max' => 50],
            ['process_english_name', 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'process_name'         => '工序名称',
            'process_english_name' => '工序英文名称',
            'process_color'        => '色块',
        ];
    }

    /**
     * 根据单品ID，查询单品数据
     * @author  zmy
     * @version 2017-09-06
     * @param   [string]     $id [单品ID]
     * @return  [obj]            [model]
     */
    public static function getEquipProcessById($id)
    {
        $model       = new self();
        $processList = EquipProductGroupApi::getEquipProcessById($id);
        $model->load(['EquipProcess' => $processList]);
        return $model;
    }

}
