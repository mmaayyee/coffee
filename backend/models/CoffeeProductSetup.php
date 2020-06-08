<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "coffee_product_setup".
 *
 * @property integer $setup_id
 * @property integer $product_id
 * @property string $equip_type_id
 * @property integer $order_number
 * @property double $water
 * @property double $delay
 * @property double $volume
 * @property double $stir
 * @property string $stock_code
 * @property integer $blanking
 * @property integer $mixing
 */
class CoffeeProductSetup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coffee_product_setup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'equip_type_id', 'order_number', 'blanking', 'mixing'], 'integer'],
            [['water', 'delay', 'volume', 'stir'], 'number'],
            [['stock_code'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'setup_id' => 'Setup ID',
            'product_id' => 'Product ID',
            'equip_type_id' => 'Equip Type ID',
            'order_number' => 'Order Number',
            'water' => 'Water',
            'delay' => 'Delay',
            'volume' => 'Volume',
            'stir' => 'Stir',
            'stock_code' => 'Stock Code',
            'blanking' => 'Blanking',
            'mixing' => 'Mixing',
        ];
    }
}
