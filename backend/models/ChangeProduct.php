<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "change_product".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $build_id
 * @property integer $last_product_id
 * @property integer $present_product_id
 * @property integer $created_user
 * @property string $create_time
 */
class ChangeProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'change_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'build_id', 'last_product_id', 'present_product_id', 'created_user'], 'integer'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equip_id' => 'Equip ID',
            'build_id' => 'Build ID',
            'last_product_id' => 'Last Product ID',
            'present_product_id' => 'Present Product ID',
            'created_user' => 'Created User',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 获取指定的信息
     * @param $field
     * @param $where
     * @return bool|mixed
     */
    public static function getField($field,$where){
        $product = self::find()->select($field)->where($where)->one();
        return $product ? $product->$field : false;
    }

    /**
     * 保存产品组修改记录
     * @param $data
     * @return bool
     */
    public static function addProductGroupChangeRecord($data){
        $productGroup = self::findOne(['equip_id' => $data['equip_id']]);
        if($productGroup){
            $productGroup->build_id = $data['build_id'];
            $productGroup->last_product_id = $data['last_product_id'];
            $productGroup->present_product_id = $data['present_product_id'];
            $productGroup->created_user = $data['created_user'];
            return $productGroup->save();
        }else{
            $model = new ChangeProduct();
            $model->equip_id = $data['equip_id'];
            $model->build_id = $data['build_id'];
            $model->last_product_id = $data['last_product_id'];
            $model->present_product_id = $data['present_product_id'];
            $model->created_user = $data['created_user'];
            return $model->save();
        }
    }

    /**
     * 判断设备本次产品组和上次产品组是否一致
     * @author wxl
     * @param int $equipId
     * @return bool
     */
    public static function IsSameLastProductIdPresentProductId($equipId = 0){
        $productGroupIds = self::find()->select('last_product_id, present_product_id')->where(['equip_id' => $equipId])->asArray()->one();
        return $productGroupIds ? ($productGroupIds['last_product_id'] == $productGroupIds['present_product_id']) : true;
    }

    /**
     * 修改设备产品组上次产品组ID为本次产品组ID
     * @author wxl
     * @param int $equipmentId
     * @return bool
     */
    public static function modifyLastProductId($equipmentId = 0){
        $productGroup = self::findOne(['equip_id' => $equipmentId]);
        if($productGroup){
            $productGroup->last_product_id = $productGroup->present_product_id;
            return $productGroup->save();
        }
        return false;
    }
}
