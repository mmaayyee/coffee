<?php

namespace backend\models;

use Yii;
use common\models\Api;

/**
 * This is the model class for table "light_belt_product_group".
 *
 * @property integer $id
 * @property string $product_group_name
 * @property string $choose_product
 */
class LightBeltProductGroup extends \yii\db\ActiveRecord
{
    public $product_group_name;  // 饮品组名称

    public $choose_product; // 所选饮品
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipments';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_group_name' => '饮品组名称',
            'choose_product' => '所选饮品',
        ];
    }
    
    /**
     * 通过接口获取所有单品(不过期的),并组合成id=》name形式数组
     * @author  zmy
     * @version 2017-06-16
     * @return  [type]     [description]
     */
    public static function getProductArr()
    {
        $productList =  json_decode(Api::getProductList(), true);
        $productArr = [];
        foreach ($productList as $product) {
            if ($product['cf_product_status'] != 2 && $product['cf_source_id'] == 0) {
                $productArr[$product['cf_product_id']] = $product['cf_product_name'];
            }else{
                continue;
            }
        }
        return $productArr;
    }

    /**
     * 组装要显示的饮品多选框，显示在页面中
     * @author  zmy
     * @version 2017-06-16
     * @return  [type]     [description]
     */
    public static function showProductList()
    {
        $productList =  self::getProductArr();
        $input      = "";
        foreach ($productList as $id => $product) 
        {
            if ($product) {
                $input  .= "<input type='checkbox' name='productIdArr[]' id='".$id."' value='".$id."'/><label style='margin-right:20px;' for='".$id."'>".$product."</label>";
            }
        }
        if($input){
            $input      =  $input."<div class='help-block'></div>";
        }else{
            $input      =   "<input type='hidden' id='no_product' value='0' ><div style='color:#a94442;'>暂无饮品数据</div>";
        }
        return $input;
    }

    /**
     * 获取所有的饮品组 id=》name 数组，并进行添加 请选择
     * @author  zmy
     * @version 2017-06-29
     * @return  [type]     [description]
     */
    public static function getProGroupList()
    {
        $proGroupList = json_decode(Api::getProductGroupNameList(), true);
        $proGroupList['']='请选择';
        ksort($proGroupList);
        return $proGroupList;
    }

}
