<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class ConsumeDailyTotal extends \yii\db\ActiveRecord
{

    /**
     * 导出获取数据接口
     * @Author  : GaoYongLi
     * @DateTime: 2018/7/18
     * @param $params
     * @return array|mixed
     */
    public static function exportConsumeDailyTotalList($params)
    {
        $productNameList = self::getBase("consume-daily-total-api/erp-consume-daily-export.html" . '?date=' . $params);
        return !$productNameList ? [] : Json::decode($productNameList);
    }
    /**
     * 导出数据所用接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-07-26
     * @param:    [param]
     * @return
     * @param     [type]     $ConsumeDailyTotalList [description]
     * @return    [type]                            [description]
     */
    public static function export($ConsumeDailyTotalList)
    {
        $userGrowthList        = $ConsumeDailyTotalList['data']['userGrowthList'];
        $consumptionRetailList = $ConsumeDailyTotalList['data']['consumptionRetailList'];
        $userSleepList         = $ConsumeDailyTotalList['data']['userSleepList'];
        $userRecallhList       = $ConsumeDailyTotalList['data']['userRecallhList'];
        $userRetainList        = $ConsumeDailyTotalList['data']['userRetainList'];
        $objPHPExcel           = new \PHPExcel();
        // 零售数据导出
        if (!empty($consumptionRetailList)) {
            $objPHPExcel = self::exportConsumption($consumptionRetailList, $objPHPExcel);
        }
        // 用户增长与活跃
        if (!empty($userGrowthList)) {
            $objPHPExcel = self::exportUserGrowthList($userGrowthList, $objPHPExcel);
        }
        // 沉睡用户与召回用户导出接口
        if (!empty($userSleepList) || !empty($userRecallhList)) {
            $objPHPExcel = self::exportUserSleepAndRecall($userSleepList, $userRecallhList, $objPHPExcel);
        }
        // 留存用户
        if (!empty($userRetainList)) {
            $objPHPExcel = self::exportUserRetainList($userRetainList, $objPHPExcel);
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-日报总表-信息列表" . date("Y-m-d") . ".xlsx";
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outputFileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //2007
        header('Cache-Control: max-age=0'); //禁止缓存
        $objWriter->save('php://output');
        exit;
    }
    /**
     * 留存用户导出接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-07-26
     * @param:    [param]
     * @return
     * @param     [type]     $userRetainList [description]
     * @param     [type]     $objPHPExcel    [description]
     * @return    [type]                     [description]
     */
    public static function exportUserRetainList($userRetainList, $objPHPExcel)
    {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(3)
            ->setTitle('日报总表-留存用户')
            ->setCellValue('A1', '日报总表-留存用户')
            ->mergeCells("A1:N1");
        $startTwoNum  = 3;
        $letterNumber = 67;
        $number       = 65;
        foreach ($userRetainList as $key => $UserRetain) {
            $letter = strtoupper(chr($letterNumber));
            if ($letterNumber > 90) {
                $num    = 65;
                $letter = strtoupper(chr($num)) . strtoupper(chr($number));
                $number++;
            }
            $objPHPExcel->setActiveSheetIndex(3)
                ->setCellValue('A2', '星期')
                ->setCellValue('B2', '日期')
                ->setCellValue($letter . '2', $key)
                ->setCellValue('A' . $startTwoNum, $UserRetain['retain_week'])
                ->setCellValue('B' . $startTwoNum, $key);
            $startThreeNum = 3;
            $capitalNumber = 67;
            foreach ($UserRetain['retain_number'] as $date => $retain) {
                $numTwo  = 65;
                $capital = strtoupper(chr($capitalNumber));
                if ($capitalNumber > 90) {
                    $numOne  = 65;
                    $capital = strtoupper(chr($numOne)) . strtoupper(chr($numTwo));
                    $numTwo++;
                }
                $objPHPExcel->setActiveSheetIndex(3)->setCellValue($capital . $startTwoNum, $retain);
                $startThreeNum++;
                $capitalNumber++;
            }
            $startTwoNum++;
            $letterNumber++;
        }
        return $objPHPExcel;
    }
    /**
     * 沉睡用户与召回用户导出接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-07-26
     * @param:    [param]
     * @return
     * @param     [type]     $userSleepList   [description]
     * @param     [type]     $userRecallhList [description]
     * @param     [type]     $objPHPExcel     [description]
     * @return    [type]                      [description]
     */
    public static function exportUserSleepAndRecall($userSleepList, $userRecallhList, $objPHPExcel)
    {
        $startTwoNum = 3;
        $objPHPExcel->createSheet();
        // 沉睡用户表
        if (!empty($userSleepList)) {
            $objPHPExcel->setActiveSheetIndex(2)
                ->setTitle('日报总表-沉睡用户')
                ->setCellValue('A1', '日报总表-沉睡用户')
                ->mergeCells("A1:N1")
                ->setCellValue('A2', '星期')
                ->setCellValue('B2', '日期')
                ->setCellValue('C2', '总沉睡用户')
                ->setCellValue('D2', '沉睡2周-1个月')
                ->setCellValue('E2', '沉睡1个月-2个月')
                ->setCellValue('F2', '沉睡2个月-3个月')
                ->setCellValue('G2', '沉睡3个月-4个月')
                ->setCellValue('H2', '沉睡4个月-5个月')
                ->setCellValue('I2', '沉睡5个月-6个月')
                ->setCellValue('J2', '沉睡6个月以上')
            ;
            foreach ($userSleepList as $key => $userSleep) {
                $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue('A' . $startTwoNum, $userSleep['create_week_day'])
                    ->setCellValue('B' . $startTwoNum, date('Y-m-d', strtotime($userSleep['created_at'])))
                    ->setCellValue('C' . $startTwoNum, $userSleep['user_sleep_total'])
                    ->setCellValue('D' . $startTwoNum, $userSleep['sleep_two_weeks'])
                    ->setCellValue('E' . $startTwoNum, $userSleep['sleep_one_month'])
                    ->setCellValue('F' . $startTwoNum, $userSleep['sleep_two_month'])
                    ->setCellValue('G' . $startTwoNum, $userSleep['sleep_three_month'])
                    ->setCellValue('H' . $startTwoNum, $userSleep['sleep_four_month'])
                    ->setCellValue('I' . $startTwoNum, $userSleep['sleep_five_month'])
                    ->setCellValue('J' . $startTwoNum, $userSleep['sleep_six_month']);
                $startTwoNum++;
            }
        }
        if (!empty($userRecallhList)) {
            $startTwoNum += 4;
            $startTitleNum = $startTwoNum - 1;
            $objPHPExcel->setActiveSheetIndex(2)
                ->setTitle('日报总表-沉睡与召回用户信息')
                ->setCellValue('A' . $startTitleNum, '日报总表-召回用户')
                ->mergeCells("A" . $startTitleNum . ":N" . $startTitleNum)
                ->setCellValue('A' . $startTwoNum, '星期')
                ->setCellValue('B' . $startTwoNum, '日期')
                ->setCellValue('C' . $startTwoNum, '总召回用户')
                ->setCellValue('D' . $startTwoNum, '召回2周-1个月')
                ->setCellValue('E' . $startTwoNum, '召回1个月-2个月')
                ->setCellValue('F' . $startTwoNum, '召回2个月-3个月')
                ->setCellValue('G' . $startTwoNum, '召回3个月-4个月')
                ->setCellValue('H' . $startTwoNum, '召回4个月-5个月')
                ->setCellValue('I' . $startTwoNum, '召回5个月-6个月')
                ->setCellValue('J' . $startTwoNum, '召回6个月以上')
            ;
            $startTwoNumber = $startTwoNum + 1;
            foreach ($userRecallhList as $key => $userRecall) {
                $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue('A' . $startTwoNumber, $userRecall['create_week_day'])
                    ->setCellValue('B' . $startTwoNumber, date('Y-m-d', strtotime($userRecall['created_at'])))
                    ->setCellValue('C' . $startTwoNumber, $userRecall['user_recall_total'])
                    ->setCellValue('D' . $startTwoNumber, $userRecall['recall_two_weeks'])
                    ->setCellValue('E' . $startTwoNumber, $userRecall['recall_one_month'])
                    ->setCellValue('F' . $startTwoNumber, $userRecall['recall_two_month'])
                    ->setCellValue('G' . $startTwoNumber, $userRecall['recall_three_month'])
                    ->setCellValue('H' . $startTwoNumber, $userRecall['recall_four_month'])
                    ->setCellValue('I' . $startTwoNumber, $userRecall['recall_five_month'])
                    ->setCellValue('J' . $startTwoNumber, $userRecall['recall_six_month']);
                $startTwoNumber++;
            }
        }
        return $objPHPExcel;
    }
    /**
     * 导出用户增长与活跃数据
     * @Author:   GaoYongLi
     * @DateTime: 2018-07-26
     * @param:    [param]
     * @return
     * @param     [type]     $userGrowthList [description]
     * @param     [type]     $objPHPExcel    [description]
     * @return    [type]                     [description]
     */
    public static function exportUserGrowthList($userGrowthList, $objPHPExcel)
    {
        $objPHPExcel->setActiveSheetIndex(1)
            ->setTitle('日报总表-用户增长及活跃')
            ->setCellValue('A1', '日报总表-用户增长及活跃')
            ->mergeCells("A1:N1")
            ->setCellValue('A2', '星期')
            ->setCellValue('B2', '日期')
            ->setCellValue('C2', '总用户数')
            ->setCellValue('D2', '新增用户数')
            ->setCellValue('E2', '用户增长率')
            ->setCellValue('F2', '活跃用户数')
            ->setCellValue('G2', '上周同期对比')
            ->setCellValue('H2', '付费活跃用户数')
            ->setCellValue('I2', '上周同期对比')
            ->setCellValue('J2', '免费活跃用户')
            ->setCellValue('K2', '人均付费购买频次')
        ;
        $startOneNum = 3;
        $startNum    = 3;
        foreach ($userGrowthList['list'] as $key => $userGrowth) {
            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('A' . $startOneNum, $userGrowth['create_week_day'])
                ->setCellValue('B' . $startOneNum, date('Y-m-d', strtotime($userGrowth['created_at'])))
                ->setCellValue('C' . $startOneNum, $userGrowth['users_total_number'])
                ->setCellValue('D' . $startOneNum, $userGrowth['new_total_number'])
                ->setCellValue('E' . $startOneNum, $userGrowth['users_total_up'])
                ->setCellValue('F' . $startOneNum, $userGrowth['user_active_total'])
                ->setCellValue('G' . $startOneNum, $userGrowth['week_active_total'])
                ->setCellValue('H' . $startOneNum, $userGrowth['user_pay_active'])
                ->setCellValue('I' . $startOneNum, $userGrowth['week_pay_active'])
                ->setCellValue('J' . $startOneNum, $userGrowth['user_free_active'])
                ->setCellValue('K' . $startOneNum, $userGrowth['per_capita_pay']);
            $startOneNum++;
            $startNum++;
        }
        $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue("A" . $startOneNum, date('Y-m', strtotime($userGrowth['created_at'])) . '月MTD数据')
            ->mergeCells("A" . $startOneNum . ":B" . $startNum)
            ->setCellValue('C' . $startOneNum, $userGrowthList['MTD']['users_total_number'])
            ->setCellValue('D' . $startOneNum, $userGrowthList['MTD']['new_total_number'])
            ->setCellValue('E' . $startOneNum, $userGrowthList['MTD']['users_total_up'])
            ->setCellValue('F' . $startOneNum, $userGrowthList['MTD']['user_active_total'])
            ->setCellValue('G' . $startOneNum, $userGrowthList['MTD']['week_active_total'])
            ->setCellValue('H' . $startOneNum, $userGrowthList['MTD']['user_pay_active'])
            ->setCellValue('I' . $startOneNum, $userGrowthList['MTD']['week_pay_active'])
            ->setCellValue('J' . $startOneNum, $userGrowthList['MTD']['user_free_active'])
            ->setCellValue('K' . $startOneNum, $userGrowthList['MTD']['per_capita_pay'])
        ;
        return $objPHPExcel;
    }
    /**
     * 零售数据导出数据
     * @Author:   GaoYongLi
     * @DateTime: 2018-07-26
     * @param     [array]     $consumptionRetailList [导出的数据数组]
     * @param     [obj]     $objPHPExcel           [excel插件对象]
     * @return    [type]                            [.xls]
     */
    public static function exportConsumption($consumptionRetailList, $objPHPExcel)
    {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('日报总表-零售数据信息')
            ->setCellValue('A1', '日报总表-零售数据')
            ->mergeCells("A1:N1")
            ->setCellValue('A2', '星期')
            ->setCellValue('B2', '日期')
            ->setCellValue('C2', '设备台数(除mini)')
            ->setCellValue('D2', '设备总台数')
            ->setCellValue('E2', '总销售额(元)')
            ->setCellValue('F2', '总杯数')
            ->setCellValue('G2', '付费杯数')
            ->setCellValue('H2', '总台日均(杯数)')
            ->setCellValue('I2', '周同比(总台日均)')
            ->setCellValue('J2', '上周同期')
            ->setCellValue('K2', '付费台日均')
            ->setCellValue('L2', '周同比(付费台日均)')
            ->setCellValue('M2', '上周同期')
            ->setCellValue('N2', '总杯均价')
            ->setCellValue('O2', '付费杯均价')
            ->setCellValue('P2', '免费杯数')
        ;

        $startNum = 3;
        foreach ($consumptionRetailList['list'] as $key => $consumptionRetail) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $startNum, $consumptionRetail['week'])
                ->setCellValue('B' . $startNum, date('Y-m-d', strtotime($consumptionRetail['date'])))
                ->setCellValue('C' . $startNum, $consumptionRetail['equipments_number'])
                ->setCellValue('D' . $startNum, $consumptionRetail['equipments_number_count'])
                ->setCellValue('E' . $startNum, $consumptionRetail['consume_total_amount'])
                ->setCellValue('F' . $startNum, $consumptionRetail['consume_total_cups'])
                ->setCellValue('G' . $startNum, $consumptionRetail['consume_pay_cups'])
                ->setCellValue('H' . $startNum, $consumptionRetail['equipments_daily_average'])
                ->setCellValue('I' . $startNum, $consumptionRetail['week_compare_daily_average'])
                ->setCellValue('J' . $startNum, $consumptionRetail['last_week_daily_average'])
                ->setCellValue('K' . $startNum, $consumptionRetail['pay_daily_average'])
                ->setCellValue('L' . $startNum, $consumptionRetail['week_compare_pay'])
                ->setCellValue('M' . $startNum, $consumptionRetail['week_pay_daily_average'])
                ->setCellValue('N' . $startNum, $consumptionRetail['cups_daily_average'])
                ->setCellValue('O' . $startNum, $consumptionRetail['pay_cups_daily_average'])
                ->setCellValue('P' . $startNum, $consumptionRetail['free_cups_daily_average']);
            $startNum++;
        }
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $startNum, date('Y-m', strtotime($consumptionRetail['date'])) . '月MTD数据')
            ->mergeCells("A" . $startNum . ":B" . $startNum)
            ->setCellValue('C' . $startNum, $consumptionRetailList['MTD']['equipments_number'])
            ->setCellValue('D' . $startNum, $consumptionRetailList['MTD']['equipments_number_count'])
            ->setCellValue('E' . $startNum, $consumptionRetailList['MTD']['consume_total_amount'])
            ->setCellValue('F' . $startNum, $consumptionRetailList['MTD']['consume_total_cups'])
            ->setCellValue('G' . $startNum, $consumptionRetailList['MTD']['consume_pay_cups'])
            ->setCellValue('H' . $startNum, $consumptionRetailList['MTD']['equipments_daily_average'])
            ->setCellValue('I' . $startNum, $consumptionRetailList['MTD']['week_compare_daily_average'])
            ->setCellValue('J' . $startNum, $consumptionRetailList['MTD']['last_week_daily_average'])
            ->setCellValue('K' . $startNum, $consumptionRetailList['MTD']['pay_daily_average'])
            ->setCellValue('L' . $startNum, $consumptionRetailList['MTD']['week_compare_pay'])
            ->setCellValue('M' . $startNum, $consumptionRetailList['MTD']['week_pay_daily_average'])
            ->setCellValue('N' . $startNum, $consumptionRetailList['MTD']['cups_daily_average'])
            ->setCellValue('O' . $startNum, $consumptionRetailList['MTD']['pay_cups_daily_average'])
            ->setCellValue('P' . $startNum, $consumptionRetailList['MTD']['free_cups_daily_average'])
        ;
        return $objPHPExcel;
    }
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
}
