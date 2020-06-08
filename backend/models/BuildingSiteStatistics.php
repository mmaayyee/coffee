<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "building_site_statistics".
 *
 * @property int $id 楼宇点位统计自增ID
 * @property string $build_number 楼宇编号
 * @property string $operation_state 运营状态
 * @property int $sales_volumes 点位销售杯数
 * @property string $equipment_type 设备类型编码
 * @property string $operation_mode 运营模式
 * @property int $sales_amount 点位销售金额（以分为单位）
 * @property string $create_date 点位统计数据生成时间
 */
class BuildingSiteStatistics extends \yii\db\ActiveRecord
{
    /*定义楼宇点位统计表字段*/
    public $build_number;
    public $name;
    public $organization_type;
    public $org_name;
    public $org_city;
    public $create_time;
    public $un_bind_time;
    public $equipment_code;
    public $equipment_type_name;
    public $build_site_statistics;

    /*定义检索条件字段*/
    public $beginDate;
    public $endDate;
    public $operationBeginDate;
    public $operationEndDate;
    public $operationMode;
    public $equipmentType;
    public $organizationId;
    public $buildingName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building_site_statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_number', 'name', 'organization_type', 'org_name', 'org_city', 'create_time', 'un_bind_time', 'equipment_code', 'equipment_type_name', 'build_site_statistics'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'build_number'       => 'Build Number',
            'operation_state'    => 'Operation State',
            'sales_volumes'      => 'Sales Volumes',
            'equipment_type'     => 'Equipment Type',
            'operation_mode'     => 'Operation Mode',
            'sales_amount'       => 'Sales Amount',
            'create_date'        => 'Create Date',
            'beginDate'          => '统计时间(开始)',
            'endDate'            => '统计时间(结束)',
            'operationBeginDate' => '运营时间(开始)',
            'operationEndDate'   => '运营时间(结束)',
        ];
    }

    /**
     * 获取楼宇点位统计检索条件数据
     * @author wangxiwen
     * @version 2018-09-25
     * @return array
     */
    public static function getBuildSiteSearchData()
    {
        return self::getBase('building-site-statistics-api/build-site-search-data.html');
    }

    /**
     * 获取楼宇点位统计展示数据
     * @author wangxiwen
     * @version 2018-09-25
     * @param array $params 查询条件
     * @return
     */
    public static function getBuildSiteShowData($params)
    {
        $buildSiteStatistics = self::postBase('building-site-statistics-api/build-site-show-data.html', $params);
        return empty($buildSiteStatistics) ? [] : Json::decode($buildSiteStatistics);
    }

    /**
     * 获取楼宇点位统计导出数据
     * @author wangxiwen
     * @version 2018-09-25
     * @param array $params 查询条件
     * @return
     */
    public static function getBuildSiteExportData($params)
    {
        $buildSiteStatistics = self::postBase('building-site-statistics-api/build-site-export-data.html', $params);
        return empty($buildSiteStatistics) ? [] : Json::decode($buildSiteStatistics);
    }

    /**
     * get提交数据共用方法
     * @param   string     $action 请求的方法名
     * @return  [type]     [description]
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }

    /**
     * post提交数据共用方法
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    public static function postBase($action, $data)
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action, json_encode($data, JSON_UNESCAPED_UNICODE);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 组合楼宇点位导出数据
     * @author wangxiwen
     * @version 2018-09-29
     * @param array $dateList 日期查询条件数组
     * @param array $buildSiteList 检索符合条件的数据
     * @return array
     */
    public static function getExportDataFormat($dateList, $buildSiteList)
    {
        foreach ($buildSiteList as $key => $buildSite) {
            $statisticsList = $buildSite['build_site_statistics'];
            foreach ($dateList as $date) {
                $buildSiteList[$key]['exportDateList'][] = $statisticsList[$date] ?? '';
            }
            unset($buildSiteList[$key]['build_site_statistics']);
        }
        return $buildSiteList;
    }

    /**
     * 获取检索条件日期数组
     * @author wangxiwen
     * @version 2018-09-29
     * @param array $beginDate 开始日期
     * @param array $endDate 结束日期
     * @return array
     */
    public static function getDateList($beginDate, $endDate)
    {
        $beginTime = strtotime($beginDate);
        $endTime   = strtotime($endDate);
        $days      = ($endTime - $beginTime) / 60 / 60 / 24;
        $dateList  = [];
        for ($i = 0; $i <= $days; $i++) {
            $dateList[] = date('Y-m-d', $beginTime + 60 * 60 * 24 * $i);
        }
        return $dateList;
    }

    /**
     * 同步设备初次投放日期到智能系统
     * @author wangxiwen
     * @version 2018-11-01
     * @param array $equipDeliveryRecord 设备投放记录
     * @return boolean
     */
    public static function syncEquipDeliveryRecord($equipDeliveryRecord)
    {
        return self::postBase('building-site-statistics-api/save-equip-operation-date.html', $equipDeliveryRecord);
    }

    /**
     * 同步楼宇初次投放日期到智能系统
     * @author wangxiwen
     * @version 2018-11-01
     * @param array $buildDeliveryRecord 楼宇投放记录
     * @return boolean
     */
    public static function syncBuildDeliveryRecord($buildDeliveryRecord)
    {
        return self::postBase('building-site-statistics-api/save-build-operation-date.html', $buildDeliveryRecord);
    }

    /**
     *
     * @author wangxiwen
     * @version 2018-11-01
     * @param array $buildDeliveryRecord 楼宇投放记录
     * @return boolean
     */
    public static function saveBuildDeliveryTime($buildNumber)
    {
        return self::getBase('building-site-statistics-api/save-build-delivery-time.html?build_number=' . $buildNumber);
    }

}
