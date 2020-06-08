<?php

namespace backend\models;

use common\models\Api;
use common\models\Equipments;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "equip_brew".
 *
 * @property integer $id
 * @property string $equip_code
 * @property integer $product_id
 * @property integer $brew_time
 * @property integer $create_time
 *
 * @property Equipments $equipCode
 */
class EquipBrew extends \yii\db\ActiveRecord
{
    public $build_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_brew';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'brew_time', 'create_time'], 'integer'],
            [['equip_code'], 'string', 'max' => 64],
            [['equip_code'], 'exist', 'skipOnError' => true, 'targetClass' => Equipments::className(), 'targetAttribute' => ['equip_code' => 'equip_code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'equip_code'  => '设备编号',
            'product_id'  => '产品名称',
            'brew_time'   => '冲泡器时间',
            'create_time' => '添加时间',
            'build_name'  => '楼宇名称',
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
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(CoffeeProduct::className(), ['equip_code' => 'equip_code']);
    }

    /**
     * 保存设备冲泡器时间信息
     * @author  zgw
     * @version 2017-04-10
     * @param   [type]     $data [description]
     */
    public static function addData($data)
    {
        $model              = new EquipBrew();
        $model->equip_code  = $data['equip_code'];
        $model->product_id  = $data['product_id'];
        $model->brew_time   = !isset($data['brew_time']) ? '' : $data['brew_time'];
        $model->create_time = time();
        return $model->save();
    }

    /**
     * 获取产品id和名称的数组
     * @author  zgw
     * @version 2017-05-04
     * @return  [type]     [description]
     */
    public static function getProductNameList()
    {
        // 获取产品列表
        $productList      = Json::decode(Api::getProductList(), 1);
        $productIdNameArr = [];
        foreach ($productList as $product) {
            $productIdNameArr[$product['cf_product_id']] = $product['cf_product_name'];
        }
        return $productIdNameArr;
    }
}
