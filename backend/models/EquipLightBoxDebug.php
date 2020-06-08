<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_light_box_debug".
 *
 * @property string $Id
 * @property string $debug_item
 * @property integer $result
 *
 * @property EquipAcceptanceLightBoxAssoc[] $equipAcceptanceLightBoxAssocs
 * @property EquipDeliveryLightBoxAssoc[] $equipDeliveryLightBoxAssocs
 */
class EquipLightBoxDebug extends \yii\db\ActiveRecord
{
    /** 定义是否删除常量 */
    const DEL_NOT = 1;
    const DEL_YES = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_light_box_debug';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['debug_item'], 'required'],
            [['debug_item'], 'lightBoxDebugNameUniqueVeriry'],
            [['debug_item'], 'string', 'max' => 255],
            [['is_del', 'light_box_id'], 'integer'],
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
    public function lightBoxDebugNameUniqueVeriry($attribute) {
        $id = Yii::$app->request->get('id');
        if ($id) {
            $where = ['and', ['debug_item' => $this->debug_item], ['is_del' => self::DEL_NOT], ['light_box_id' => $this->light_box_id], ['!=', 'id', $id]];
        } else {
            $where = ['debug_item' => $this->debug_item, 'light_box_id' => $this->light_box_id, 'is_del' => self::DEL_NOT];
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
            'debug_item' => '灯箱调试项',
            'light_box_id' => '灯箱名称',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipAcceptanceLightBoxAssocs()
    {
        return $this->hasMany(EquipAcceptanceLightBoxAssoc::className(), ['equip_light_box_id' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipDeliveryLightBoxAssocs()
    {
        return $this->hasMany(EquipDeliveryLightBoxAssoc::className(), ['equip_light_box_id' => 'Id']);
    }

    /**
     * 获取灯箱验收选项列表
     * @return array 灯箱验收选项列表
     */
    public static function getLightBoxDebugList()
    {
        return self::find()->asArray()->all();
    }

    /**
     * 获取灯箱验收选项详情
     * @param  array    $where 查询条件
     * @return array        
     */
    public static function getLightBoxDebugDetail($where)
    {
        return self::find()->asArray()->where($where)->one();
    }

    /**
     * 返回灯箱调试项名称列表（搜索用）
     * @return  [type]     [description]
     */
    public static function getLightBoxDebugNameArr($lightBoxId) {
        return \yii\helpers\ArrayHelper::getColumn(self::find()->where(['is_del'=>self::DEL_NOT, 'light_box_id' => $lightBoxId])->all(), 'debug_item');
    }

    /**
     * 根据设备id获取灯箱调试项
     * @author  zgw
     * @version 2016-10-09
     * @param   string     $equipId [description]
     * @return  [type]              [description]
     */
    public static function getLightBoxDebugArrFromEquipId($equipId = '') {
        if ($equipId) {
            $lightBoxId = \common\models\Equipments::getField('light_box_id',['id' => $equipId]);
            if ($lightBoxId) {
                return self::find()->where(['is_del'=>self::DEL_NOT, 'light_box_id' => $lightBoxId])->asArray()->all();
            }
        }
        return [];
    }
}
