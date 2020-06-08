<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "equip_light_box".
 *
 * @property integer $id
 * @property string $light_box_name
 * @property integer $is_del
 */
class EquipLightBox extends \yii\db\ActiveRecord
{
    /** 定义是否删除常量 */
    const DEL_NOT = 1;
    const DEL_YES = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_light_box';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['light_box_name'], 'required'],
            [['light_box_name'], 'lightBoxNameUniqueVeriry'],
            [['is_del'], 'integer'],
            [['light_box_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * 验证未删除的灯箱唯一性
     * @author  zgw
     * @version 2016-09-26
     * @param   [type]     $attribute [description]
     * @param   [type]     $params    [description]
     * @return  [type]                [description]
     */
    public function lightBoxNameUniqueVeriry($attribute)
    {
        $id = Yii::$app->request->get('id');
        if ($id) {
            $where = ['and', ['light_box_name' => $this->light_box_name], ['is_del' => self::DEL_NOT], ['!=', 'id', $id]];
        } else {
            $where = ['light_box_name' => $this->light_box_name, 'is_del' => self::DEL_NOT];
        }
        $isExite = self::find()->where($where)->one();
        if ($isExite) {
            $this->addError($attribute, "灯箱名称的值" . $this->light_box_name . "已经被占用了。");
        }

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => '灯箱id',
            'light_box_name' => '灯箱名称',
            'is_del'         => '1-没有删除 2-已删除',
        ];
    }

    /**
     * 返回灯箱名称列表(搜索用)
     * @author  zgw
     * @version 2016-09-26
     * @return  [type]     [description]
     */
    public static function getLightBoxNameArr()
    {
        return ArrayHelper::getColumn(self::find()->where(['is_del' => self::DEL_NOT])->all(), 'light_box_name');
    }

    /**
     * 返回灯箱名称列表(搜索用)
     * @author  zgw
     * @version 2016-09-26
     * @return  [type]     [description]
     */
    public static function getLightBoxIdNameArr($type = 1)
    {
        return Tools::map(self::find()->where(['is_del' => self::DEL_NOT])->all(), 'id', 'light_box_name', null, $type);
    }
}
