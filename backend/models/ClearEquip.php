<?php

namespace backend\models;

use Yii;
use common\models\Api;

/**
 * This is the model class for table "clear_equip".
 *
 * @property integer $clear_equip_id
 * @property string $equip_type_id
 * @property string $code
 * @property string $remark
 * @property double $consum_total
 */
class ClearEquip extends \yii\db\ActiveRecord
{       
    public $clear_code_name;
    public $equipment_name;
    public $equip_type_id;
    public $code;
    public $remark;
    public $consum_total;
    public $clear_equip_id;
    public $isNewRecord;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'code', 'remark', 'consum_total'], 'required'],
            [['equip_type_id'], 'integer'],
            [['consum_total'], 'number'],
            [['code'], 'string', 'max' => 20],
            [['remark'], 'string', 'max' => 255],
            [['clear_code_name','equipment_name','clear_equip_id'],'safe'],
            ['equip_type_id', "requiredByASpecial",'on' => 'create'],
        ];
    }

    /**
     *  自定义验证sale_name
     */
    public function requiredByASpecial($attribute, $params)
    {   
        
        $params = array('ClearEquip' => array('equip_type_id' => $this->equip_type_id,'code' => $this->code));
        if(Api::verifyClearEquipCreate($params)){
            $this->addError($attribute, "该类型已经添加过");
        }
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'clear_equip_id' => '主键id',
            'equip_type_id' => '设备类型',
            'code' => '清洗类型',
            'remark' => '备注',
            'consum_total' => '水消耗总量/毫升',
        ];
    }


    /**
     * 根据id获取详情
     */
    public static function findModel($id)
    {   
        $info = Api::getClearEquipInfo($id);
        if($info){
            $model = new self();
            $model->load(['ClearEquip' => $info]);
            return $model;
        }
    }

    public function saveClearEquipInfo($params){
        return Api::saveClearEquipInfo($params);
    }

    public function createClearEquipInfo($params){
        return Api::createClearEquipInfo($params);
    }
}
