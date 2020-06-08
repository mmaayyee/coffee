<?php
namespace common\models;

use backend\models\ScmMaterialStock;
use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class Api extends \yii\db\ActiveRecord
{
    /**
     * 服务密钥配置
     */
    public static function encrptConfig()
    {
        return array(
            'coffee08'    => array( //erp密钥配置
                'screct'  => 'daE8p5yQbm0U6Nwd',
                'encrypt' => '50nGI1JW0OHfk8ah',
            ),
            'coffee08Erp' => array( //erp密钥配置
                'screct'  => ' 5039374e82E5f7B7',
                'encrypt' => 'RUJmwsslBFkZGNcY',
            ),
        );
    }

    /**
     * 验证服务密钥是否合法
     * @param type $key  应用ID
     * @param type $secretString 加密串
     * @return type
     */
    public static function verifyService($key, $secretString)
    {
        $verifyResult = false;
        $config       = self::encrptConfig();
        if (array_key_exists($key, $config)) {
            //存在
            $screct        = $config[$key]['screct'];
            $encrypt       = $config[$key]['encrypt'];
            $encryptString = md5($encrypt . $screct);
            $verifyResult  = $secretString === $encryptString;
        }
        return $verifyResult;
    }

    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data)
    {
        // echo Yii::$app->params['coffeeUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE);die;
        $res = Tools::http_post(Yii::$app->params['coffeeUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($res === 'true' || $res == 1) {
            return true;
        }
        return false;
    }

    /**
     * post提交数据带有参数返回的共用方法
     * @author  tuqiang
     * @version 2017-09-06
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  array              返回的数据
     */
    private static function postBaseGetData($action, $data, $params = "")
    {
        // echo Yii::$app->params['coffeeUrl'] . $action . self::verifyString() . $params, json_encode($data, JSON_UNESCAPED_UNICODE);die;
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . $action . self::verifyString() . $params, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    /**
     * post提交数据到机构管理的专用接口文件
     * @author  tuqiang
     * @version 2017-09-06
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  array              返回的数据
     */
    public static function postBaseGetOrgData($action, $data, $params = "")
    {
        // echo Yii::$app->params['fcoffeeUrl'] . 'organization-api/' . $action . self::verifyString();json_encode($data, JSON_UNESCAPED_UNICODE);die();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . 'organization-api/' . $action . self::verifyString() . $params, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * post提交数据到组织机构接口获取返回值
     * @author  tuqiang
     * @version 2017-09-07
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  array              返回的数据
     */
    private static function postBaseOrg($action, $data)
    {
        //echo  Yii::$app->params['coffeeOrgUrl'] . $action . self::verifyString();die();
        return Tools::http_post(Yii::$app->params['coffeeOrgUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    /**
     * post提交数据获取平均消耗方法
     * @author  wangxiwen
     * @version 2017-05-17
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的日期数据
     * @return  boole              返回的数据
     */
    public static function postBaseGetAvgConsume($action, $data)
    {
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    /**
     * post提交数据更新剩余物料
     * @author  wangxiwen
     * @version 2017-06-19
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的日期数据
     * @return  boole              返回的数据
     */
    public static function postBaseSurplusMaterial($action, $data)
    {
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function getBase($action, $params = '')
    {
//         echo Yii::$app->params['coffeeUrl'] . $action . self::verifyString() . $params;die;
        return Tools::http_get(Yii::$app->params['coffeeUrl'] . $action . self::verifyString() . $params);
    }
    /**
     * 获取设备产品组料仓信息
     * @author  wangxiwen
     * @version 2018-09-03
     * @return  [type]     [description]
     */
    public static function getEquipProductGroupStockInfo($action, $params = '')
    {
        $groupInfo = Tools::http_get(Yii::$app->params['coffeeUrl'] . $action . self::verifyString() . $params);
        return !empty($groupInfo) ? Json::decode($groupInfo) : [];
    }

    /**
     * 获取设备配方调整数据
     * @author  wangxiwen
     * @version 2018-07-26
     * @return  [type]     [description]
     */
    public static function getFormulaAdjustment($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
    /**
     * 更新设备配方调整数据
     * @author  wangxiwen
     * @version 2018-07-26
     * @return  [type]     [description]
     */
    public static function saveFormulaAdjustment($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . json_encode($params, JSON_UNESCAPED_UNICODE);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, json_encode($params, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取一维组织架构数组
     * @author  zmy
     * @version 2017-11-06
     * @param   [type]     $data [传输参数]
     * @return  [type]           [description]
     */
    public static function getOrgInfoListOne($data)
    {
        $ret = self::postBaseOrg('get-org-info-list-one', $data);
        return $ret ? Json::decode($ret) : [];
    }

    /**
     * 获取平均消耗物料
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]    [description]
     */
    public static function consume()
    {
        return self::getBase("avg-consume");
    }
    /**
     * 获取剩余物料
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function surplus()
    {
        return self::getBase("stock-remain");
    }

    /**
     * 获取设备料仓和物料对应关系以及其上下线值
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function stockLimit()
    {
        return self::getBase("stock-limit");
    }

    /**
     * 获取楼宇二维码
     * @author  zgw
     * @version 2016-09-09
     * @param   [type]     $equipCode [description]
     * @return  [type]              [description]
     */
    public static function buildCode($equipCode)
    {
        return self::getBase("get-qrcode", "&bq=" . $equipCode);
    }

    /**
     * 根据产品型号获取产品组数据
     * @author  zgw
     * @version 2016-09-11
     * @param   [type]     $equipTypeId [description]
     * @return  [type]                  [description]
     */
    public static function getGroups($equipTypeId = '', $type = 1)
    {
        return self::getBase("get-groups", "&equipTypeId=" . $equipTypeId . '&type=' . $type);
    }

    /**
     * 获取产品接口
     * @author  zgw
     * @version 2016-09-11
     * @param   [type]     $groupId [description]
     * @return  [type]              [description]
     */
    public static function getProducts($groupId, $type = 1)
    {
        return self::getBase("get-group-products", "&groupId=" . $groupId . '&type=' . $type);
    }

    /**
     * 根据产品组ID，type获取上下架产品的接口，
     * @author  zmy
     * @version 2017-05-19
     * @return  [type]     [description]
     */
    public static function getProductOfflineList($equipCode, $groupId, $offlineType)
    {
        return self::getBase("get-products-by-type", "&groupId=" . $groupId . '&type=' . $offlineType . "&equipCode=" . $equipCode);
    }

    /**
     * 对下架产品进行上架( 删除数据 )
     * @author  zmy
     * @version 2017-06-03
     * @param   [type]     $data [设备产品下线表 中 ID]
     * @return  [type]         [description]
     */
    public static function productLineSync($data)
    {
        return self::getBase('product-offline-del', "&data=" . $data);
    }

    /**
     * 根据传输的data数据查询出需要 上下架库中的数据
     * @author  zmy
     * @version 2017-06-02
     * @param   [type]     $data [传输的条件数组]
     * @return  [type]           [json string]
     */
    public static function getProductOfflineArr($data, $pageSize = 20, $page = 1, $orgID)
    {
        return self::getBase("get-products-list-by-where", "&productListJson=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page . "&org_id=" . $orgID);
    }

    /**
     * 代理商删除接口
     * @author  zgw
     * @version 2016-11-18
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function organizationDel($data)
    {
        return self::getBase('organization-del', $data);
    }

    /**
     * 同步设备类型
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function equipTypeSync($data)
    {
        return self::postBase('equipment-type-update', $data);
    }

    /**
     * 设备类型和料仓关联数据同步
     * @author  zgw
     * @version 2016-08-30
     * @param   array     $data  要同步的数据
     * @return  string
     */
    public static function matstockSync($data)
    {
        $equipTypeMaterialStockArr['id']     = $data['id'];
        $equipTypeMaterialStockArr['name']   = $data['model'];
        $equipTypeMaterialStockArr['stocks'] = [];
        if ($data['matstock']) {
            foreach ($data['matstock'] as $matstockId) {
                $materialStockDetail = ScmMaterialStock::getMaterialStockDetail('stock_code, name', ['id' => $matstockId]);
                if ($materialStockDetail) {
                    $equipTypeMaterialStockArr['stocks'][$materialStockDetail['stock_code']] = $materialStockDetail['name'];
                }
            }
        }
        return self::postBase('equipment-type-stock-update', $equipTypeMaterialStockArr);
    }

    /**
     * 物料分类同步接口
     * @author  zgw
     * @version 2016-08-29
     * @return  string     调用接口后的返回结果
     */
    public static function materialTypeSync($data)
    {
        return self::postBase('material-type-update', $data);
    }

    /**
     * 同步楼宇信息
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function buildSync($data)
    {
        return self::postBase('building-update', $data);
    }
    /**
     * 批量同步楼宇信息
     * @author  GaoYongLi
     * @version 2018-05-30
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function buildSyncAll($data)
    {
        return self::postBase('building-update-all', $data);
    }
    /**
     * 同步设备信息
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function equipmentSync($data)
    {
        return self::postBase('equipment-update', $data);
    }

    /**
     * 设备绑定解绑接口
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function equipmentBind($data)
    {
        return self::postBase('equipment-bind', $data);
    }

    /**
     * 设备锁定解锁接口
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function equipmentLock($data)
    {
        return self::postBase('equipment-lock', $data);
    }

    /**
     * 物料消耗同步
     * @author  zgw
     * @version 2016-11-04
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function materialConsumeSync($data)
    {
        return self::postBase('stock-material-supply', $data);
    }

    /**
     * 代理商更新接口
     * @author  zgw
     * @version 2016-11-18
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function organizationUpdate($data)
    {
        return self::postBase('organization-update', $data);
    }

    /**
     * coffee后台与erp 返回信息（门禁卡接口返回信息）
     * @author  zmy
     * @version 2016-12-12
     * @param   string     $code      [错误编码]
     * @param   string     $msg       [提示信息]
     * @param   boolean    $available [是否是合法卡]
     * @param   boolean    $owner     [是否指定运维人员]
     * @param   boolean    $open      [是否开门]
     * @return  [type]                [json格式返回]
     */
    public static function retResult($code = '', $msg = '', $available = false, $owner = false, $open = false)
    {
        return json_encode([
            'code'      => $code, //  返回的错误编码
            'msg'       => $msg, //  错误提示信息
            'available' => $available, //  合法卡（判断是否为合法的卡） （兼容原有设备app版本）
            'owner'     => $owner, //  指定运维人员
            'open'      => $open, //  是否开门
        ], JSON_UNESCAPED_UNICODE);die;
    }

    /**
     * 获取单品列表
     * @author  zgw
     * @version 2017-05-04
     * @return  [type]     [description]
     */
    public static function getProductList()
    {
        return self::getBase("get-products");
    }
    /**
     * 获取可用优惠券列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-06-26
     * @param:    [param]
     * @return
     * @return    [type]     [description]
     */
    public static function activityCombinGetCouponList()
    {
        $quickSendCouponData = self::getBase("get-activity-combin-coupon-lists");
        if ($quickSendCouponData) {
            $couponList = Json::decode($quickSendCouponData);
            $couponName = [];
            foreach ($couponList as $key => $coupon) {
                $couponName[$coupon['coupon_id']] = $coupon['coupon_name'];
            }
            return $couponName;
        }
        return [];

    }
    /**
     * 更新楼宇类型
     * @author  zgw
     * @version 2017-05-22
     * @param   array     $data 楼宇类型数组
     * @return  boole           保存结果
     */
    public static function saveBuildType($data)
    {
        $res = Tools::http_post(Yii::$app->params['coffeeUrl'] . 'save-build-type' .
            self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($res === 'true' || $res == 1) {
            return true;
        } else {
            return \yii\helpers\Json::decode($res);
        }

//        return false;
        //        return self::postBase('save-build-type', $data);
    }

    /**
     * 获取楼宇类型列表
     * @author  zgw
     * @version 2017-05-22
     * @param   string     $name 楼宇类型名称
     * @return  array                     楼宇类型列表
     */
    public static function getBuildType($name = '')
    {
        return self::getBase("get-build-type", '&name=' . $name);
    }
    public static function getBuildTypeCode($code = '')
    {
        return self::getBase("get-build-type", '&code=' . $code);
    }
    /**
     * 根据楼宇id获取楼宇类型信息
     * @author  zgw
     * @version 2017-05-23
     * @param   int     $id 楼宇类型id
     * @return  array       楼宇类型信息
     */
    public static function getBuildTypeInfo($id)
    {
        return self::getBase("get-build-type-info", '&id=' . $id);
    }
    /**
     * 产品上、下架接口处理
     * @author  zmy
     * @version 2017-05-17
     * @return  [type]     [description]
     */
    public static function equipProductOfflineSync($data)
    {
        return self::postBase('operation-product-offline', $data);
    }

    /**
     * 获取优惠券套餐列表
     * @author  zgw
     * @version 2017-05-25
     * @return  array      优惠券套餐列表
     */
    public static function getCouponGroup()
    {
        $couponGroupList = self::getBase("get-coupon-group");
        return !$couponGroupList ? [] : Json::decode($couponGroupList, 1);
    }

    /**
     * 获取优惠券套餐及其对应的优惠券信息
     * @author  zgw
     * @version 2017-08-25
     * @param   integer     $couponGroupID 优惠券套餐id
     * @return  array                      优惠券套餐及其对应的优惠券信息
     */
    public static function getCouponGroupInfo($couponGroupID = 0)
    {
        $couponGroupInfo = self::getBase("get-coupon-group-info", '&couponGroupID=' . $couponGroupID);
        return !$couponGroupInfo ? [] : Json::decode($couponGroupInfo);
    }

// --------------------------------------------------------灯带接口APi
    /**
     * 存入灯带饮品组表
     * @author  zmy
     * @version 2017-06-16
     * @return  [type]     [description]
     */
    public static function saveLightBeltProductGroup($data)
    {
        return self::postBase("save-light-belt-product-group", $data);
    }

    /**
     * 通过id 删除灯带饮品组数据
     * @author  zmy
     * @version 2017-06-17
     * @return  [type]     [true/false]
     */
    public static function getDelLightBelProductGroupById($id)
    {
        return self::getBase("del-light-belt-pro-group-by-id", "&id=" . $id);
    }

    /**
     * 根据楼宇编号，删除相关的方案
     * @author  zmy
     * @version 2017-12-15
     * @param   [type]     $buildNumber [description]
     * @return  [type]              [description]
     */
    public static function getDelLightProgramAssocByBuildNumber($buildNumber)
    {
        return self::getBase("del-light-program-assoc-by-build-number", "&build_number=" . $buildNumber);
    }

    /**
     * 通过饮品组ID，查询使用的策略名称
     * @author  zmy
     * @version 2017-07-14
     * @param   [type]     $pro_group_id [description]
     * @return  [type]                   [description]
     */
    public static function getUseScenarioByProGroupId($proGroupID)
    {
        return self::getBase("get-use-scenario-by-pro-group-id", "&pro_group_id=" . $proGroupID);
    }

    /**
     * 通过场景ID，查询使用的方案名称
     * @author  zmy
     * @version 2017-07-14
     * @param   [type]     $scenarioID [description]
     * @return  [type]                 [description]
     */
    public static function getUseProgramByScenarioId($scenarioID)
    {
        return self::getBase("get-use-program-by-scenario-id", "&scenario_id=" . $scenarioID);
    }

    /**
     * 通过策略ID 查询使用的场景名称
     * @author  zmy
     * @version 2017-07-14
     * @param   [type]     $strategyID [description]
     * @return  [type]                 [description]
     */
    public static function getUseScenarioByStrategyId($strategyID)
    {
        return self::getBase("get-use-scenario-by-strategy-id", "&strategy_id=" . $strategyID);
    }

    /**
     * 根据传输的data数据查询出需要 灯带饮品组的数据
     * @author  zmy
     * @version 2017-06-17
     * @param   [type]     $data     [传输的数据]
     * @param   integer    $pageSize [分页大小]
     * @param   integer    $page     [页数]
     * @return  [type]               [获取的数组]
     */
    public static function getLightBeltProGroupArr($data, $pageSize = 20, $page = 1)
    {
        return self::getBase("light-belt-pro-group-by-where", "&proGroupListJson=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 根据Id查询，灯带饮品组数据
     * @author  zmy
     * @version 2017-06-17
     * @param   [type]     $id [灯带饮品组ID]
     * @return  [type]         [1维数组]
     */
    public static function getLightBeltProductGroupById($id)
    {
        return self::getBase("select-light-belt-pro-group-by-id", "&id=" . $id);
    }

    /**
     * 获取饮品组数据 id=>name
     * @author  zmy
     * @version 2017-06-20
     * @return  [type]     [id=>name 数组]
     */
    public static function getProductGroupNameList()
    {
        return self::getBase("pro-group-name-list");
    }

    /**
     * 通过id 删除灯带策略数据
     * @author  zmy
     * @version 2017-06-17
     * @return  [type]     [true/false]
     */
    public static function getDelLightBeltStrategyById($id)
    {
        return self::getBase("del-light-belt-strategy-by-id", "&id=" . $id);
    }

    /**
     * 获取策略数据 id=>name
     * @author  zmy
     * @version 2017-06-21
     * @return  [type]     [id=>name 数组]
     */
    public static function getStrategyNameList()
    {
        return self::getBase("strategy-name-list");
    }

    /**
     * 根据传输的data数据查询出需要 灯带策略的数据
     * @author  zmy
     * @version 2017-06-20
     * @param   [type]     $data     [传输的数据]
     * @param   integer    $pageSize [分页大小]
     * @param   integer    $page     [页数]
     * @return  [type]               [获取的数组]
     */
    public static function getLightBeltStrategyArr($data, $pageSize = 20, $page = 1)
    {
        return self::getBase("light-belt-strategy-by-where", "&strategyListJson=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 根据Id查询，灯带策略数据
     * @author  zmy
     * @version 2017-06-17
     * @param   [type]     $id [灯带饮品组ID]
     * @return  [type]         [1维数组]
     */
    public static function getLightBeltStrategyById($id)
    {
        return self::getBase("select-light-belt-strategy-by-id", "&id=" . $id);
    }

    /**
     * 传输的添加策略
     * @author  zmy
     * @version 2017-06-20
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function saveLightBeltStrategy($data)
    {
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . 'save-light-belt-strategy' . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 根据传输的data数据查询出需要 灯带场景的数据
     * @author  zmy
     * @version 2017-06-21
     * @param   [type]     $data     [传输的数据]
     * @param   integer    $pageSize [分页大小]
     * @param   integer    $page     [页数]
     * @return  [type]               [获取的数组]
     */
    public static function getLightBeltScenarioArr($data, $pageSize = 20, $page = 1)
    {
        return self::getBase("light-belt-scenario-by-where", "&scenarioListJson=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 获取灯带场景数据，通过ID
     * @author  zmy
     * @version 2017-06-22
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public static function getLightBeltScenarioById($id)
    {
        return self::getBase("select-scenario-by-id", "&id=" . $id);
    }

    /**
     * 对灯带场景进行操作（添加、修改）
     * @author  zmy
     * @version 2017-06-22
     * @return  [type]     [description]
     */
    public static function saveLightBeltScenario($data)
    {
        return self::postBase("save-light-belt-scenario", $data);
    }

    /**
     * 删除灯带场景，通过ID
     * @author  zmy
     * @version 2017-06-22
     */
    public static function getDelLightBeltScenarioById($id)
    {
        return self::getBase("del-light-belt-scenario-by-id", "&id=" . $id);
    }

    /**
     * 添加方案时，查询出指定条件下的场景
     * @author  zmy
     * @version 2017-06-29
     * @param   [type]     $data [查询的条件]
     * @return  [type]           [返回的数组]
     */
    public static function getSpecifiedScenarioArr($data)
    {
        return self::getBase("get-specified-scenario", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 根据传输的data数据查询出需要 灯带方案
     * @author  zmy
     * @version 2017-06-25
     * @param   [type]     $data     [传输的数据]
     * @param   integer    $pageSize [分页大小]
     * @param   integer    $page     [页数]
     * @return  [type]               [获取的数组]
     */
    public static function getLightBeltProgramArr($data, $pageSize = 20, $page = 1)
    {
        return self::getBase("light-belt-program-by-where", "&programListJson=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 获取某方案下所有的楼宇（使用于方案详情页面）
     * @author  zmy
     * @version 2017-07-04
     * @param   [type]     $programID [方案ID]
     * @param   [type]     $data      [查询的条件]
     * @param   integer    $pageSize  [分页大小]
     * @param   integer    $page      [分页数]
     * @return  [type]                [楼宇数组 和 总数]
     */
    public static function getBuildInProgramWhere($programID, $data, $pageSize = 20, $page = 1)
    {
        return self::getBase("get-build-in-program-where", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&programID=' . $programID . '&pageSize=' . $pageSize . "&page=" . $page);

    }

    /**
     * 检测场景是否符合条件，不符条件的场景不可添加方案
     * @author  zmy
     * @version 2017-08-25
     * @param   [array] $data   [场景一维数组]
     * @return  [string]        [json]
     */
    public static function checkLightBeltScenario($data)
    {
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . 'check-light-belt-scenario' . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 存入/修改 灯带方案表
     * @author  zmy
     * @version 2017-06-25
     * @return  [type]     [description]
     */
    public static function saveLightBeltProgram($data)
    {
        return Tools::http_post(Yii::$app->params['coffeeUrl'] . 'save-light-belt-program' . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 通过id 删除灯带方案数据
     * @author  zmy
     * @version 2017-06-25
     * @return  [type]     [true/false]
     */
    public static function getDelLightBelProgramById($id)
    {
        return self::getBase("del-light-belt-program-by-id", "&id=" . $id);
    }

    /**
     * 获取灯带方案数据，通过ID
     * @author  zmy
     * @version 2017-06-29
     * @param   [type]     $id [方案ID]
     * @return  [type]         [description]
     */
    public static function getLightBeltProgramById($id)
    {
        return self::getBase("select-program-by-id", "&id=" . $id);
    }

    /**
     * 存入/修改灯带楼宇表
     * @author  zmy
     * @version 2017-06-25
     * @return  [type]     [description]
     */
    public static function saveLightProgramAssoc($data)
    {
        return self::postBase("save-light-program-assoc", $data);
    }

    /**
     * 获取 查询的楼宇数据
     * @author  zmy
     * @version 2017-06-30
     * @param   [type]     $data     []
     * @param   [type]     $page     [分页页数]
     * @param   [type]     $pageSize [分页大小]
     * @param   [type]     $selectType [0/1 0--批量移除楼宇， 1--批量添加楼宇]
     * @return  [type]               [数据：id=》name]
     */
    public static function getSearchBuildByWhere($programID, $data, $page, $pageSize, $selectType)
    {
        return self::getBase("get-search-build-by-where", "&programID=" . $programID . "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page . "&selectType=" . $selectType);
    }

    /**
     * 删除灯带楼宇数据
     * @author  zmy
     * @version 2017-06-30
     * @return  [type]     [description]
     */
    public static function delLightProgramAssoc($data)
    {
        return self::getBase("del-light-program-assoc", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取 楼宇类型数组
     * @author  zmy
     * @version 2017-07-01
     * @return  [type]     [description]
     */
    public static function getBuildTypeList()
    {
        $buildTypeList = self::getBase("get-build-type-list");
        return !$buildTypeList ? [] : Json::decode($buildTypeList);
    }

    /**
     * 获取灯带方案中搜素 设备类型数组
     * @author  zmy
     * @version 2017-07-01
     * @return  [type]     [description]
     */
    public static function getEquipTypeList()
    {
        $equipTypeList = self::getBase("get-equip-type-list");
        if ($equipTypeList) {
            return Json::decode($equipTypeList);
        }
        return [];
    }

    /**
     * 获取灯带方案中搜素 根据类型查询是分公司还是代理商，分公司数组
     * 0、公司 1、代理商 2、合作商
     * @author  zmy
     * @version 2017-07-01
     * @return  [type]     [description]
     */
    public static function getOrgListByType($orgType)
    {
        return self::getBase("get-org-list-by-type", "&orgType=" . $orgType);
    }

    /**
     * 获取灯带方案楼宇数据
     * @author  zmy
     * @version 2017-07-03
     * @param   [type]     $data     [查询的数据格式]
     * ["buildName"]=> "楼宇测试"
     *   ["equipType"]=> "1" 设备类型
     *   ["branch"]=> "2"
     *   ["programName"]=> "1" 方案名称
     *   ['agent']  => 代理商
     *   ["partner"]=> "13" // 合作商
     *   ["scenarioName"]=> "1" 场景名称
     *   ["strategyName"]=> "1" 策略名称
     *   ["proGroupName"]=> "1" 饮品组名称
     * @param   [type]     $pageSize [分页大小]
     * @param   [type]     $page     [页数]
     * @return  [type]               [返回数据和总条数]
     */
    public static function getBuildProgramByWhere($data, $pageSize, $page)
    {
        return self::getBase("get-build-program-by-where", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 修改灯带方案版本号
     * @author  zmy
     * @version 2017-07-20
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public static function getUpdateVersionProgram($id)
    {
        return self::getBase("update-version", "&id=" . $id);
    }

    /**
     * 修改灯带方案 默认方案
     * @author  zmy
     * @version 2017-07-20
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public static function getUpdateDefaultProgram($id)
    {
        return self::getBase("update-default-program", "&id=" . $id);
    }

    /**
     * 获取灯带数据 id=>name
     * @author  zmy
     * @version 2017-09-22
     * @param   [string]     $isSelect [是否请选择]
     * @return  [Array]                [Array]
     */
    public static function getProgramNameList($isSelect)
    {
        return self::getBase("get-program-name-list", '&isSelect=' . $isSelect);
    }

    /**
     * 同步灯带方案关联表数据
     * @author  zmy
     * @version 2017-09-22
     * @param   [Array]     $programData  ['build_id'=>'', 'program_id'=>'']
     * @return  [boolen]           [true/flase]
     */
    public static function buildProgramSync($programData)
    {
        return self::postBase("build-program-sync", $programData);
    }

    /**
     * 通过楼宇编号，查询出方案ID、。
     * @author  zmy
     * @update  tuqiang
     * @version 2017-09-22
     * @param   [string]     $buildId [楼宇ID]
     * @return  [string]              [方案ID]
     */
    public static function getProgramIdByBuildId($buildNumber)
    {
        return self::getBase("get-program-id-by-build-id", '&buildNumber=' . $buildNumber);
    }

    /**
     * 获取城市优惠策略
     * @author  zgw
     * @version 2017-07-03
     * @return  array     城市优惠策略
     */
    public static function getCities()
    {
        $cities = self::getBase("get-cities");
        if ($cities) {
            return Json::decode($cities);
        }
        return [];
    }

    /**
     * 获取城市优惠策略
     * @author  zgw
     * @version 2017-07-03
     * @return  array     城市优惠策略
     */
    public static function getCityPreferentialStrategy($params)
    {
        $cityPreferentialStrategy = self::getBase('get-city-preferential-strategy', $params);
        return Json::decode($cityPreferentialStrategy, 1);
    }

    /**
     * 保存城市优惠策略
     * @author  zgw
     * @version 2017-07-03
     * @param   array     $data 要保存的数据
     * @return  int             保存结果 1-保存成功 0-保存失败
     */
    public static function saveCityPreferentialStrategy($data)
    {
        return self::postBase("save-city-preferential-strategy", $data);
    }

    /**
     * 删除城市优惠策略
     * @author  zgw
     * @version 2017-07-05
     * @param   int     $id 城市优惠策略id
     * @return  boole        删除结果
     */
    public static function delCityPreferentialStrategy($id)
    {
        return self::postBase("del-city-preferential-strategy", ['id' => $id]);
    }

    //------------------------------------------------产品组新需求Api

    /**
     * 根据传输的data数据查询出需要 产品组料仓信息表的数据
     * @author  zmy
     * @version 2017-08-30
     * @param   [type]       $data     [传输的数据]
     * @param   [integer]    $pageSize [分页大小]
     * @param   [integer]    $page     [页数]
     * @return  [Array]                [获取的数组]
     */
    public static function getProGroupStockInfo($data, $pageSize = 20, $page = 1)
    {
        return self::getBase("pro-group-stock-info-by-where", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }

    /**
     * 保存产品组料仓信息
     * @author  zmy
     * @version 2017-08-30
     * @param   [Array]     $data  [数组]
     * @return  [boolen]           [true/false]
     */
    public static function saveProGroupStockInfo($action, $data)
    {
        $res = Tools::http_post(Yii::$app->params['coffeeUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
        return !empty($res) ? Json::decode($res) : [];
    }

    /**
     * 根据产品组料仓信息ID，查询产品组料仓数据
     * @author  zmy
     * @version 2017-08-30
     * @param   [type]     $proGroupStockInfoID [产品组料仓信息ID]
     * @return  [Array]                         [产品组料仓信息数组]
     */
    public static function getProGroupStockInfoByID($proGroupStockInfoID)
    {
        return self::getBase("get-pro-group-stock-info-by-id", "&id=" . $proGroupStockInfoID);
    }

    /**
     * 根据产品组料仓信息ID，删除产品组料仓数据
     * @author  zmy
     * @version 2017-08-30
     * @param   [type]     $proGroupStockInfoID [产品组料仓信息ID]
     * @return  [boolen]                        [true/false]
     */
    public static function delProGroupStockInfoByID($proGroupStockInfoID)
    {
        return self::getBase("del-pro-group-stock-info-by-id", "&id=" . $proGroupStockInfoID);
    }

    /**
     * 获取产品id和name的数组
     * @author  zgw
     * @version 2017-08-25
     * @return  array     单品名称列表
     */
    public static function getProductIDName()
    {
        $productIDNameList = self::getBase("get-product-names");
        if ($productIDNameList) {
            return Json::decode($productIDNameList);
        }
        return [];
    }
    /**
     * 获取楼宇id名称列表
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array      array(build_id => building)   楼宇id列表名称
     */
    public static function getBuildIdNameList()
    {
        $buildIdNameList = self::getBase("get-build-id-name-list");
        if ($buildIdNameList) {
            return Json::decode($buildIdNameList);
        }
        return [];
    }
    /**
     * 获取销售id名称列表
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array      array(sale_id => name)   楼宇id列表名称
     */
    public static function getSaleIdNameList()
    {
        $saleIdNameList = self::getBase("get-sale-id-name-list");
        if ($saleIdNameList) {
            return Json::decode($saleIdNameList);
        }
        return [];
    }
    /**
     * 根据查询条件获取销售表与楼宇关联的json数据对象;
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array       json数据对象
     * @param   array       查询条件
     */
    public static function getSaleBuildAssocJsonObj($data = array())
    {
        $saleJsonObj = self::postBaseGetData("get-sale-build-assoc-json-obj", $data, '&page=' . $data['page']);
        if ($saleJsonObj) {
            return Json::decode($saleJsonObj);
        }
        return [];
    }

    /**
     * 获取销售名称列表
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array       销售名称列表
     */
    public static function getSaleNameList()
    {
        $saleNameList = self::getBase("get-sale-name-list");
        if ($saleNameList) {
            return Json::decode($saleNameList);
        }
        return [];
    }

    /**
     * 获取销售列表
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array       销售列表
     */
    public static function getSaleAllList()
    {
        $saleAllList = self::getBase("get-sale-all-list");
        if ($saleAllList) {
            return Json::decode($saleAllList);
        }
        return [];
    }

    /**
     * 获取楼宇名称列表;
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array       楼宇名称列表
     */
    public static function getBuildNameList()
    {
        $buildNameList = self::getBase("get-build-name-list");
        if ($buildNameList) {
            return Json::decode($buildNameList);
        }
        return [];
    }

    /**
     * 验证是否已经存在销售人员的二维码信息;
     * @author  tuqinag
     * @version 2017-09-04
     * @return  array       二维码信息
     */
    public static function createSaleBuildingInfoVerify($data = array())
    {
        $saleBuildingInfo = self::postBaseGetData("create-sale-building-info-verify", $data);
        if ($saleBuildingInfo) {
            return Json::decode($saleBuildingInfo);
        }
        return [];
    }

    /**
     * 添加 销售与楼宇的邀请链接所生成的二维码图片;
     * @author  tuqinag
     * @version 2017-09-04
     * @return  boolean true/false;
     */
    public static function saleBuildingAssocCreate($params)
    {
        return self::postBase("sale-building-assoc-create", $params);
    }

    /**
     * 添加 销售与楼宇的邀请链接所生成的二维码图片;
     * @author  tuqinag
     * @version 2017-09-04
     * @return  boolean true/false;
     */
    public static function saleBuildingAssocDelete($params)
    {
        return self::postBase("sale-building-assoc-delete", $params);
    }

    /**
     * 根据查询条件获取优惠与楼宇关联的json数据;
     * @author  tuqinag
     * @version 2017-09-07
     * @return  array       优惠与楼宇的统计数据
     * @param   array $data 查询条件
     */
    public static function getDisBuildAssocStatisList($data = array())
    {
        $discountHolicyJsonObj = self::postBaseGetData("get-dis-build-assoc-statis-list", $data, '&page=' . $data['page']);
        if ($discountHolicyJsonObj) {
            return Json::decode($discountHolicyJsonObj);
        }
        return [];
    }

    /**
     * 根据优惠策略条件获取楼宇数据
     * @author  tuqiang
     * @version 2017-09-08
     * @param   array     $data
     * @param   integer   $page     分页数
     * @param   integer   $pageSize 分页大小
     * @return  array               [数据：id=> name]
     */
    public static function getDiscountBuildingListByWhere($data, $page, $pageSize)
    {
        return self::getBase("get-discount-building-list-by-where", "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
    }
    /**
     * 根据支付方式id获取对应的优惠列表;
     * @author  tuqinag
     * @version 2017-09-07
     * @return  array       符合条件的优惠策略数据
     * @param   array $data 查询条件
     */
    public static function getDisHolicyPaymentList($data = array())
    {
        $discountHolicyList = self::postBaseGetData("get-dis-holicy-payment-list", $data);
        if ($discountHolicyList) {
            return Json::decode($discountHolicyList);
        }
        return [];
    }

    /**
     * 优惠策略楼宇添加;
     * @author  tuqinag
     * @version 2017-09-07
     * @return  boolean     trueue/false true添加成功 false 添加失败
     * @param   array       添加详情
     */
    public static function disBuildingAssocCreate($data = array())
    {
        return self::postBase("dis-building-assoc-create", $data);
    }

    /**
     * 根据楼宇名称获取楼宇id
     * @author  tuqinag
     * @version 2017-09-11
     * @return  array       楼宇id列表
     * @param   array       楼宇名称列表
     */
    public static function getBuilidingIDList($data = array())
    {
        $builldingIDList = self::postBaseGetData("get-builiding-id-list", $data);
        if ($builldingIDList) {
            return Json::decode($builldingIDList);
        }
        return [];
    }

    /**
     * 获取销售人员列表
     * @author  tuqinag
     * @version 2017-09-11
     * @return  array       楼宇id列表
     * @param   array       楼宇名称列表
     */
    public static function getSaleList($params = array())
    {
        $saleList = self::postBaseGetData("get-sale-list", $params, '&page=' . $params['page']);
        if ($saleList) {
            return Json::decode($saleList);
        }
        return [];
    }
    /**
     * 新增销售
     * @author  tuqinag
     * @version 2017-09-11
     * @return  boolean     true 添加成功 false 添加失败
     * @param   array       销售人员详情
     */
    public static function saleCreate($data = array())
    {

        if (self::postBase("sale-create", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取销售人员详情
     * @author  tuqinag
     * @version 2017-09-11
     * @return  array       销售详情
     * @param   integer       销售id
     */
    public static function getSaleInfo($params)
    {
        $params = self::postBaseGetData("get-sale-info", $params);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 新增销售
     * @author  tuqinag
     * @version 2017-09-11
     * @return  boolean     true 修改成功 false 修改失败
     * @param   array       销售人员详情
     */
    public static function saleUpdate($data = array())
    {
        if (self::postBase("sale-update", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 删除
     * @author  tuqinag
     * @version 2017-09-11
     * @return  boolean     true 删除成功 false 删除失败
     * @param   array       销售人员id
     */
    public static function saleDelete($data = array())
    {

        if (self::postBase("sale-delete", $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据id获取优惠策略相关楼宇
     * @author  tuqinag
     * @version 2017-09-12
     * @param   array     优惠id
     * @return  array     优惠楼宇列表
     */
    public static function getDisBuildingList($buildPayTypeId)
    {
        $disBuildingList = self::getBase("get-dis-building-list", '&buildPayTypeId=' . $buildPayTypeId);
        if ($disBuildingList) {
            return Json::decode($disBuildingList);
        }
        return [];
    }

    /**
     * 获取优惠策略详情
     * @author  tuqinag
     * @version 2017-09-04
     * @param   array      优惠id
     * @return  array      详情
     */
    public static function getHolicyInfo($data = array())
    {
        $disInfo = self::postBaseGetData("get-holicy-info", $data);
        if ($disInfo) {
            return Json::decode($disInfo);
        }
        return [];
    }

    /**
     * 根据优惠策略id获取楼宇详情数据
     * @author  tuqinag
     * @version 2017-09-04
     * @param   array      优惠id
     * @return  array      详情
     */
    public static function getDisBuildingEquipTypeList($data = array())
    {
        $disInfo = self::postBaseGetData("get-dis-building-equip-type-list", $data);
        if ($disInfo) {
            return Json::decode($disInfo);
        }
        return [];
    }

    /**
     * 获取优惠列表
     * @author  tuqinag
     * @version 2017-09-04
     * @param   array      优惠id
     * @return  array      详情
     */
    public static function getDiscountList($data = array())
    {
        $disList = self::postBaseGetData("get-discount-list", $data, '&page=' . $data['page']);
        if ($disList) {
            return Json::decode($disList);
        }
        return [];
    }
    /**
     *  优惠策略添加验证
     *  @author     tuqiang
     *  @version    2017-09-15
     *  @param      array   要添加的策略类型
     *  @return     boolean true 已存在 false 不存在
     */
    public static function verifyDiscountHolicyCreate($data = array())
    {

        if (self::postBase("verify-discount-holicy-create", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *  优惠策略添加
     *  @author     tuqiang
     *  @version    2017-09-15
     *  @param      array   要添加的策略类型
     *  @return     boolean true 已存在 false 不存在
     */
    public static function discountHolicyCreate($data = array())
    {

        if (self::postBase("discount-holicy-create", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *  优惠策略修改验证
     *  @author     tuqiang
     *  @version    2017-09-15
     *  @param      array   要添加的策略类型
     *  @return     boolean true 已存在 false 不存在
     */
    public static function verifyDiscountHolicyUpdate($data = array())
    {

        if (self::postBase("verify-discount-holicy-update", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *  优惠策略修改
     *  @author     tuqiang
     *  @version    2017-09-15
     *  @param      array   要添加的策略信息
     *  @return     boolean true 已存在 false 不存在
     */
    public static function discountHolicyUpdate($data = array())
    {

        if (self::postBase("discount-holicy-update", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取人员详情
     * @author  tuqinag
     * @version 2017-09-15
     * @return  array       销售详情
     * @param   integer       销售id
     */
    public static function getDiscountHolicyInfo($params)
    {
        $params = self::postBaseGetData("get-discount-holicy-info", $params);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     *  优惠策略删除
     *  @author     tuqiang
     *  @version    2017-09-15
     *  @param      array   策略id
     *  @return     boolean true 成功 false 失败
     */
    public static function discountHolicyDelete($data = array())
    {
        if (self::postBase("discount-holicy-delete", $data)) {
            return true;
        } else {
            return false;
        }
    }
    public static function discountBuildingAssocDelete($data = array())
    {
        if (self::postBase("discount-building-assoc-delete", $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 销售删除验证
     * @author  tuqinag
     * @version 2017-09-13
     * @return  boolean     true 删除成功 false 删除失败
     * @param   array       销售人员id
     */
    public static function verifySaleDelete($data = array())
    {
        if (self::postBase("verify-sale-delete", $data)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 零售人员添加验证
     * @author  tuqinag
     * @version 2017-09-13
     * @return  boolean     true 删除成功 false 删除失败
     * @param   array       销售人员id
     */
    public static function verifySaleCreate($data = array())
    {
        $params = self::postBaseGetData("verify-sale-create", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 零售人员修改验证
     * @author  tuqinag
     * @version 2017-09-13
     * @return  boolean     true 删除成功 false 删除失败
     * @param   array       销售人员id
     */
    public static function verifySaleUpdate($data = array())
    {
        $params = self::postBaseGetData("verify-sale-update", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 根据楼宇id删除支付渠道楼宇相应的数据
     * @author  tuqinag
     * @version 2017-09-25
     * @param   array       楼宇id
     * @return  boolean     true 删除成功 false 删除失败
     */
    public static function discountBuildingAssocDeleteByEquip($data = array())
    {
        return self::postBase("discount-building-assoc-delete-by-equip", $data);
    }
    /**
     * erp获取单个机构详情
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array array('orgId' => 'org_name')
     * @return  array 机构详情
     */
    public static function getOrgDetailsModel($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-details-model", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * erp获取机构符合条件的IDName数组
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array 查询条件
     * @return  array 机构详情
     */
    public static function getOrgIdNameArray($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-id-name-array", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取符合条件的机构id数组
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array $data  array("parent_path" => 1);  查询条件
     * @return  array array(1,2,3,4,5);                  返回机构ID数组
     */
    public static function getOrgIdArray($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-id-array", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取符合条件的机构IdName数组
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array $data  array("parent_path" => 1);  查询条件
     * @return  array array(org_id => $org_name);        返回机构IdName数组
     */
    public static function getOrgIdNameListReturnErp($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-id-name-list-return-erp", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取符合条件的机构Name
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array $data  array("parent_path" => 1);  查询条件
     * @return  array array(org_name);                   返回机构name
     */
    public static function getOrgNameReturnErp($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-name-return-erp", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取机构名称列表
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array $data array("org_id" => 1);  查询条件
     * @return  array array(org_name);             返回机构name列表
     */
    public static function getOrgNameList($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-name-list", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * erp机构管理搜索数据
     * @author  tuqiang
     * @version 2017-09-29
     * @param   array $data array("org_id" => 1);  查询条件
     * @return  array array(org_name);             返回机构name列表
     */
    public static function getSearchOrgErp($data = array())
    {
        $params = self::postBaseGetOrgData("get-search-org-erp", $data, '&page=' . $data['page']);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 验证机构名称是否已经存在
     * @author  tuqiang
     * @version 2017-10-09
     * @param   array array('org_name' => '机构名称') 验证名称
     * @return  boolean true/false 存在/不存在
     */
    public static function verifyOrgCreate($data = array())
    {
        $params = self::postBaseGetOrgData("verify-org-create", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 验证机构名称是否已经存在 修改
     * @author  tuqiang
     * @version 2017-10-09
     * @param   array array('org_name' => '机构名称','org_id' => 1) 验证名称
     * @return  boolean true/false 存在/不存在
     */
    public static function verifyOrgUpdate($data = array())
    {
        $params = self::postBaseGetOrgData("verify-org-update", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 同步添加到智能平台
     * @author tuqiang
     * @version 2017-10-09
     * @param  array   $data        机构详情
     * @return boolean true/false   添加成功/添加失败
     */
    public static function syncErpAddOrg($data = array())
    {
        $params = self::postBaseGetOrgData("sync-erp-add-org", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 同步添加到智能平台 修改
     * @author tuqiang
     * @version 2017-10-09
     * @param  array   $data        机构详情
     * @return boolean true/false   修改成功/修改失败
     */
    public static function syncErpUpdateOrg($data = array())
    {
        $params = self::postBaseGetOrgData("sync-erp-update-org", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 根据条件获得机构id列表
     * @author   tuqiang
     * @version  2017-10-10
     * @param    array      $where  查询条件
     * @return   array      $data   返回数据
     */
    public static function getOrgByWhereIdList($data = array())
    {
        $params = self::postBaseGetOrgData("get-org-by-where-id-list", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     *  判断策略楼宇是否存在
     *  @author     tuqiang
     *  @version    2017-10-12
     *  @param      array   策略id
     *  @return     boolean true 成功 false 失败
     */
    public static function discountHolicyBuildingIsExistence($data = array())
    {
        $params = self::postBase("discount-holicy-building-is-existence", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     *  获取快速发券列表
     *  @author     tuqiang
     *  @version    2017-09-22
     *  @param      array       查询条件的
     *  @return     arrayy      快速发券列表
     */
    public static function quickSendCouponList($data = array())
    {
        $params = self::postBaseGetData("quick-send-coupon-list", $data, '&page=' . $data['page']);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     *  获取快速发券导出
     *  @author     tuqiang
     *  @version    2017-09-22
     *  @param      array       查询条件的
     *  @return     arrayy      快速发券列表
     */
    public static function quickSendCouponExport($data = array())
    {
        $params = self::postBaseGetData("quick-send-coupon-export", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取优惠券列表
     * @author  tuqiang
     * @version 2017-09-23
     * @param   array       优惠券种类id,优惠券单品/通用id
     * @return  array       优惠券列表
     */
    public static function getQuickSendCouponList($data)
    {
        $params = self::postBaseGetData("get-quick-send-coupon-list", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 判断当前添加的手机号是否存在，并且是否在黑名单中
     * @author  tuqiang
     * @version 2017-09-23
     * @param   $sendPhone  手机号
     * @return  array('code' => 0/1/2,'msg' => 'lalalal');
     */
    public static function verifyQuickSendCouponPhone($data)
    {
        $params = self::postBaseGetData("verify-quick-send-coupon-phone", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取套餐列表
     * @author  tuqiang
     * @version 2017-09-25
     * @return  array(); 优惠券列表
     */
    public static function getCouponGroupList()
    {
        $params = self::getBase("get-coupon-group-list");
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 获取有效的套餐列表
     * @author  wbq
     * @version 2018-6-5
     * @return  array(); 优惠券列表
     */
    public static function getCouponGroupValidList()
    {
        $params = self::getBase("get-coupon-group-valid-list");
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 客服快速发送优惠券
     * @author  tuqiang
     * @version 2017-09-25
     * @param   array() 发送者信息
     * @return  boolean true/false 成功/失败
     */
    public static function quickSendCouponCreate($data)
    {
        $params = self::postBaseGetData("quick-send-coupon-create", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     *
     * @author  tuqiang
     * @version 2017-10-31
     * @param   int         $id  快速发券id
     * @return  array            快速发券信息
     */
    public static function getQuickSendCouponDetails($id)
    {
        $params = self::getBase("get-quick-send-coupon-details", '&id=' . $id);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 根据物料分类查询物料消耗数据
     * @author  tuqiang
     * @version 2017-11-20
     * @param   array      $data  查询条件
     * @return  array             物料消耗数据
     */
    public static function getMaterielDayList($data)
    {
        $params = self::postBaseGetData("get-materiel-day-list", $data, '&page=' . $data['page']);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     * 根据楼宇查询物料消耗数据
     * @author  tuqiang
     * @version 2017-11-20
     * @param   array      $data  查询条件
     * @return  array             物料消耗数据
     */
    public static function getMaterielDayBuildingList($data)
    {
        $params = self::postBaseGetData("get-materiel-day-building-list", $data, '&page=' . $data['page']);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     * 获取物料分类接口
     * @author   tuqiang
     * @version  2017-11-20
     * @return   array      物料分类数据  id=>name;
     */
    public static function getMaterialTypeList()
    {
        $params = self::getBase("get-material-type-list");
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     * 查询月表物料消耗
     * @author  tuqiang
     * @version 2017-11-20
     * @param   array      $data  查询条件
     * @return  array             物料消耗数据
     */
    public static function getMaterielMonthList($data)
    {
        $params = self::postBaseGetData("get-materiel-month-list", $data, '&page=' . $data['page']);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     * 查询月表物料消耗单个消耗数据
     * @author  tuqiang
     * @version 2017-11-20
     * @param   array      $data  查询条件    array(楼宇id,时间)
     * @return  array             物料消耗数据
     */
    public static function getMaterielMonthInfo($data)
    {
        $params = self::postBaseGetData("get-materiel-month-info", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 修改月表物料消耗单个消耗数据
     * @author  tuqiang
     * @version 2017-11-20
     * @param   array      $data  查询条件    array(楼宇id,时间,物料类型消耗数值)
     * @return  array             物料消耗数据
     */
    public static function saveMaterielMonthInfo($data)
    {
        $params = self::postBaseGetData("save-materiel-month-info", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 运维导出excel
     * @author  tuqiang
     * @version 2017-11-22
     * @param   array      $param    查询条件
     * @return  array      符合条件的物料数据
     */
    public static function getMaintainExcelMaterielDay($data)
    {
        $params = self::postBaseGetData("get-maintain-excel-materiel-day", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 楼宇导出excel
     * @author  tuqiang
     * @version 2017-11-22
     * @param   array      $param    查询条件
     * @return  array      符合条件的物料数据
     */
    public static function getBuildExcelMaterielDay($data)
    {
        $params = self::postBaseGetData("get-build-excel-materiel-day", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 按天查询物料消耗数据
     * @param   string $createAt 时间
     * @return  array            以时间维度查询的楼宇物料消耗
     */
    public static function getMaterielDayInfoByDate($data)
    {
        $params = self::postBaseGetData("get-materiel-day-info-by-date", $data);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }

    /**
     * 计算物料消耗
     * @author   tuqiang
     * @version  2017-11-28
     * @param    string     $equipmentCode  设备编号
     */
    public static function getMaterielInfo($equipmentCode)
    {
        return self::getBase("get-materiel-info", "&equipmentCode=" . $equipmentCode);
    }
    /**
     * 获取磨豆设置详情
     * @author   tuqinag
     * @version  2017-12-11
     * @return   array      磨豆详情数据
     */
    public static function getGrindInfo($id)
    {
        $params = self::getBase("get-grind-info", "&id=" . $id);
        if ($params) {
            return Json::decode($params);
        }
        return [];
    }
    /**
     * 预磨豆根据条件查询出楼宇
     * @author  tuqiang
     * @version 2017-12-12
     * @param   array      $data             [条件数组]
     * @param   integer    $selectType       [查询类型，0-添加，1-修改]
     * @param   integer    $page             [分页数]
     * @param   integer    $pageSize         [分页大小]
     * @return  [string]                     [json 楼宇数据]
     */
    public static function getGrindBuildList($data = [])
    {
        $buildList = self::postBaseGetData("get-grind-build-list", $data, '&page=' . $data['page']);
        if ($buildList) {
            return Json::decode($buildList);
        }
        return [];
    }
    /**
     * 预磨豆根据条件查询出楼宇
     * @author  tuqiang
     * @version 2017-12-12
     * @param   array      $data             [条件数组]
     * @return  [string]                     [json 楼宇数据]
     */
    public static function getAllBuildingInProductSourceGrind($data = [])
    {
        return self::postBaseGetData("get-all-building-in-product-source-grind", $data);
    }

    public static function getUpdateGrind($data)
    {
        return self::postBaseGetData("get-grind-update-info", $data);
    }

    public static function getBuildGrindBuilding($data)
    {
        $buildList = self::postBaseGetData("get-build-grind-building", $data, '&page=' . $data['page']);
        if ($buildList) {
            return Json::decode($buildList);
        }
        return [];
    }
    public static function getDeleteGrind($id)
    {
        return self::getBase("get-delete-grind", "&id=" . $id);
    }
    public static function getDeleteGrindBuild($equipmentCode)
    {
        return self::getBase("get-delete-grind-build", "&equipmentCode=" . $equipmentCode);
    }
    /**
     * 获取符合条件的清洗设置
     * @author  tuqiang
     * @version 2017-12-20
     * @param   array      $data             [条件数组]
     * @return  [string]                     [json 楼宇数据]
     */
    public static function getClearEquipList($data = [])
    {
        $ClearList = self::postBaseGetData("get-clear-equip-list", $data, '&page=' . $data['page']);
        if ($ClearList) {
            return Json::decode($ClearList);
        }
        return [];
    }
    /**
     * 获取清洗类型
     * @param  int   $type  列表类型(是否加请选择 1是 0 不是)
     * @return [type]                [description]
     */
    public static function getClearTypeList($type = 0)
    {
        $clearTypeList = self::getBase("get-clear-type-list", "&type=" . $type);
        if ($clearTypeList) {
            return Json::decode($clearTypeList);
        }
        return [];
    }
    /**
     * 根据id获取清洗数据
     * @param  int   $id    清洗类型主键ID
     * @return array        清洗数组
     */
    public static function getClearEquipInfo($id = 0)
    {
        $info = self::getBase("get-clear-equip-info", "&id=" . $id);
        if ($info) {
            return Json::decode($info);
        }
        return [];
    }

    /**
     * 修改清洗设置数据
     * @param  array $params 清洗设置参数
     * @return boolean  true/false 成功/失败
     */
    public static function saveClearEquipInfo($params)
    {
        $data = self::postBase("save-clear-equip-info", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }

    /**
     * 添加清洗类型设置验证
     * @param  array $params 验证参数
     * @return boolean  true/false 成功/失败
     */
    public static function verifyClearEquipCreate($params)
    {
        $data = self::postBaseGetData("verify-clear-equip-create", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 添加清洗类型设置数据
     * @param  array $params 验证参数
     * @return boolean  true/false 成功/失败
     */
    public static function createClearEquipInfo($params)
    {
        $data = self::postBaseGetData("create-clear-equip-info", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 删除清洗类型设置数据
     * @param  array $params 验证参数
     * @return boolean  true/false 成功/失败
     */
    public static function deleteClearEquipInfo($id)
    {
        $data = self::getBase("delete-clear-equip-info", "&id=" . $id);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 获取符合条件的料盒数据
     * @author  tuqiang
     * @version 2017-12-20
     * @param   array      $data             [条件数组]
     * @return  [string]                     [json 楼宇数据]
     */
    public static function getMaterielBoxSpeedList($data = [])
    {
        $materielBoxSpeedList = self::postBaseGetData("get-materiel-box-speed-list", $data, '&page=' . $data['page']);
        if ($materielBoxSpeedList) {
            return Json::decode($materielBoxSpeedList);
        }
        return [];
    }
    /**
     * 根据id获取数据
     * @param  int   $id    料盒类型主键ID
     * @return array        料盒数组
     */
    public static function getMaterielBoxSpeedInfo($id = 0)
    {
        $info = self::getBase("get-materiel-box-speed-info", "&id=" . $id);
        if ($info) {
            return Json::decode($info);
        }
        return [];
    }

    /**
     * 修改指定料盒数据
     * @param  array $params       料盒数据
     * @return boolean  true/false 成功/失败
     */
    public static function saveMaterielBoxSpeedInfo($params)
    {
        $data = self::postBase("save-materiel-box-speed-info", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 添加料盒类型设置验证
     * @param  array $params 验证参数
     * @return boolean  true/false 成功/失败
     */
    public static function verifyMaterielBoxSpeedCreate($params)
    {
        $data = self::postBaseGetData("verify-materiel-box-speed-create", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 添加料盒类型设置数据
     * @param  array $params 料盒参数
     * @return boolean  true/false 成功/失败
     */
    public static function createMaterielBoxSpeedInfo($params)
    {
        $data = self::postBaseGetData("create-materiel-box-speed-info", $params);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }

    /**
     * 删除料盒类型设置数据
     * @param  array $params 验证参数
     * @return boolean  true/false 成功/失败
     */
    public static function deleteMaterielBoxSpeedInfo($id)
    {
        $data = self::getBase("delete-materiel-box-speed-info", "&id=" . $id);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 获取物料消耗记录列表
     */
    public static function getMaterielLogList($data)
    {
        $materielLogList = self::postBaseGetData("get-materiel-log-list", $data, '&page=' . $data['page']);
        if ($materielLogList) {
            return Json::decode($materielLogList);
        }
        return [];
    }

    /**
     * 获取预磨豆设置列表
     */
    public static function getGrindList($data)
    {
        $grindList = self::postBaseGetData("get-grind-list", $data, '&page=' . $data['page']);
        if ($grindList) {
            return Json::decode($grindList);
        }
        return [];
    }

    public static function createGrindInfo($data)
    {
        $result = self::postBaseGetData("create-grind-info", $data);
        if ($result) {
            return Json::decode($result);
        }
        return [];
    }

    public static function updateGrindInfo($data)
    {
        $result = self::postBaseGetData("update-grind-info", $data);
        if ($result) {
            return Json::decode($result);
        }
        return [];
    }

    /**
     * 获取公司ID=》name数组
     * @author  zmy
     * @version 2018-01-12
     * @return  [type]     [description]
     */
    public static function getIdToCompanysNameList()
    {
        $data = self::getBase("get-id-to-companys-name-list");
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }

    /**
     * 获取用户筛选任务的ID=》name数组
     * @author  zmy
     * @version 2018-01-12
     * @param   Array       $list
     * @param   string      $isSelect 是否选中请选择
     * @return  [Array]      [数组]
     */
    public static function getUserSelectionTaskIdToNameList($list = [], $isSelect = 0)
    {
        $list = Json::encode($list);
        $data = self::getBase("get-user-selection-task-id-to-name-list", "&is_select=" . $isSelect . '&data=' . $list);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }

    /**
     * 获取所有的分公司城市数组
     * @author  zmy
     * @version 2018-01-23
     * @param   integer    $isSelect [是否加请选择]
     * @return  [Array]               [数组]
     */
    public static function getOrgCityList($isSelect = 0)
    {
        $data = self::getBase("get-org-city-list", "&is_select=" . $isSelect);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }
    /**
     * 获取机构数组
     * @author  wangxiwen
     * @version 2018-08-08
     * @return  [Array]               [数组]
     */
    public static function getOrgMechanismList()
    {
        $data = self::getBase("get-org-mechanism-list");
        return !empty($data) ? Json::decode($data) : [];
    }
    public static function searchUpdateBuild($id)
    {
        return self::getBase("search-update-build", '&id=' . $id);
    }
    /**
     * 同步楼宇负责人
     * @param  integer $distributionUserID 楼宇负责人
     * @param  array   $buildingList       楼宇列表
     * @return boole                       true-成功 false-失败
     */
    public static function buildingUser($distributionUserID, $buildingList)
    {
        return self::postBase("building-user", ['distributionUserID' => $distributionUserID, 'buildingList' => $buildingList]);
    }

    /**
     * 发送短信
     * @param string $tel 电话号码
     * @param
     */
    public static function sendTel($tel)
    {
        return self::getBase("send-tel", "&tel=" . $tel);
    }

    /**
     * 根据设备编号获得设备产品分组料仓信息
     * @author sulingling
     * @version 2018-06-08
     * @param $equipmentCode string 设备编号
     * @param $stockCode string 料仓编号
     * @return array() | boolean
     */
    public static function getEquipmentProductGroupStock($equipmentCode = '')
    {
        return self::getBase("equipment-product-group-stock", "&equipment_code=" . $equipmentCode);
    }

    /**
     * 远程开门获取socket服务器域名
     * @author wangxiwen
     * @version 2018-11-01
     * @return
     */
    public static function getSocketServer($euipCode)
    {
        $socketServer = Tools::http_get(Yii::$app->params['fcoffeeUrl'] . 'service-api/get-socket-server' . self::verifyString() . '&equipCode=' . $euipCode);
        return empty($socketServer) ? [] : Json::decode($socketServer);
    }

    /* 发布产品组料仓
     * @author  wangxiwen
     * @version 2018-12-10
     * @param   [int]     $stockInfoId [产品组料仓信息ID]
     * @return  [boolen]  [true/false]
     */
    public static function releaseProductGroupStock($stockInfoId)
    {
        return self::getBase("release-product-group-stock", "&id=" . $stockInfoId);
    }
}
