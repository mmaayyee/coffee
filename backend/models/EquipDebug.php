<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_debug".
 *
 * @property string $Id
 * @property string $debug_item
 * @property integer $result
 *
 * @property EquipAcceptanceDebugAssoc[] $equipAcceptanceDebugAssocs
 * @property EquipDeliveryDebugAssoc[] $equipDeliveryDebugAssocs
 */
class EquipDebug extends \yii\db\ActiveRecord
{
    /** 定义是否删除常量 */
    const DEL_NOT = 1;
    const DEL_YES = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_debug';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['debug_item', 'equip_type_id'], 'required'],
            [['debug_item'], 'string', 'max' => 255],
            [['debug_item'], 'equipDebugNameUniqueVeriry'],
            [['equip_type_id', 'is_del'], 'integer']
        ];
    }
    /**
     * 验证未删除的灯箱调试项唯一性
     * @author  zgw
     * @version 2016-09-26
     * @param   [type]     $attribute [description]
     * @param   [type]     $params    [description]
     * @return  [type]                [description]
     */
    public function equipDebugNameUniqueVeriry($attribute) {
        $id = Yii::$app->request->get('id');
        if ($id) {
            $where = ['and', ['debug_item' => $this->debug_item], ['is_del' => self::DEL_NOT], ['equip_type_id' => $this->equip_type_id], ['!=', 'id', $id]];
        } else {
            $where = ['debug_item' => $this->debug_item, 'equip_type_id' => $this->equip_type_id, 'is_del' => self::DEL_NOT];
        }
        $isExite = self::find()->where($where)->one();
        if ($isExite)
            $this->addError($attribute, "灯箱调试项名称的值".$this->debug_item."已经被占用了。");
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'debug_item' => '设备调试项',
            'equip_type_id' => '设备类型'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipType() {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipAcceptanceDebugAssocs()
    {
        return $this->hasMany(EquipAcceptanceDebugAssoc::className(), ['equip_debug_id' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipDeliveryDebugAssocs()
    {
        return $this->hasMany(EquipDeliveryDebugAssoc::className(), ['equip_debug_id' => 'Id']);
    }
    /**
     * 返回设备调试项列表(搜索用)
     * @author  zgw
     * @version 2016-09-26
     * @return  [type]     [description]
     */
    public static function getEquipDebugNameArr() {
        return \yii\helpers\ArrayHelper::getColumn(self::find()->where(['is_del'=>self::DEL_NOT])->all(), 'debug_item');
    }

}
