<?php

namespace backend\models;

use backend\models\BuildType;
use common\models\Api;
use common\models\Building;
use common\models\CoffeeBackApi;
use common\models\TaskApi;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is the model class for table "coupon_send_task".
 *
 * @property integer $id
 * @property string $task_name
 * @property string $where_string
 * @property integer $coupon_group_id
 * @property integer $task_type
 * @property integer $check_status
 * @property integer $user_num
 * @property integer $send_time
 * @property integer $create_time
 * @property string $refuse_reason
 */
class CouponSendTask extends \yii\db\ActiveRecord
{

    /** 审核状态 */
    // 待审核
    const CHECK_NOT = 0;
    // 审核通过(带发布)
    const CHECK_YES = 1;
    // 审核未通过
    const CHECK_NO = 2;
    // 已完成（已发布）
    const CHECK_COMPLETE = 3;


    // 任务id
    public $id;
    // 任务名称
    public $task_name;
    // 用户任务筛选ID
    public $selection_task_id;
    // 优惠券套餐id
    public $coupon_group_id;
    // 优惠券{id:num,...}列表 json 字符串
    public $coupon_id_num_map;
    // 审核状态
    public $check_status;
    // 用户数量
    public $user_num;
    // 优惠券发送数量
    public $coupon_num;
    // 任务发送时间
    public $send_time;
    // 任务创建时间
    public $create_time;
    // 审核时间
    public $examine_time;
    // 审核意见
    public $examine_opinion;
    // 导出文件路径
    public $mobile_file_path;
    // 输入的手机号Json
    public $mobile_string;
    // 手机号文件路径
    public $mobile_file_url;
    // 黑名单文件路径
    public $black_mobile_file_url;
    // 用户使用总数
    public $user_total_num;
    // 优惠券使用总数
    public $user_coupn_total_num;
    // 优惠券名称
    public $coupon_name;
    // 查询开始时间
    public $startTime;
    // 查询结束时间
    public $endTime;

    /**
     * 审核状态
     * @var array
     */
    public static $checkStatus = [
        self::CHECK_YES => '审核通过',
        self::CHECK_NO  => '审核未通过',
    ];

    /**
     * 任务状态
     * @var array
     */
    public static $taskStatus = [
        self::CHECK_NOT      => '待审核',
        self::CHECK_YES      => '待发布',
        self::CHECK_NO       => '审核未通过',
        self::CHECK_COMPLETE => '已发布',
    ];


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['examine_opinion'], 'required'],
            [['coupon_group_id', 'check_status', 'sheild_user_type', 'send_type', 'examine_time'], 'integer'],
            [['task_name', 'mobile_string', 'mobile_file_url', 'black_mobile_file_url'], 'string'],
            [['coupon_id_num_map'], 'string', 'max' => 255],
            [['task_name'], 'string'],
            [['id', 'selection_task_id', 'send_time', 'create_time', 'user_num', 'coupon_num', 'mobile_file_path', 'user_type', 'user_total_num', 'user_coupn_total_num', 'coupon_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => '任务id',
            'task_name'         => '任务名称',
            'coupon_group_id'   => '优惠券套餐',
            'coupon_id_num_map' => '优惠券id-数量',
            'coupon_name'       => '优惠券名称',
            'check_status'      => '审核状态',
            'user_num'          => '发送人数',
            'coupon_num'        => '发送优惠券数量',
            'send_time'         => '发券时间',
            'create_time'       => '任务创建时间',
            'examine_opinion'   => '审核意见',
            'user_total_num'    => '用户使用总数',
            'user_coupn_total_num'=> '用户优惠券使用总数',
        ];
    }

    /**
     * 获取审核状态名称
     * @author  zgw
     * @version 2017-08-24
     * @return  string     审核状态名称
     */
    public static function getCheckStatus($checkStatus)
    {
        return !isset(self::$taskStatus[$checkStatus]) ? '' : self::$taskStatus[$checkStatus];
    }

    /**
     * 格式化时间戳
     * @author  zgw
     * @version 2017-08-24
     * @param   int     $time 时间戳
     * @return  string        日期格式
     */
    public function dateFormat($time)
    {
        return !$time ? '' : date('Y-m-d H:i:s', $time);
    }

    /**
     * 获取单品名称
     * @author  zgw
     * @version 2017-08-28
     * @param   integer     $ID 单品id
     * @return  string          单品名称
     */
    private function getProductNames($ID)
    {
        $productNames = CoffeeBackApi::getProductNames($ID);
        if ($productNames) {
            return implode('，', $productNames);
        }
        return '';
    }

    /**
     * 保存失败删除上传的文件
     * @author  zgw
     * @version 2017-09-18
     * @return  integer     1-删除成功 0-删除失败
     */
    public function unlinkFile($data)
    {
        $filePathArr = [
            'addBuildingFile'    => $data['addBuildingFile'] == $this->addBuildingFile ? '' : $data['addBuildingFile'],
            'addMobileFile'      => $data['addMobileFile'] == $this->addMobileFile ? '' : $data['addMobileFile'],
            'sheildMobileFile'   => $data['sheildMobileFile'] == $this->sheildMobileFile ? '' : $data['sheildMobileFile'],
            'sheildBuildingFile' => $data['sheildBuildingFile'] == $this->sheildBuildingFile ? '' : $data['sheildBuildingFile'],
        ];
        return CoffeeBackApi::unlinkFile($filePathArr);
    }
    /**
     * 获取文件中的号码，
     * @author  zmy
     * @version 2018-01-30
     * @param   [string]     $mobileStr [号码字符串]
     * @return  [Array]                 [号码数组]
     */
    public static function getMobileList($mobileStr)
    {
        $mobileStr       =   str_replace('，', ',', $mobileStr);
        $mobileStr       =   str_replace("\r\n", ',', $mobileStr);
        $mobileStr       =   str_replace(' ', ',', $mobileStr);
        $mobileList      =   explode(',', trim($mobileStr));
        return $mobileList;
    }

    /**
     * 组合发券数据信息导出
     * @author  zmy
     * @version 2018-05-23
     * @param   [object]     $objPHPExcel        [Excel对象]
     * @param   [array]      $couponSendTaskList [发券任务数组]
     * @return  [object]                         [Excel对象]
     */
    public static function getCouponSendTaskList($objPHPExcel, $couponSendTaskList)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '发券任务数据总统计')
            ->mergeCells("A1:D1");
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A2", '序号')
                ->setCellValue("B2", '类别')
                ->setCellValue("C2", '发券用户数')
                ->setCellValue("D2", '使用用户券数');
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A3', '1')
                    ->setCellValue('B3', $couponSendTaskList['coupon_group_name'])
                    ->setCellValue('C3', $couponSendTaskList['user_num'])
                    ->setCellValue('D3', $couponSendTaskList['user_total_num']);
        return $objPHPExcel;
    }

    /**
     * 组合优惠券数据进行导出
     * @author  zmy
     * @version 2018-05-23
     * @param   [object]     $objPHPExcel              [Excel对象]
     * @param   [array]      $couponSendTaskCouponList [优惠券数据数组]
     * @return  [object]                               [Excel对象]
     */
    public static function getCombinationCouponList($objPHPExcel, $couponSendTaskCouponList)
    {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', '发券任务优惠券数据总统计')
            ->mergeCells("A1:E1")
            ->setCellValue('A2', '类别')
            ->setCellValue('B2', '种类')
            ->setCellValue('C2', '券种')
            ->setCellValue('D2', '发券量')
            ->setCellValue('E2', '使用券量');
        // excel表数据
        $couponNum = count($couponSendTaskCouponList['couponList'])+2;

        $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A3', $couponSendTaskCouponList['coupon_group_name'])
            ->mergeCells("A3:A$couponNum");

        if(isset($couponSendTaskCouponList['couponList']) && $couponSendTaskCouponList['couponList']){
            $startNum = 3;
            foreach ($couponSendTaskCouponList['couponList'] as $key => $coupon) {
                $objPHPExcel->setActiveSheetIndex(1)
                        ->setCellValue('B'.$startNum, $coupon['coupon_type'])
                        ->setCellValue('C'.$startNum, $coupon['coupon_name'])
                        ->setCellValue('D'.$startNum, $coupon['send_total_num'])
                        ->setCellValue('E'.$startNum, $coupon['use_total_num']);
                $startNum += 1;
            }
        }
        return $objPHPExcel;
    }

    /**
     * 组合单品数据进行导出
     * @author  zmy
     * @version 2018-05-23
     * @param   [object]     $objPHPExcel               [Excel对象]
     * @param   [array]      $couponSendTaskProductList [优惠券组合导出信息数组]
     * @param   [string]     $couponTaskName            [发券任务名称]
     * @return  [object]                                [发券任务对象]
     */
    public static function getCombinationProductList($objPHPExcel, $couponSendTaskProductList, $couponTaskName)
    {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(2)
                ->setCellValue('A2', "发券任务名称")
                ->setCellValue('B2', "单品名称");
        $i = 0;
        
        
        foreach ($couponSendTaskProductList['productInfo'] as $key => $coupon) {
            $objPHPExcel->setActiveSheetIndex(2)
            ->setCellValue(DistributionTask::getExcelConversionLetter($i+2).'1', $coupon['coupon_name'])
                ->mergeCells(DistributionTask::getExcelConversionLetter($i+2)."1:".(DistributionTask::getExcelConversionLetter($i+3))."1")
                ->setCellValue(DistributionTask::getExcelConversionLetter($i+2).'2', "销量")
                ->setCellValue(DistributionTask::getExcelConversionLetter($i+3).'2', "销售额");
                $i = $i+2;
        }
        $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($i+2).'1', '合计')
                    ->mergeCells(DistributionTask::getExcelConversionLetter($i+2)."1:".(DistributionTask::getExcelConversionLetter($i+3))."1")
                    ->setCellValue(DistributionTask::getExcelConversionLetter($i+2).'2', "销量")
                    ->setCellValue(DistributionTask::getExcelConversionLetter($i+3).'2', "销售额");
        
        //表数据遍历
        $productNum = 3;
        foreach ($couponSendTaskProductList['couponList'] as $productName => $couponList) {
            $salesNum = 2;
            $objPHPExcel->setActiveSheetIndex(2)
                ->setCellValue('A'.$productNum, $couponTaskName)
                ->setCellValue('B'.$productNum, $productName);
            foreach ($couponList as $couponId => $salesList) {
                $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($salesNum).$productNum, $salesList['sales_volume'])
                    ->setCellValue(DistributionTask::getExcelConversionLetter($salesNum+1).$productNum, $salesList['sales_quota']);
                    $salesNum = $salesNum + 2;
            }
            $objPHPExcel->setActiveSheetIndex(2)
            ->setCellValue(DistributionTask::getExcelConversionLetter($salesNum).$productNum, $couponSendTaskProductList['productSaleTotal'][$productName]['total_sales_volume'])
            ->setCellValue(DistributionTask::getExcelConversionLetter($salesNum+1).$productNum, $couponSendTaskProductList['productSaleTotal'][$productName]['total_sales_quota']);
            $productNum ++;
        }
        return $objPHPExcel;
    }
}
