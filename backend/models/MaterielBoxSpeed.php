<?php

namespace backend\models;

use common\models\Api;
use Yii;

/**
 * This is the model class for table "materiel_box_speed".
 *
 * @property string $materiel_box_speed_id
 * @property string $equip_type_id
 * @property string $material_type_id
 * @property string $speed
 */
class MaterielBoxSpeed extends \yii\db\ActiveRecord
{
    public $equip_type_id;
    public $material_type_id;
    public $speed;
    public $material_type_name;
    public $equipment_name;
    public $materiel_box_speed_id;
    public $isNewRecord;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'materiel_box_speed_id', 'material_type_id'], 'integer'],
            [['material_type_id', 'speed', 'equip_type_id'], 'required'],
            [['speed'], 'string', 'max' => 20],
            [['equipment_name', 'material_type_name'], 'safe'],
            ['equip_type_id', "requiredByASpecial", 'on' => 'create'],
        ];
    }

    /**
     *  自定义验证sale_name
     */
    public function requiredByASpecial($attribute, $params)
    {

        $params = array('MaterielBoxSpeed' => array('equip_type_id' => $this->equip_type_id, 'material_type_id' => $this->material_type_id));
        if (Api::verifyMaterielBoxSpeedCreate($params)) {
            $this->addError($attribute, "该类型已经添加过");
        }

    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'materiel_box_speed_id' => '料盒自增id',
            'equip_type_id'         => '设备类型',
            'material_type_id'      => '物料类型',
            'speed'                 => '每秒速度/克',
        ];
    }
    /**
     * 根据id获取详情
     */
    public static function findModel($id)
    {
        $info = Api::getMaterielBoxSpeedInfo($id);
        if ($info) {
            $model = new self();
            $model->load(['MaterielBoxSpeed' => $info]);
            return $model;
        }
    }
    public function saveMaterielBoxSpeedInfo($params)
    {
        return Api::saveMaterielBoxSpeedInfo($params);
    }
    public function createMaterielBoxSpeedInfo($params)
    {
        return Api::createMaterielBoxSpeedInfo($params);
    }
}
