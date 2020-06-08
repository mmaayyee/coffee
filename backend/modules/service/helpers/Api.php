<?php
namespace backend\modules\service\helpers;

use common\helpers\multiRequest\MutiRequestHandler as Request;
use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 包装请求智能系统的http请求
 * @author wlw
 * @date   2018-09-13
 *
 */
class Api
{
    public static function createUrl($action, array $params = [])
    {
        $url      = Yii::$app->params['fcoffeeUrl'] . $action . '.html';
        $queryStr = [];
        foreach ($params as $name => $value) {
            if (is_null($value)) {
                continue;
            }
            $queryStr[] = "$name=$value";
        }
        $queryStr = implode('&', $queryStr);

        return $url . '?' . $queryStr;
    }

    /**
     * 获取所有咨询类型
     */
    public static function getAdvisoryTypes($is_show = null)
    {
        $url     = self::createUrl('erpapi/customer-service/advisory-types', ['is_show' => $is_show]);
        $handler = (new Request())->setGetHandle($url);

        return $handler;
    }

    public static function getAdvisoryType($id)
    {
        $url     = self::createUrl('erpapi/customer-service/advisory-type', ['id' => $id]);
        $handler = (new Request())->setGetHandle($url);
        return $handler;
    }
    /**
     * 修改咨询类型
     */
    public static function updateAdvisoryType($data)
    {
        $url     = self::createUrl('erpapi/customer-service/update-advisory-type');
        $handler = (new Request())->setPostHandle($url, $data);
        return $handler;
    }

    public static function addAdvisoryType($data)
    {
        $url     = self::createUrl('erpapi/customer-service/add-advisory-type');
        $handler = (new Request())->setPostHandle($url, $data);
        return $handler;
    }

    /**
     * 获取解决方案列表
     * @param unknown $isShow
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getSolutions($isShow = null)
    {
        $url     = self::createUrl('erpapi/customer-service/solutions', ['is_show' => $isShow]);
        $handler = (new Request())->setGetHandle($url);
        return $handler;
    }

    /**
     * 获取特定解决方案
     * @param unknown $id
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getSolution($id)
    {
        $url     = self::createUrl('erpapi/customer-service/solution', ['id' => $id]);
        $handler = (new Request())->setGetHandle($url);

        return $handler;
    }

    /**
     * 添加解决方案
     * @param unknown $data
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function addSolution($data)
    {
        $url     = self::createUrl('erpapi/customer-service/add-solution');
        $handler = (new Request())->setPostHandle($url, $data);

        return $handler;
    }

    /**
     * 编辑解决方案
     * @param unknown $data
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function updateSolution($data)
    {
        $url     = self::createUrl('erpapi/customer-service/update-solution');
        $handler = (new Request())->setPostHandle($url, $data);

        return $handler;
    }

    /**
     * 添加问题类型
     * @param unknown $data
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function addQuestionType($data)
    {
        $url     = self::createUrl('erpapi/customer-service/add-question-type');
        $handler = (new Request())->setPostHandle($url, $data);

        return $handler;
    }

    /**
     * 获取问题类型列表
     * @param unknown $isShow
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getQuestionTypes($isShow = null)
    {
        $url = self::createUrl('erpapi/customer-service/question-types', ['is_show' => $isShow]);
        return (new Request())->setGetHandle($url);
    }

    /**
     * 更新问题类型
     * @param unknown $data
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function updateQuestionType($data)
    {
        $url = self::createUrl('erpapi/customer-service/update-question-type');
        return (new Request())->setPostHandle($url, $data);
    }

    public static function getQuestionType($id)
    {
        $url = self::createUrl('erpapi/customer-service/question-type', ['id' => $id]);
        return (new Request())->setGetHandle($url);
    }

    /**
     * 根据手机手机号检索客诉信息
     * @param unknown $mobile
     */
    public static function mobileSearch($mobile)
    {
        $url = self::createUrl('erpapi/customer-service/mobile-search', ['mobile' => $mobile]);
        return (new Request())->setGetHandle($url);

    }

    public static function getUserOrderList($userId, $where = [], $limit = 3, $offset = 0)
    {
        $params = array_merge(['user_id' => $userId, 'limit' => $limit, 'offset' => $offset], $where);
        $url    = self::createUrl('erpapi/order/user-orders', $params);
        return (new Request())->setGetHandle($url);
    }

    public static function getUserConsumList($userId, $where = [], $limit = 3, $offset = 0)
    {
        $params = array_merge(['user_id' => $userId, 'limit' => $limit, 'offset' => $offset], $where);
        $url    = self::createUrl('erpapi/order/user-consums', $params);
        return (new Request())->setGetHandle($url);
    }
    public static function getUserComplaintList($userId, $where = [], $limit = 3, $offset = 0)
    {
        $params = array_merge(['user_id' => $userId, 'limit' => $limit, 'offset' => $offset], $where);
        $url    = self::createUrl('erpapi/order/user-complaint', $params);
        return (new Request())->setGetHandle($url);
    }
    public static function getOrderlistByCode($orderCode)
    {
        $params = array_merge(['order_code' => $orderCode]);
        $url    = self::createUrl('erpapi/order/get-order-info-code', $params);
        return (new Request())->setGetHandle($url);
    }
    public static function positionSearch($name, $number = '')
    {
        $url = self::createUrl('erpapi/position/search', ['name' => $name, 'number' => $number]);
        return (new Request())->setGetHandle($url);
    }

    public static function getPositionConsumList($buildId, $where = [], $limit = 3, $offset = 0)
    {
        $params = array_merge(['build_id' => $buildId, 'limit' => $limit, 'offset' => $offset], $where);
        $url    = self::createUrl('erpapi/position/consum-list', $params);
        return (new Request())->setGetHandle($url);
    }
    public static function getPositionComplaintList($buildId, $limit = 3, $offset = 0, $userID = '')
    {
        $url = self::createUrl('erpapi/position/complaint-list', [
            'build_id' => $buildId,
            'limit'    => $limit,
            'offset'   => $offset,
            'user_id'  => $userID,
        ]);
        return (new Request())->setGetHandle($url);
    }
    public static function getOrgName()
    {
        $url = self::createUrl('erpapi/organization/name-list', ['sign' => md5('SYQ7G5WO0X84')]);
        return (new Request())->setGetHandle($url);
    }

    /**
     * 获取所有支付方式
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getAllPayWay()
    {
        $url = self::createUrl('erpapi/order/get-all-pay-way');
        return (new Request())->setGetHandle($url);
    }

    public static function getBuildingList()
    {
        $url = self::createUrl('erpapi/position/building-list');
        return (new Request())->setGetHandle($url);
    }

    public static function GetBuildingEquipments()
    {
        $url = self::createUrl('erpapi/position/get-building-equipments');
        return (new Request())->setGetHandle($url);
    }

    public static function addComplaint($data)
    {
        $url     = self::createUrl('erpapi/customer-service/add-complaint');
        $handler = (new Request())->setPostHandle($url, $data);
        return $handler;
    }
    public static function getCustomerServiceSolutionList()
    {
        $url = self::createUrl('erpapi/position/get-customer-service-solution-list');
        return (new Request())->setGetHandle($url);
    }

    /**
     * 用户咖啡列表 接口
     * @param $userID
     * @return Request
     */
    public static function getUserCoffee($userID)
    {
        $url = self::createUrl('erpapi/customer-service/get-user-coffee', ['user_id' => $userID]);
        return (new Request())->setGetHandle($url);
    }
    /**
     * 用户优惠券列表 接口
     * @param $userID
     * @return Request
     */
    public static function getUserCoupon($userID)
    {
        $url = self::createUrl('erpapi/customer-service/get-user-coupon', ['user_id' => $userID]);
        return (new Request())->setGetHandle($url);
    }
    public static function getAdvisoryAndQuestionTypes()
    {
        $url     = self::createUrl('erpapi/customer-service/advisory-question-types');
        $handler = (new Request())->setGetHandle($url);
        return $handler;
    }
    public static function getSolutionList()
    {
        $url     = self::createUrl('erpapi/position/get-solution-list');
        $handler = (new Request())->setGetHandle($url);
        return $handler;
    }
    /**
     * 根据消费ID检索客诉信息
     * @param userConsumeID
     */
    public static function getCustomerComplaint($userConsumeID)
    {
        $url = self::createUrl('erpapi/customer-service/edit-complain', ['user_consume_id' => $userConsumeID]);
        return (new Request())->setGetHandle($url);
    }

    /**
     * 根据订单编号检索客诉信息
     * @param $orderCode
     */
    public static function getCustomerComplaintByOrderCode($orderCode)
    {
        $url = self::createUrl('erpapi/customer-service/edit-complain', ['order_code' => $orderCode]);
        return (new Request())->setGetHandle($url);
    }

    /**
     * 根据客诉编号检客诉信息
     * @param $orderCode
     */
    public static function getCustomerComplaintByID($complaintID)
    {
        $url = self::createUrl('erpapi/customer-service/edit-complain', ['complain_id' => $complaintID]);
        return (new Request())->setGetHandle($url);
    }
    public static function getProductList($orderCode)
    {
        $url = self::createUrl('erpapi/position/get-product-list', ['eq' => $orderCode]);
        return (new Request())->setGetHandle($url);
    }
    public static function getComplaintList($params, $page)
    {
        $data['CustomerServiceComplaintSearch'] = $params;
        $complaintList                          = self::postBase('erpapi/customer-service/get-complaint-list', $data, '?page=' . $page);
        return !$complaintList ? [] : Json::decode($complaintList);
    }

    public static function exportComplaintList($params)
    {
        $data['CustomerServiceComplaintSearch'] = $params;
        $complaintList                          = self::postBase('erpapi/customer-service/export-complaint', $data);
        return !$complaintList ? [] : Json::decode($complaintList);
    }
    public static function getComplaintInfo($complaintID)
    {
        $url = self::createUrl('erpapi/customer-service/get-complaint-info', ['complain_id' => $complaintID]);
        return (new Request())->setGetHandle($url);
    }
    public static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;
        // var_dump(Json::encode($data));exit();
        if ($params) {
            $params .= '&sign=' . md5('SYQ7G5WO0X84');
        } else {
            $params = '?sign=' . md5('SYQ7G5WO0X84');
        }
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }

    public static function getBase($action, $params)
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html?sign=' . md5('SYQ7G5WO0X84') . $params);
    }
    /**
     * 根据公司ID获取点位名称列表
     * @author zhenggangwei
     * @date   2020-01-04
     * @param  integer     $cid 公司ID
     * @return array
     */
    public static function getBuildNameByCid($cid)
    {
        $url = self::createUrl('erpapi/customer-service/get-build-name-by-cid', ['cid' => $cid]);
        return (new Request())->setGetHandle($url);
    }
}
