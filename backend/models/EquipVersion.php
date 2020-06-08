<?php

namespace backend\models;

use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_version".
 *
 * @property integer $id
 * @property string $equip_code
 * @property string $app_version
 * @property string $main_control_version
 * @property string $io_version
 * @property integer $create_time
 *
 * @property Equipments $equipCode
 */
class EquipVersion extends \yii\db\ActiveRecord
{
    public $build_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'group_id'], 'integer'],
            [['equip_code'], 'string', 'max' => 64],
            [['app_version', 'group_version', 'main_control_version', 'io_version'], 'string', 'max' => 100],
            [['equip_code'], 'exist', 'skipOnError' => true, 'targetClass' => Equipments::className(), 'targetAttribute' => ['equip_code' => 'equip_code']],
            [['group_id'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => '设备版本信息id',
            'equip_code'           => '设备编号',
            'app_version'          => 'app版本号',
            'main_control_version' => '主控板版本号',
            'io_version'           => 'io板版本号',
            'create_time'          => '回传时间',
            'build_name'           => '楼宇名称',
            'group_version'        => '产品组版本号',
            'group_id'             => '产品组',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    /**
     * 保存设备版本信息
     * @author  zgw
     * @version 2017-04-10
     * @param   [type]     $data [description]
     */
    public static function addData($data)
    {
        $model                       = new EquipVersion();
        $model->equip_code           = $data['equip_code'];
        $model->app_version          = !isset($data['app_version']) ? '' : $data['app_version'];
        $model->main_control_version = !isset($data['main_control_version']) ? '' : $data['main_control_version'];
        $model->io_version           = !isset($data['io_version']) ? '' : $data['io_version'];
        $model->create_time          = time();
        $model->group_id             = !isset($data['groupId']) ? 0 : $data['groupId'];
        $model->group_version        = !isset($data['groupVersion']) ? '' : (string) $data['groupVersion'];
        return $model->save();
    }

    /**
     * 根据设备编号获取设备APP的版本号
     * @author zhenggangwei
     * @date   2020-04-01
     * @param  string     $equipCode 设备编号
     * @return string
     */
    public static function getEquipVersionByEquipCode($equipCode)
    {
        return self::find()->select('app_version')->where(['equip_code' => $equipCode])->orderBy('create_time desc')->scalar();
    }
}
