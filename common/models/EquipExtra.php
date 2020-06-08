<?php

namespace common\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "equip_extra".
 *
 * @property integer $id
 * @property string $extra_name
 * @property integer $is_del
 */
class EquipExtra extends \yii\db\ActiveRecord
{
    const NO_DELETE = 1;
    const DELETED   = 2;

    public static $status = ['' => '请选择', self::NO_DELETE => '正常', self::DELETED => '禁用'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_del'], 'integer'],
            [['extra_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'extra_name' => '附件名称',
            'is_del'     => '状态',
        ];
    }

    /**
     * 获取设备附件名
     * @param bool|true $isDel
     * @return array
     */
    public static function getEquipExtra($delete = true)
    {
        if ($delete) {
            $list = self::find()->select('id,extra_name')->where(['is_del' => self::NO_DELETE])->asArray()->all();
        } else {
            $list = self::find()->select('id,extra_name')->asArray()->all();
        }

        return $list ? Tools::map($list, 'id', 'extra_name', null, 0) : [];
    }

    /**
     * 获取选项设备附件名称
     * @return array
     */
    public static function getEquipExtraSelect()
    {
        $selectOption = ['' => '请选择'];

        foreach (self::getEquipExtra(false) as $key => $item) {
            $selectOption[$key] = $item;
        }
        return $selectOption;
    }

    /**
     * 根据ID获取附件名称
     */
    public static function getExtraNameByID($extraId = '')
    {
        $id   = explode(',', $extraId);
        $list = self::find()->select('extra_name')->where(['id' => $id])->asArray()->all();
        $str  = '';
        foreach ($list as $k => $v) {
            $str .= $v['extra_name'] . '<br/>';
        }
        return $str;
    }
}
