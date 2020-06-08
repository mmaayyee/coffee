<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_type_product_config".
 *
 * @property integer $id
 * @property integer $equip_type_id
 * @property integer $product_id
 * @property integer $cf_choose_sugar
 * @property double $half_sugar
 * @property double $full_sugar
 * @property integer $brew_up
 * @property integer $brew_down
 */
class EquipTypeProductConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_type_product_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'product_id', 'cf_choose_sugar', 'brew_up', 'brew_down'], 'integer'],
            [['half_sugar', 'full_sugar'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equip_type_id' => 'Equip Type ID',
            'product_id' => 'Product ID',
            'cf_choose_sugar' => 'Cf Choose Sugar',
            'half_sugar' => 'Half Sugar',
            'full_sugar' => 'Full Sugar',
            'brew_up' => 'Brew Up',
            'brew_down' => 'Brew Down',
        ];
    }
}
