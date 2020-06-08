<?php
namespace backend\controllers;

use backend\models\DistributionDailyTask;
use backend\models\EquipConsumeMaterial;
use backend\models\EquipLog;
use backend\models\EquipMaterialStockAssoc;
use backend\models\EquipRfidCard;
use backend\models\EquipSurplusMaterial;
use backend\models\EquipVersion;
use backend\models\EquipWarn;
use backend\models\ProductGroupStockInfo;
use backend\models\ProductMaterialStockAssoc;
use common\models\Api;
use common\models\EquipDeliveryRecord;
use common\models\Equipments;
use common\models\SendNotice;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 日志测试
     * @author  zgw
     * @version 2016-11-04
     * @return  [type]     [description]
     */
    public function actionIndex()
    {
        $phpLog = Yii::$app->cache->get('log');
        print_r($phpLog);
    }

    /**
     * 同步门禁卡号 为 正常，全国，无绑定人
     * @author  zmy
     * @version 2016-12-15
     * @return  [type]     [description]
     */
    public function actionSyncRfid()
    {
        $endI = Yii::$app->request->get("endI");
        if (!$endI) {
            echo "-1";exit();
        }
        $rfidCardObj = EquipRfidCard::find()->orderBy("id DESC")->one();
        if ($rfidCardObj) {
            $startI = $rfidCardObj->id;
        } else {
            $startI = 1;
        }
        for ($i = $startI; $i <= $endI + $startI; $i++) {
            $model = new EquipRfidCard();
            if ($i <= 9) {
                $model->rfid_card_code = '00000' . $i;
            } else {
                $model->rfid_card_code = '0000' . $i;
            }
            $model->rfid_card_pass = md5(111111);
            $model->create_time    = time();
            $model->org_id         = 1;
            $model->area_type      = 1;
            $model->rfid_state     = 0;
            $model->save();
        }
        echo "1";exit();
    }

    /**
     * 异常发送通知接口
     * @return [type] [description]
     *  $data = '{"equip_code":"0010100","equip_status":"1","log_type":"1","content":{"0101001":"行程开关左故障", "0101002":"热胆温度温度底"}}';
     */
    public function actionLogApi()
    {
        // 加密验证
        $key          = Yii::$app->request->get("key");
        $secretString = Yii::$app->request->get("secret");
        $verifyRs     = Api::verifyService($key, $secretString);
        if (!$verifyRs) {
            echo Json::encode(['status' => 1, 'msg' => '加密验证失败']);
            die;
        }
        // 获取数据
        $data = file_get_contents("php://input");
        //处理异常报警消息
        EquipWarn::callMessage($data);
    }

    /**
     * 设备和料仓关联表(1点)
     * @return [type] [description]
     */
    public function actionStockLimit()
    {
        $data = Api::stockLimit();
        $data = json_decode($data, true);
        if (EquipMaterialStockAssoc::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 产品组和料仓关联表(1点)
     * @author wangxl
     * @return [type] [description]
     */
    public function actionProductStockLimit()
    {
        $data = Api::stockLimit();
        $data = json_decode($data, true);

        if (ProductMaterialStockAssoc::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 同步产品组料仓信息
     * @author wangxiwen
     * @version 2018-09-10
     * @return boolean
     */
    public function actionSyncProductGroupStock()
    {
        $syncData = Json::decode(file_get_contents("php://input"));
        return ProductGroupStockInfo::syncProductGroupStockInfo($syncData);
    }

    /**
     * 获取楼宇投放记录
     * @author wangxiwen
     * @version 2018-09-27
     * @return json
     */
    public function actionGetBuildReleaseRecord()
    {
        $releaseRecordList = EquipDeliveryRecord::getBuildReleaseRecord();
        return Json::encode($releaseRecordList);
    }

    /**
     * 获取楼宇点位设备日志
     * @author wangxiwen
     * @version 2018-09-27
     * @return json
     */
    public function actionGetBuildEquipmentsLog()
    {
        $equipmentsLogList = EquipLog::getBuildEquipmentsLog();
        return Json::encode($equipmentsLogList);
    }

    /**
     * 同步设备中的产品组ID
     * @author  zmy
     * @version 2017-10-19
     * @return  [type]     [description]
     */
    public function actionSyncUpdateEquipmentsProGroup()
    {
        // 获取数据
        $data = json_decode(file_get_contents("php://input"), true);
        // ['buildIdList'=>$buildIdList, 'proGroupId'=>$proGroupID]
        $ret = Equipments::updateEquipmentsProGroup($data['buildIdList'], $data['proGroupId']);
        if ($ret) {
            echo "1";
        } else {
            echo "0";
        }

    }

    /**
     * 设备消耗物料接口(需要走定时任务，每天凌晨3点)
     * @return [type] [description]
     */
    public function actionConsumeMaterial()
    {
        $data = Api::consume();
        //$data = '{"1702000000":{"1":3,"2":2,"5":5,"15":0,"11":1,"8":4.7}}';
        $data = json_decode($data, true);
        if (EquipConsumeMaterial::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 设备剩余物料接口（需要走定时任务，每天18点）
     * @return [type] [description]
     */
    public function actionSurplusMaterial()
    {
        $data = Api::surplus();
        $data = json_decode($data, true);
        if (EquipSurplusMaterial::addAll($data)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 日常任务数据生成（18点10分）
     * @author  zgw
     * @version 2016-08-12
     * @return  [type]     [description]
     */
    public function actionCreateDailyTask()
    {
        if (DistributionDailyTask::addAll()) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 返回刷卡开门的接口 （ coffee后台调用——）
     * @author  zmy
     * @version 2016-12-12
     * @return  [type]     [description]
     */
    public function actionOpenRfidRes()
    {
        $equipmentCode = Yii::$app->request->get("eq");
        $cardCode      = Yii::$app->request->get("card");
        $endPassword   = Yii::$app->request->get("encpass");
        // 返回发送后获取的数据
        echo EquipRfidCard::retOpenRfidRes($equipmentCode, $cardCode, $endPassword);die();
    }
    /**
     * 计算商业运行设备总台数
     * @param $start 开始解绑时间
     * @param $end   结束解绑时间
     * @return string   设备编号
     */
    public function actionEquipDeliveryRecord($start, $end)
    {
        $equipDeliveryRecord = EquipDeliveryRecord::equipDeliveryRecord($start, $end);
        return Json::encode($equipDeliveryRecord);
    }
    public function actionGetMenu()
    {

        $topMenu = [];

        //智能零售
        $retail = [];
        //智能运维
        $operation = [];
        // 智能客服
        $service = [];
        // 供应链
        $supplier = [];
        //系统设置
        $sysconfig = [];

        /** 系统设置 */

        $menuArray = array();
        if (Yii::$app->user->can('系统设置列表')) {
            $menuArray[] = ['label' => '系统设置', 'url' => ['/sysconfig/index']];
        }

        if (Yii::$app->user->can('修改密码')) {
            $menuArray[] = ['label' => '修改密码', 'url' => ['/site/change-password']];
        }

        if (Yii::$app->user->can('角色管理')) {
            $menuArray[] = ['label' => '角色管理', 'url' => ['/role/index']];
        }

        if (Yii::$app->user->can('管理员管理')) {
            $menuArray[] = ['label' => '管理员管理', 'url' => ['/manager/index']];
        }

        if (Yii::$app->user->can('操作日志')) {
            $menuArray[] = ['label' => '操作日志', 'url' => ['/manager-log/index']];
        }

        if (Yii::$app->user->can('下载远程文件')) {
            $menuArray[] = ['label' => '下载远程文件', 'url' => ['/sysconfig/download-file']];
        }

        if (Yii::$app->user->can('系统设置')) {
            $sysconfig[] = array('label' => '系统设置',
                'items'                      => $menuArray,
            );
        }

        /** 供应链管理 */
        // 基础信息
        $menuArray = [];
        if (Yii::$app->user->can('供应商管理')) {
            $menuArray[] = ['label' => '供应商管理', 'url' => ['/scm-supplier/index']];
        }
        if (Yii::$app->user->can('物料分类管理')) {
            $menuArray[] = ['label' => '物料类别管理', 'url' => '/scm-material-type/index'];
        }
        if (Yii::$app->user->can('物料信息管理')) {
            $menuArray[] = ['label' => '物料信息管理', 'url' => '/scm-material/index'];
        }
        if (Yii::$app->user->can('库信息管理')) {
            $menuArray[] = ['label' => '库信息管理', 'url' => '/scm-warehouse/index'];
        }

        if (Yii::$app->user->can('供应链基础信息')) {
            $supplier[] = array('label' => '基础信息',
                'items'                     => $menuArray,
            );
        }

        // 入库管理
        $menuArray = [];
        if (Yii::$app->user->can('入库信息管理')) {
            $menuArray[] = ['label' => '入库信息管理', 'url' => '/scm-stock/index'];
        }

        if (Yii::$app->user->can('供应链入库管理')) {
            $supplier[] = array('label' => '入库管理',
                'items'                     => $menuArray,
            );
        }
        // 库存核算
        $menuArray = [];
        if (Yii::$app->user->can('库存信息管理')) {
            $menuArray[] = ['label' => '库存信息管理', 'url' => '/scm-total-inventory/index'];
        }

        if (Yii::$app->user->can('供应链库存核算')) {
            $supplier[] = array('label' => '库存核算',
                'items'                     => $menuArray,
            );
        }

        // 出库单管理
        $menuArray = [];
        if (Yii::$app->user->can('出库单管理')) {
            $menuArray[] = ['label' => '运维出库单', 'url' => '/out-statistics/index'];
        }
        // 预估单管理
        if (Yii::$app->user->can('预估单管理')) {
            $menuArray[] = ['label' => '运维预估单', 'url' => '/estimate-statistics/index'];
        }

        if (Yii::$app->user->can('供应链出库管理')) {
            $supplier[] = array('label' => '出库管理',
                'items'                     => $menuArray,
            );
        }

        // 报表信息
        $menuArray = [];
        if (Yii::$app->user->can('物料消耗预测')) {
            $menuArray[] = ['label' => '物料消耗预测', 'url' => '/distribution-filler/index'];
        }

        if (Yii::$app->user->can('物料分类消耗统计')) {
            $menuArray[] = ['label' => '物料分类消耗统计', 'url' => '/materiel-day/index'];
        }

        if (Yii::$app->user->can('物料楼宇消耗统计')) {
            $menuArray[] = ['label' => '物料楼宇消耗统计', 'url' => '/materiel-day/index-build'];
        }

        if (Yii::$app->user->can('物料消耗差异值管理')) {
            $menuArray[] = ['label' => '物料消耗差异值管理', 'url' => '/materiel-month/index'];
        }

        if (Yii::$app->user->can('物料消耗记录')) {
            $menuArray[] = ['label' => '物料消耗记录', 'url' => ['/materiel-log/index']];
        }

        if (Yii::$app->user->can('查看工厂模式操作日志')) {
            $menuArray[] = ['label' => '工厂模式操作日志', 'url' => ['/equip-back-log/index']];
        }

        if (Yii::$app->user->can('查看工厂模式物料消耗设置')) {
            $menuArray[] = ['label' => '工厂模式物料消耗设置', 'url' => ['/equip-back-consume-material-setup/index']];
        }

        if (Yii::$app->user->can('供应链报表信息')) {
            $supplier[] = array('label' => '报表信息',
                'items'                     => $menuArray,
            );
        }

        /** 智能客服 */
        // 智能客服菜单
        $menuArray = array();
        if (Yii::$app->user->can('类别管理')) {
            $menuArray[] = ['label' => '类别管理', 'url' => '/service-category/index'];
        }
        if (Yii::$app->user->can('问题管理')) {
            $menuArray[] = ['label' => '问题管理', 'url' => '/service-question/index'];
        }
        if (Yii::$app->user->can('统计管理')) {
            $menuArray[] = ['label' => '统计管理', 'url' => '/service-count/index'];
        }

        if (Yii::$app->user->can('自动回复')) {
            $service[] = array('label' => '自动回复',
                'items'                    => $menuArray,
            );
        }

        $menuArray = [];
        if (Yii::$app->user->can('电话搜索')) {
            $menuArray[] = ['label' => '电话搜索', 'url' => 'service/complaint/mobile-search'];
        }
        if (Yii::$app->user->can('点位搜索')) {
            $menuArray[] = ['label' => '点位搜索', 'url' => 'service/complaint/position-search'];
        }
        if (Yii::$app->user->can('客诉记录')) {
            $menuArray[] = ['label' => '客诉记录', 'url' => 'service/complaint/index'];
        }
        if (Yii::$app->user->can('客服系统')) {
            $service[] = ['label' => '客服系统', 'items' => $menuArray];
        }

        $menuArray = [];
        if (Yii::$app->user->can('咨询类型设置')) {
            $menuArray[] = ['label' => '咨询类型设置', 'url' => 'service/advisory-type/index'];
        }
        if (Yii::$app->user->can('问题类型设置')) {
            $menuArray[] = ['label' => '问题类型设置', 'url' => 'service/question-type/index'];
        }
        if (Yii::$app->user->can('协商方案设置')) {
            $menuArray[] = ['label' => '协商方案设置', 'url' => 'service/solution/index'];
        }
        if (Yii::$app->user->can('客服设置')) {
            $service[] = ['label' => '客服设置', 'items' => $menuArray];
        }
        /** 智能零售 */
        // 活动管理
        $menuArray = array();
        if (Yii::$app->user->can('城市优惠策略')) {
            $menuArray[] = ['label' => '城市优惠策略', 'url' => '/city-preferential-strategy/index'];
        }
        if (Yii::$app->user->can('查看支付方式')) {
            $menuArray[] = ['label' => '支付方式管理', 'url' => '/pay-type/index'];
        }
        if (Yii::$app->user->can('支付方式优惠策略')) {
            $menuArray[] = ['label' => '支付方式优惠策略', 'url' => '/discount-holicy/index'];
        }
        if (Yii::$app->user->can('楼宇支付策略')) {
            $menuArray[] = ['label' => '楼宇支付策略管理', 'url' => '/build-pay-type/index'];
        }
        if (Yii::$app->user->can('活动管理')) {
            $retail[] = array('label' => '活动管理',
                'items'                   => $menuArray,
            );
        }

        // 营销工具
        $menuArray = [];
        // 营销游戏管理
        if (Yii::$app->user->can('营销游戏管理')) {
            $menuArray[] = ['label' => '抽奖游戏管理', 'url' => '/activity/index'];
        }
        if (Yii::$app->user->can('活动提示语信息管理')) {
            $menuArray[] = ['label' => '活动提示语信息管理', 'url' => '/lottery-winning-hint/index'];
        }
        // 自组合套餐
        if (Yii::$app->user->can('自组合套餐活动管理')) {
            $menuArray[] = ['label' => '自组合套餐活动管理', 'url' => '/activity-combin-package-assoc/index'];
        }
        // 拼团活动管理
        if (Yii::$app->user->can('拼团活动管理')) {
            $menuArray[] = ['label' => '拼团活动管理', 'url' => '/group-activity/index'];
        }
        // 拉新活动设置
        if (Yii::$app->user->can('拉新活动设置')) {
            $menuArray[] = ['label' => '拉新活动设置', 'url' => '/laxin-activity-config/view'];
        }
        if (Yii::$app->user->can('拉新活动奖励列表')) {
            $menuArray[] = ['label' => '拉新活动奖励列表', 'url' => '/user-laxin-reward-record/share-reward'];
        }
        if (Yii::$app->user->can('拉新活动绑定用户列表')) {
            $menuArray[] = ['label' => '拉新活动绑定用户列表', 'url' => '/user-laxin-reward-record/index'];
        }
        if (Yii::$app->user->can('分享红包统计管理')) {
            $menuArray[] = ['label' => '分享红包统计管理', 'url' => '/share-red-packets-statistics/index'];
        }

        if (Yii::$app->user->can('领券活动')) {
            $menuArray[] = ['label' => '领券活动', 'url' => '/get-coupon-activity/index'];
        }
        if (Yii::$app->user->can('营销工具')) {
            $retail[] = array('label' => '营销工具',
                'items'                   => $menuArray,
            );
        }

        // 发券系统
        $menuArray = array();
        if (Yii::$app->user->can('黑白名单管理')) {
            $menuArray[] = ['label' => '黑白名单管理', 'url' => '/black-and-white-list/index'];
        }
        if (Yii::$app->user->can('快速发券')) {
            $menuArray[] = ['label' => '快速发券', 'url' => '/quick-send-coupon/index'];
        }
        if (Yii::$app->user->can('用户筛选任务管理')) {
            $menuArray[] = ['label' => '用户筛选', 'url' => '/user-selection-task/index'];
        }
        if (Yii::$app->user->can('发券管理')) {
            $menuArray[] = ['label' => '发券管理', 'url' => '/coupon-send-task/index'];
        }
        if (Yii::$app->user->can('发券任务统计')) {
            $menuArray[] = ['label' => '发券任务统计', 'url' => '/coupon-send-task-total-statistics/index'];
        }
        if (Yii::$app->user->can('发券系统')) {
            $retail[] = array('label' => '发券系统',
                'items'                   => $menuArray,
            );
        }
        // 周边商城
        $menuArray = array();
        if (Yii::$app->user->can('商品管理')) {
            $menuArray[] = ['label' => '商品管理', 'url' => '/shop-goods/index'];
        }

        if (Yii::$app->user->can('商品订单')) {
            $menuArray[] = ['label' => '订单管理', 'url' => '/shop-order/index'];
        }
        if (Yii::$app->user->can('周边商城')) {
            $retail[] = array('label' => '周边商城',
                'items'                   => $menuArray,
            );
        }
        // 订单系统
        $menuArray = array();
        if (Yii::$app->user->can('订单管理')) {
            $menuArray[] = ['label' => '订单管理', 'url' => '/order-info/index'];
        }
        if (Yii::$app->user->can('订单商品')) {
            $menuArray[] = ['label' => '订单商品', 'url' => '/order-goods/index'];
        }
        if (Yii::$app->user->can('退款记录')) {
            $menuArray[] = ['label' => '退款记录', 'url' => '/user-refund/index'];
        }
        if (Yii::$app->user->can('消费记录')) {
            $menuArray[] = ['label' => '消费记录', 'url' => '/user-consume/index'];
        }
        if (Yii::$app->user->can('商品汇总')) {
            $menuArray[] = ['label' => '商品汇总', 'url' => '/order-goods-count/index'];
        }
        if (Yii::$app->user->can('失败记录')) {
            $menuArray[] = ['label' => '失败记录', 'url' => '#'];
        }
        if (Yii::$app->user->can('咖豆充值')) {
            $menuArray[] = ['label' => '咖豆充值', 'url' => '#'];
        }
        if (Yii::$app->user->can('咖豆消费')) {
            $menuArray[] = ['label' => '咖豆消费', 'url' => '#'];
        }

        if (Yii::$app->user->can('订单系统')) {
            $retail[] = array('label' => '订单系统',
                'items'                   => $menuArray,
            );
        }
        // 日报系统
        $menuArray = array();
        if (Yii::$app->user->can('渠道日报')) {
            $menuArray[] = ['label' => '渠道日报', 'url' => '/consume-channel-daily/index'];
        }
        if (Yii::$app->user->can('日报总表')) {
            $menuArray[] = ['label' => '日报总表', 'url' => '/consume-daily-total/index'];
        }

        if (Yii::$app->user->can('日报报表')) {
            $retail[] = array('label' => '日报报表',
                'items'                   => $menuArray,
            );
        }
        // 周报系统
        $menuArray = array();
        if (Yii::$app->user->can('周报营收数据')) {
            $menuArray[] = ['label' => '周报营收数据', 'url' => '/weekly-revenue/index'];
        }
        if (Yii::$app->user->can('周报用户数据')) {
            $menuArray[] = ['label' => '周报用户数据', 'url' => '/weekly-user/index'];
        }
        if (Yii::$app->user->can('周报复购数据')) {
            $menuArray[] = ['label' => '周报复购数据', 'url' => '/weekly-return/index'];
        }
        if (Yii::$app->user->can('周报报表')) {
            $retail[] = array('label' => '周报报表',
                'items'                   => $menuArray,
            );
        }

        // 周报系统
        $menuArray = array();
        if (Yii::$app->user->can('月报营收数据')) {
            $menuArray[] = ['label' => '月报营收数据', 'url' => '/monthly-revenue/index'];
        }
        if (Yii::$app->user->can('月报用户数据')) {
            $menuArray[] = ['label' => '月报用户数据', 'url' => '/monthly-users/index'];
        }
        if (Yii::$app->user->can('月报报表')) {
            $retail[] = array('label' => '月报报表',
                'items'                   => $menuArray,
            );
        }
        // 外卖管理
        $menuArray = [];
        if (Yii::$app->user->can('外卖日报')) {
            $menuArray[] = ['label' => '外卖日报', 'url' => '/delivery-order/count'];
        }
        if (Yii::$app->user->can('外卖订单')) {
            $menuArray[] = ['label' => '外卖订单', 'url' => '/delivery-order/index'];
        }
        if (Yii::$app->user->can('配送人员管理')) {
            $menuArray[] = ['label' => '配送管理', 'url' => '/delivery-person/index'];
        }
        if (Yii::$app->user->can('配送区域管理')) {
            $menuArray[] = ['label' => '区域管理', 'url' => '/delivery-region/index'];
        }
        if (Yii::$app->user->can('外卖管理')) {
            $retail[] = array('label' => '外卖管理',
                'items'                   => $menuArray,
            );
        }
        /** 智能运维 */
        // 门禁卡管理
        $menuArray = array();
        if (Yii::$app->user->can('RFID门禁卡管理')) {
            $menuArray[] = ['label' => 'RFID门禁卡管理', 'url' => '/equip-rfid-card/index'];
        }
        if (Yii::$app->user->can('门禁卡特殊开门')) {
            $menuArray[] = ['label' => '门禁卡特殊开门', 'url' => '/special-permission/index'];
        }
        if (Yii::$app->user->can('门禁卡开门记录')) {
            $menuArray[] = ['label' => '门禁卡开门记录', 'url' => '/equip-rfid-card-record/index'];
        }
        if (Yii::$app->user->can('检测门禁卡开门')) {
            $menuArray[] = ['label' => '检测门禁卡开门', 'url' => '/equip-rfid-card/check-open-door'];
        }
        if (Yii::$app->user->can('申请临时开门记录')) {
            $menuArray[] = ['label' => '申请临时开门记录', 'url' => ['/temporary-authorization/index']];
        }
        if (Yii::$app->user->can('门禁卡管理')) {
            $operation[] = array('label' => '门禁卡管理',
                'items'                      => $menuArray,
            );
        }
        // 灯带管理
        $menuArray = array();
        if (Yii::$app->user->can('饮品组管理')) {
            $menuArray[] = ['label' => '饮品组管理', 'url' => '/light-belt-product-group/index'];
        }
        if (Yii::$app->user->can('灯带策略管理')) {
            $menuArray[] = ['label' => '灯带策略管理', 'url' => '/light-belt-strategy/index'];
        }
        if (Yii::$app->user->can('灯带场景管理')) {
            $menuArray[] = ['label' => '灯带场景管理', 'url' => '/light-belt-scenario/index'];
        }

        if (Yii::$app->user->can('灯带方案管理')) {
            $menuArray[] = ['label' => '灯带方案管理', 'url' => '/light-belt-program/index'];
        }

        if (Yii::$app->user->can('灯带管理')) {
            $operation[] = array('label' => '灯带管理',
                'items'                      => $menuArray,
            );
        }
        // 运维管理
        $menuArray = array();
        if (Yii::$app->user->can('运维人员管理')) {
            $menuArray[] = ['label' => '运维人员管理', 'url' => '/distribution-user/index'];
        }
        if (Yii::$app->user->can('配送分工')) {
            $menuArray[] = ['label' => '楼宇分工', 'url' => '/distribution-user/user-build'];
        }

        if (Yii::$app->user->can('运维任务管理')) {
            $menuArray[] = ['label' => '运维任务管理', 'url' => '/distribution-task/index'];
        }
        if (Yii::$app->user->can('运维任务统计管理')) {
            $menuArray[] = ['label' => '运维任务统计管理', 'url' => '/distribution-task/statistics'];
        }

        if (Yii::$app->user->can('配送通知管理')) {
            $menuArray[] = ['label' => '配送通知管理', 'url' => '/distribution-notice/index'];
        }
        if (Yii::$app->user->can('水单管理')) {
            $menuArray[] = ['label' => '水单管理', 'url' => '/distribution-water/index'];
        }

        if (Yii::$app->user->can('故障记录统计')) {
            $menuArray[] = ['label' => '故障记录统计', 'url' => '/equip-task/trouble-list'];
        }
        if (Yii::$app->user->can('料仓预警值管理')) {
            $menuArray[] = ['label' => '料仓预警值管理', 'url' => '/material-safe-value/index'];
        }
        if (Yii::$app->user->can('楼宇日常任务管理')) {
            $menuArray[] = ['label' => '楼宇日常任务管理', 'url' => '/building-task-setting/index'];
        }
        if (Yii::$app->user->can('楼宇点位统计管理')) {
            $menuArray[] = ['label' => '楼宇点位统计管理', 'url' => '/building-site-statistics/index'];
        }
        if (Yii::$app->user->can('公司设备类型日常任务管理')) {
            $menuArray[] = ['label' => '公司设备类型日常任务管理', 'url' => '/equipment-task-setting/index'];
        }

        if (Yii::$app->user->can('节假日管理')) {
            $menuArray[] = ['label' => '节假日管理', 'url' => '/holiday/holiday'];
        }

        if (Yii::$app->user->can('节假日不运维管理')) {
            $menuArray[] = ['label' => '节假日不运维管理', 'url' => '/holiday/index'];
        }
        if (Yii::$app->user->can('零售活动人员二维码管理')) {
            $menuArray[] = ['label' => '零售活动人员二维码管理', 'url' => '/sale-register-code/index'];
        }
        if (Yii::$app->user->can('零售活动人员管理')) {
            $menuArray[] = ['label' => '零售活动人员管理', 'url' => '/sale/index'];
        }

        if (Yii::$app->user->can('预磨豆设置')) {
            $menuArray[] = ['label' => '预磨豆设置', 'url' => ['/grind/index']];
        }

        if (Yii::$app->user->can('料盒速度列表')) {
            $menuArray[] = ['label' => '料盒速度列表', 'url' => ['/materiel-box-speed/index']];
        }

        if (Yii::$app->user->can('清洗设备类型列表')) {
            $menuArray[] = ['label' => '清洗设备类型列表', 'url' => ['/clear-equip/index']];
        }

        if (Yii::$app->user->can('运维管理')) {
            $operation[] = array('label' => '运维管理',
                'items'                      => $menuArray,
            );
        }
        // 设备管理
        $menuArray = array();
        if (Yii::$app->user->can('楼宇管理')) {
            $menuArray[] = ['label' => '楼宇管理', 'url' => '/building-record/index'];
        }
        if (Yii::$app->user->can('点位评估')) {
            $menuArray[] = ['label' => '点位评估', 'url' => '/point-evaluation/index'];
        }
        if (Yii::$app->user->can('点位管理')) {
            $menuArray[] = ['label' => '点位管理', 'url' => '/building/index'];
        }
        if (Yii::$app->user->can('点位助手')) {
            $menuArray[] = ['label' => '点位助手', 'url' => '/point-position/index'];
        }
        if (Yii::$app->user->can('点位申请')) {
            $menuArray[] = ['label' => '点位申请', 'url' => '/point-position/apply.html'];
        }
        if (Yii::$app->user->can('渠道类型')) {
            $menuArray[] = ['label' => '渠道类型', 'url' => '/build-type/index'];
        }
        if (Yii::$app->user->can('设备信息管理')) {
            $menuArray[] = ['label' => '设备信息管理', 'url' => '/equipments/index'];
        }
        if (Yii::$app->user->can('设备类型管理')) {
            $menuArray[] = ['label' => '设备类型管理', 'url' => '/scm-equip-type/index'];
        }

        if (Yii::$app->user->can('设备类型参数管理')) {
            $menuArray[] = ['label' => '设备类型参数管理', 'url' => '/equipment-type-parameter/index'];
        }

        if (Yii::$app->user->can('设备任务管理')) {
            $menuArray[] = ['label' => '设备任务管理', 'url' => '/equip-task/index'];
        }

        if (Yii::$app->user->can('销售投放管理')) {
            $menuArray[] = ['label' => '销售投放管理', 'url' => '/equip-delivery/index'];
        }

        if (Yii::$app->user->can('客服上报管理')) {
            $menuArray[] = ['label' => '客服上报管理', 'url' => '/equip-repair/index'];
        }

        if (Yii::$app->user->can('投放记录')) {
            $menuArray[] = ['label' => '投放记录', 'url' => '/equip-delivery-record/index'];
        }

        if (Yii::$app->user->can('异常报警设置')) {
            $menuArray[] = ['label' => '异常报警设置', 'url' => '/equip-warn/index'];
        }

        if (Yii::$app->user->can('故障现象管理')) {
            $menuArray[] = ['label' => '故障现象管理', 'url' => '/equip-symptom/index'];
        }
        if (Yii::$app->user->can('设备附件管理')) {
            $menuArray[] = ['label' => '设备附件管理', 'url' => '/equip-extra/index'];
        }
        if (Yii::$app->user->can('故障原因管理')) {
            $menuArray[] = ['label' => '故障原因管理', 'url' => '/equip-malfunction/index'];
        }

        if (Yii::$app->user->can('设备调试项管理')) {
            $menuArray[] = ['label' => '设备调试项管理', 'url' => '/equip-debug/index'];
        }

        if (Yii::$app->user->can('灯箱管理')) {
            $menuArray[] = ['label' => '灯箱管理', 'url' => '/equip-light-box/index'];
        }

        if (Yii::$app->user->can('异常报警发送记录')) {
            $menuArray[] = ['label' => '异常报警发送记录', 'url' => '/equip-abnormal-send-record/index'];
        }

        if (Yii::$app->user->can('投放商管理')) {
            $menuArray[] = ['label' => '投放商管理', 'url' => '/equip-trafficking-suppliers/index'];
        }

        if (Yii::$app->user->can('App版本号管理')) {
            $menuArray[] = ['label' => 'App版本号管理', 'url' => '/app-version-management/index'];
        }

        if (Yii::$app->user->can('设备版本信息管理')) {
            $menuArray[] = ['label' => '设备版本信息管理', 'url' => '/equip-version/index'];
        }

        if (Yii::$app->user->can('设备冲泡器时间管理')) {
            $menuArray[] = ['label' => '设备冲泡器时间管理', 'url' => '/equip-brew/index'];
        }
        if (Yii::$app->user->can('语音控制管理')) {
            $menuArray[] = ['label' => '语音控制管理', 'url' => ['/speech-control/index']];
        }

        if (Yii::$app->user->can('设备管理')) {
            $operation[] = array('label' => '设备管理',
                'items'                      => $menuArray,
            );
        }

        // 产品管理
        $menuArray = array();

        if (Yii::$app->user->can('产品上下架管理')) {
            $menuArray[] = ['label' => '产品上下架管理', 'url' => '/product-offline/index'];}

        if (Yii::$app->user->can('产品下架列表管理')) {
            $menuArray[] = ['label' => '产品下架列表管理', 'url' => '/product-line/index'];
        }
        if (Yii::$app->user->can('产品上下架记录管理')) {
            $menuArray[] = ['label' => '产品上下架记录管理', 'url' => '/product-offline-record/index'];
        }
        if (Yii::$app->user->can('产品组料仓信息管理')) {
            $menuArray[] = ['label' => '产品组料仓信息管理', 'url' => '/product-group-stock-info/index'];
        }
        if (Yii::$app->user->can('单品管理')) {
            $menuArray[] = ['label' => '单品管理', 'url' => '/coffee-product/index'];
        }

        if (Yii::$app->user->can('产品组管理')) {
            $menuArray[] = ['label' => '产品组管理', 'url' => '/equipment-product-group/index'];
        }
        if (Yii::$app->user->can('设备工序管理')) {
            $menuArray[] = ['label' => '设备工序管理', 'url' => '/equip-process/index'];
        }

        if (Yii::$app->user->can('进度条管理')) {
            $menuArray[] = ['label' => '进度条管理', 'url' => '/equip-type-progress-product-assoc/index'];
        }

        if (Yii::$app->user->can('设备端活动管理')) {
            $menuArray[] = ['label' => '设备端活动管理', 'url' => '/special-schedul/index'];
        }

        if (Yii::$app->user->can('产品标签管理')) {
            $menuArray[] = ['label' => '产品标签管理', 'url' => '/coffee-label/index'];
        }
        if (Yii::$app->user->can('咖语管理')) {
            $menuArray[] = ['label' => '咖语管理', 'url' => '/coffee-language/index'];
        }
        if (Yii::$app->user->can('成份管理')) {
            $menuArray[] = ['label' => '成份管理', 'url' => '/product-ingredient/index'];
        }
        if (Yii::$app->user->can('轻食产品上下架')) {
            $menuArray[] = ['label' => '轻食产品上下架管理', 'url' => '/light-food-product/change-product-status'];
        }
        if (Yii::$app->user->can('产品管理')) {
            $operation[] = array('label' => '产品管理',
                'items'                      => $menuArray,
            );
        }

        // 通讯录管理
        $menuArray = array();

        if (Yii::$app->user->can('部门管理')) {
            $menuArray[] = ['label' => '部门管理', 'url' => '/wx-department/index'];
        }

        if (Yii::$app->user->can('成员管理')) {
            $menuArray[] = ['label' => '成员管理', 'url' => '/wx-member/index'];
        }

        if (Yii::$app->user->can('标签管理')) {
            $menuArray[] = ['label' => '标签管理', 'url' => '/wx-tag/index'];
        }

        if (Yii::$app->user->can('机构管理')) {
            $menuArray[] = ['label' => '机构管理', 'url' => '/organization/index'];
        }

        if (Yii::$app->user->can('通讯录管理')) {
            $operation[] = array('label' => '通讯录管理',
                'items'                      => $menuArray,
            );
        }

        if (!empty($operation)) {
            $topMenu[] = array(
                'label' => '智能运维',
                'items' => $operation,
            );
        }
        if (!empty($retail)) {
            $topMenu[] = array(
                'label' => '智能零售',
                'items' => $retail,
            );
        }
        if (!empty($service)) {
            $topMenu[] = array(
                'label' => '智能客服',
                'items' => $service,
            );
        }
        if (!empty($supplier)) {
            $topMenu[] = array(
                'label' => '供应链',
                'items' => $supplier,
            );
        }

        if (!empty($sysconfig)) {
            $topMenu[] = array(
                'label' => '系统设置',
                'items' => $sysconfig,
            );
        }

        return Json::encode($topMenu);
    }

    public function actionBuildingLocation()
    {
        $dataString = file_get_contents("php://input");
        $data       = Json::decode($dataString, true);
        Equipments::saveEquipment($data);
    }

    /**
     *
     */
    public function actionVolume()
    {
        // 加密验证
        $key          = Yii::$app->request->get("key");
        $secretString = Yii::$app->request->get("secret");
        $verifyRs     = Api::verifyService($key, $secretString);
        if (!$verifyRs) {
            echo Json::encode(['status' => 1, 'msg' => '加密验证失败']);
            die;
        }
        // 获取数据
        $dataString = file_get_contents("php://input");
        $data       = Json::decode($dataString, true);
        //处理异常报警消息
        EquipSurplusMaterial::surplusMaterialUpdate($data);
    }

    public function actionSendGetOrderNotice()
    {
        // 加密验证
        $key          = Yii::$app->request->get("key");
        $secretString = Yii::$app->request->get("secret");
        $verifyRs     = Api::verifyService($key, $secretString);
        if (!$verifyRs) {
            return Json::encode(['status' => 1, 'msg' => '加密验证失败']);
        }
        // 获取数据
        $dataString = file_get_contents("php://input");
        $data       = Json::decode($dataString, true);
        if (!empty($data['userIdList'])) {
            $agentId = Yii::$app->params['delivery_agentid'];
            $url     = $data['url'] . '?agentId=' . $agentId;
            $text    = $data['msg'];
            foreach ($data['userIdList'] as $userId) {
                \common\models\SendNotice::sendWxNotice($userId, $url, $text, $agentId);
            }
        }
    }

    /**
     *  设备里面物料剩余值达到预警值的给设备的相关负责人发送消息
     * @author sulingling
     * @dateTime 2018-11-12
     * @version  [version]
     * @return   boolean     [true-成功 false-失败]
     */
    public function actionSendGetVolumeNotice()
    {
        // 加密验证
        $key          = Yii::$app->request->get("key");
        $secretString = Yii::$app->request->get("secret");
        $verifyRs     = Api::verifyService($key, $secretString);
        if (!$verifyRs) {
            return Json::encode(['status' => 1, 'msg' => '加密验证失败']);
        }
        // 获取数据
        $dataString = file_get_contents("php://input");
        $data       = Json::decode($dataString, true);
        return SendNotice::sendWxNotice($data['distributionUserid'], '', $data['stockString'], Yii::$app->params['equip_agentid']);
    }

    /**
     * 根据设备编号获取设备APP版本号
     * @author zhenggangwei
     * @date   2020-04-01
     * @return string
     */
    public function actionGetEquipVersion()
    {
        $equipCode = Yii::$app->request->get('equip_code');
        if (!$equipCode) {
            return '';
        }
        return EquipVersion::getEquipVersionByEquipCode($equipCode);
    }
}
