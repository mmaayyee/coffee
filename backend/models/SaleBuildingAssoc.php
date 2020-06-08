<?php

namespace backend\models;
use Yii;
use common\models\Api;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class SaleBuildingAssoc extends \yii\db\ActiveRecord
{
    public $sale_id;
    public $build_id;
    public $sale_name;
    public $build_name;
    public $qrcode_img;
    public $sale_arr;
    public $build_arr;
    public $isNewRecord;
    public $id;
    public $sale_email;
    public $sale_list;
    public $sale_phone;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_id','build_id'], 'integer'],
            [['qrcode_img','sale_email','build_name','sale_name','sale_phone'], 'string'],
            [['id'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'qrcode_img'    => '二维码',
            'sale_name'     => '姓名',
            'build_name'    => '楼宇',
            'sale_id'       => '销售名称',
            'build_id'      => '楼宇',
            'sale_email'    => '邮箱',
            'sale_phone'    => '手机号',
        ];
    }

    /**
     * 获取楼宇名称
     */

    public function getBuildName($build_arr,$build_id){
        return isset($build_arr[$build_id]) ? $build_arr[$build_id] : '';
    }

    /**
     * 获取销售名称
     */
    public function getSaleName($sale_arr,$sale_id){
        return isset($sale_arr[$sale_id]) ? $sale_arr[$sale_id] : '';
    }

    /**
     * 获取某个字段
     */
    public function getSaleField($sale_list,$sale_id,$field){
        foreach ($sale_list as $key => $value) {
            if($value['sale_id'] == $sale_id){
                return $value[$field];
            }
        }
        return '';
    }

    /**
     * 获取销售名称列表
     */
    public static function getSaleNameList(){
        return Api::getSaleNameList();
    }
    /**
     * 获取楼宇名称列表
     */
    public static function getBuildNameList(){
        return Api::getBuildNameList();
    }
    /**
     * 添加 销售与楼宇的邀请链接所生成的二维码图片
     */
    public static function saleBuildingAssocCreate($params){
        return Api::saleBuildingAssocCreate($params);
    }
}