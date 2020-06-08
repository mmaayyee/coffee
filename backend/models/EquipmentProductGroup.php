<?php

namespace backend\models;

use backend\models\CoffeeRecipe;
use backend\models\EquipmentProductGroupList;
use common\helpers\Tools;
use common\models\EquipProductGroupApi;
use Yii;

/**
 * This is the model class for table "equipment_product_group".
 *
 * @property integer $product_group_id
 * @property string $group_name
 * @property string $group_desc
 */
class EquipmentProductGroup extends \yii\db\ActiveRecord
{
    /*
     * 上架状态
     */
    const ONLINE = 1;

    /** 发布状态常量 0-未发布 1-已发布*/
    const RELEASE_NO  = 0;
    const RELEASE_YES = 1;

    /** 是否自动刷新产品信息 0-否 1-是 */
    const UPDATE_PRODUCT_NO  = 0;
    const UPDATE_PRODUCT_YES = 1;

    /** 是否自动刷新配方信息 0-否 1-是 */
    const UPDATE_RECIPE_NO  = 0;
    const UPDATE_RECIPE_YES = 1;

    /** 是否自动刷新进度条 0-否 1-是 */
    const UPDATE_PROGRESS_NO  = 0;
    const UPDATE_PROGRESS_YES = 1;

    // 分组ID
    public $product_group_id;
    // 分组名称
    public $group_name;
    // 分组描述
    public $group_desc;
    // 是否显示领取咖啡按钮 0-显示 1-不显示
    public $setup_get_coffee;
    //不显示领取咖啡按钮时的提示文字
    public $setup_no_coffee_msg;
    //标记发布的版本号
    public $release_version;
    // 发布状态 0-未发布 1-已发布
    public $release_status;
    // 是否自动刷新产品信息 0-否 1-是
    public $is_update_product;
    // 是否自动刷新配方信息 0-否 1-是
    public $is_update_recipe;
    // 是否自动刷新进度条 1-是 0-否
    public $is_update_progress;
    // 产品组料仓信息ID
    public $pro_group_stock_info_id;

    public $build_type_upload; // 楼宇上传类型

    public $verifyFile; // 上传文件

    public $build_upload_url; // 楼宇文件上传路径

    public $equip_type; // 设备类型

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_name'], 'required'],
            [['setup_get_coffee', 'release_version', 'release_status', 'is_update_product', 'is_update_recipe', 'is_update_progress', 'pro_group_stock_info_id', 'product_group_id'], 'integer'],
            [['group_name', 'group_desc', 'setup_no_coffee_msg'], 'string', 'max' => 255],
            [['build_upload_url'], 'string'],
            [['verifyFile'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_group_id'        => '产品组ID',
            'group_name'              => '分组名称',
            'group_desc'              => '分组描述',
            'setup_get_coffee'        => '是否显示领取咖啡',
            'setup_no_coffee_msg'     => '不显示领取咖啡时提示文字',
            'release_version'         => '发布版本号',
            'release_status'          => '发布状态',
            'is_update_product'       => '是否自动刷新产品信息',
            'is_update_recipe'        => '是否自动刷新配方信息',
            'is_update_progress'      => '是否自动刷新进度条',
            'pro_group_stock_info_id' => '产品组料仓信息ID',
            'build_type_upload'       => '楼宇上传类型',
            'build_upload_url'        => '楼宇文件上传路径',
            'equip_type'              => '设备类型',
        ];
    }

    /**
     * 获取发布状态数组
     * @author  zmy
     * @version 2017-10-20
     * @return  [type]     [description]
     */
    public static function getReleaseStatusArr()
    {
        return [
            self::RELEASE_NO  => '未发布',
            self::RELEASE_YES => '已发布',
        ];
    }
    /**
     * 是否显示领取咖啡
     * @author  zmy
     * @version 2017-10-20
     * @return  [type]     [description]
     */
    public static function getSetupGetCoffee()
    {
        return [
            0 => '是',
            1 => '否',
        ];
    }
    /**
     * 获取发布状态数组或者指定状态的名称
     * @author  zgw
     * @version 2017-06-02
     * @param   string|int     $releaseStatus 发布状态的值
     * @return  array|string                  发布状态数组或者指定状态的名称
     */
    public function getReleaseStatus($releaseStatus = '')
    {
        $releaseStatusArr = [
            self::RELEASE_NO  => '未发布',
            self::RELEASE_YES => '已发布',
        ];
        if ($releaseStatus !== '') {
            return !isset($releaseStatusArr[$releaseStatus]) ? '' : $releaseStatusArr[$releaseStatus];
        }
        return $releaseStatusArr;
    }
    /**
     * 获取是否自动刷新产品信息数组或者指定状态的名称
     * @author  zgw
     * @version 2017-06-02
     * @param   string|int     $updateProduct 是否自动刷新产品信息的值
     * @return  array|string                  是否自动刷新产品信息数组或者指定状态的名称
     */
    public function getUpdateProduct($updateProduct = '')
    {
        $updateProductArr = [
            self::UPDATE_PRODUCT_NO  => '否',
            self::UPDATE_PRODUCT_YES => '是',
        ];
        if ($updateProduct !== '') {
            return !isset($updateProductArr[$updateProduct]) ? '' : $updateProductArr[$updateProduct];
        }
        return $updateProductArr;
    }
    /**
     * 获取是否自动刷新配方数组或者指定状态的名称
     * @author  zgw
     * @version 2017-06-02
     * @param   string|int     $updateRecipe 是否自动刷新配方的值
     * @return  array|string                  是否自动刷新配方数组或者指定状态的名称
     */
    public function getUpdateRecipe($updateRecipe = '')
    {
        $updateRecipeArr = [
            self::UPDATE_RECIPE_NO  => '否',
            self::UPDATE_RECIPE_YES => '是',
        ];
        if ($updateRecipe !== '') {
            return !isset($updateRecipeArr[$updateRecipe]) ? '' : $updateRecipeArr[$updateRecipe];
        }
        return $updateRecipeArr;
    }

    /**
     * 获取是否自动刷新进度条或者指定状态的名称
     * @author  zgw
     * @version 2017-06-02
     * @param   string|int     $updateProgress 是否自动刷新进度条
     * @return  array|string                  是否自动刷新进度条数组或者指定状态的名称
     */
    public function getUpdateProgress($updateProgress = '')
    {
        $updateProgressArr = [
            self::UPDATE_PROGRESS_NO  => '否',
            self::UPDATE_PROGRESS_YES => '是',
        ];
        if ($updateProgress !== '') {
            return !isset($updateProgressArr[$updateProgress]) ? '' : $updateProgressArr[$updateProgress];
        }
        return $updateProgressArr;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentProductGroupList()
    {
        return $this->hasMany(EquipmentProductGroupList::className(), ['product_group_id' => 'product_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentGroupStocks()
    {
        return $this->hasMany(EquipmentProductGroupStock::className(), ['product_group_id' => 'product_group_id']);
    }

    /**
     * 获取料仓上限、出料速度等数据
     */
    public function getStockData()
    {
        $stockList = $this->equipmentGroupStocks;
        $dataArray = array();
        foreach ($stockList as $stock) {
            $dataArray[$stock->stock_code] = array(
                'full'        => $stock->stock_volume_bound, //满量（克）
                'second'      => $stock->blanking_rate, //每秒出料
                'material_id' => $stock->materiel_id, //每秒出料
            );
        }
        return $dataArray;
    }

    /**
     * 产品组是否包括指定产品
     * @param type $productID 产品ID
     * @return boolean
     */
    public function hasProduct($productID)
    {
        $hasProduct  = false;
        $productList = $this->equipmentProductGroupList;
        foreach ($productList as $product) {
            if ($product->product_id == $productID) {
                $hasProduct = true;
                break;
            }

        }
        return $hasProduct;
    }

    /**
     * 获取产品组指定产品
     * @param type $productID 产品ID
     * @return boolean
     */
    public function getProduct($productID)
    {
        $userProduct = null;
        $productList = $this->equipmentProductGroupList;
        foreach ($productList as $product) {
            if ($product->product_id == $productID) {
                $userProduct = $product;
                break;
            }
        }
        return $userProduct;
    }

    /**
     * 获取设备类型料仓数组
     * @return array 设备类型料仓数组
     */
    public function getEquipmentTypeSotckArray()
    {
        $stockBins  = $this->recipe->equipmentType->stockBins;
        $stockArray = array();
        foreach ($stockBins as $bin) {
            $stockArray[$bin->stock_code] = $bin->stock_bin_name;
        }
        return $stockArray;
    }

    /**
     * 获取分组状态数组
     * @return array 分组数据数组
     */
    public function getStatusArray()
    {
        return array(
            '0' => '下架',
            '1' => '正常',
        );
    }

    /**
     *获取配方名称
     * @param int $recipeId
     *@return string 配方名称
     **/
    public function getRecipeName($recipeId)
    {
        $RecipeNames = CoffeeRecipe::coffeeRecipeArray();
        return $RecipeNames[$recipeId];
    }

    /**
     * 获取产品组上下限
     * @return array 产品组料仓上下限
     */

    public static function getGroupStockLimit()
    {
        $topLimitArray = array();
        $groupList     = self::find()->all();
        $bottomLimit   = self::getBottomLimit();

        foreach ($groupList as $group) {
            $groupArray = array(
                'gid'   => $group->product_group_id,
                'gname' => $group->group_name,
            );
            $stockData = $group->getStockData();
            foreach ($stockData as $stockCode => $stock) {
                $limiePercentKey = 'setup_alert_position_' . $stockCode;
                $limiePercent    = 0.1;
                if (array_key_exists($limiePercentKey, $bottomLimit)) {
                    $limiePercent = $bottomLimit['setup_alert_position_' . $stockCode];
                }
                $groupArray[$stockCode] = array(
                    'gstockTop'        => $stock['full'],
                    'material_type_id' => $stock['material_id'],
                    'gstockBottom'     => $limiePercent * $stock['full'],
                    'second'           => $stock['second'],
                );
            }
            $topLimitArray[$group->product_group_id] = $groupArray;
        }
        return $topLimitArray;
    }

    /**
     * 获取料仓下限值
     * @return array 料仓下限值
     */
    private static function getBottomLimit()
    {
        $bottomArray = array();
        $setupList   = \common\models\Sysconfig::find()->where("config_key like 'setup_alert_position%'")->all();
        foreach ($setupList as $setup) {
            $bottomArray[$setup->config_key] = $setup->config_value / 100;
        }
        return $bottomArray;
    }

    // 根据设备型号获取分组数据
    public static function getGroup($equipTypeId = '', $type = 1)
    {
        $query = self::find()->joinWith('recipe');
        $query->andFilterWhere(['coffee_recipe.equipment_type_id' => $equipTypeId]);
        $groupList = $query->all();
        if ($type == 1) {
            return Tools::map($groupList, 'product_group_id', 'group_name');
        } else {
            return Tools::map($groupList, 'product_group_id', 'group_name', 'recipe.equipment_type_id');
        }
    }

    /**
     * 获取分组产品价格
     * @author  zgw
     * @version 2017-06-07
     * @param   int     $productID 分组产品ID
     * @return  [type]                [description]
     */
    public function getGroupProductPrice($productID)
    {
        $userProduct = $this->getProduct($productID);
        if (!$userProduct) {
            return false;
        }
        // 判断设备端是否使用折扣价
        if ($userProduct->getIsDiscount() == EquipmentProductGroupList::DISCOUNT_YES) {
            return $userProduct->group_coffee_discount_price;
        }
        return $userProduct->group_coffee_price;
    }

    /**
     * 获取是产品否可以使用优惠券
     * @author  zgw
     * @version 2017-06-21
     * @param   int     $productID 产品id
     * @return  int                是否可以使用优惠券 0-否 1-是
     */
    public function getProductIsUseCoupon($productID)
    {
        $userProduct = $this->getProduct($productID);
        if (!$userProduct) {
            return false;
        }
        return $userProduct->is_use_coupon ? true : false;
    }

    /**
     * 更新发布版本信息
     * @author  zgw
     * @version 2017-06-02
     * @return  boole     更新结果 TRUE成功 false失败
     */
    public function updateReleaseVersion()
    {
        $this->release_version += 1;
        $this->release_status = self::RELEASE_YES;
        return $this->save();
    }

    /**
     * 更新发布状态
     * @author  zgw
     * @version 2017-06-02
     * @return  [type]     [description]
     */
    public function updateRelaseStatus()
    {
        $this->release_status = self::RELEASE_NO;
        return $this->save();
    }

    /**
     * 是否显示产品组的发布按钮
     * @author  zgw
     * @version 2017-06-13
     * @return  boolean    true不显示 FALSE显示
     */
    public function isShowPublic($model)
    {
        return !\Yii::$app->user->can('产品组发布') || $model->release_status == self::RELEASE_YES || ($model->is_update_product == self::UPDATE_PRODUCT_NO && $model->is_update_recipe == self::UPDATE_RECIPE_NO && $model->is_update_progress == self::UPDATE_PROGRESS_NO);
    }

    /**
     * 接口获取产品组信息
     * @author  zmy
     * @version 2017-09-13
     * @param   [string]     $proGroupId [产品组ID]
     * @return  [obj]                    [model]
     */
    public static function getEquipProductGroupById($proGroupId)
    {
        $model        = new self();
        $proGroupList = EquipProductGroupApi::getEquipProductGroupById($proGroupId);
        $model->load(['EquipmentProductGroup' => $proGroupList]);
        return $model;
    }

    /**
     * 根据产品组料仓信息ID，查询设备类型
     * @author  zmy
     * @version 2017-10-18
     * @param   [type]     $stockInfoId [产品组料仓信息ID]
     * @return  [type]                  [设备类型]
     */
    public static function getEquipTypeByStockInfoId($stockInfoId)
    {
        return EquipProductGroupApi::getEquipTypeByStockInfoId($stockInfoId);
    }

    /**
     * 通过产品组料仓ID，获取产品组料仓名称
     * @author  zmy
     * @version 2017-10-27
     * @param   [string]     $proGroupStockInfoId [产品组料仓ID]
     * @return  [Array]                           [产品组料仓信息数组]
     */
    public static function getProGroupStockInfoByStockId($proGroupStockInfoId)
    {
        return EquipProductGroupApi::getProGroupStockInfoByStockId($proGroupStockInfoId);
    }

    /**
     * 获取产品组料仓信息
     * @author zhenggangwei
     * @date   2020-01-09
     * @return array
     */
    public static function proGroupStockList()
    {
        $equipTypeIdNameList = ScmEquipType::getEquipTypeIdNameArr();
        unset($equipTypeIdNameList['']);
        $proGroupStockList = EquipProductGroupApi::getProGroupStockList();
        $grouStoIdNameList = $grouStoIdEtypeNameList = [];
        foreach ($proGroupStockList as $stock) {
            $grouStoIdNameList[$stock['id']]      = $stock['product_group_stock_name'];
            $grouStoIdEtypeNameList[$stock['id']] = ['name' => $equipTypeIdNameList[$stock['equip_type_id']] ?? '', 'id' => $stock['equip_type_id']];
        }
        return [$grouStoIdNameList, $grouStoIdEtypeNameList];
    }

}
