<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class WeeklyRevenue extends \yii\db\ActiveRecord
{

    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }

    /**
     * 营收数据导出
     * @Author  : GaoYongLi
     * @DateTime: 2018/8/23
     */
    public static function exportWeeklyRevenueList($date)
    {
         if(!$date){
             $weeklyRevenueList = self::getBase("weekly-report-api/weekly-revenues-list.html");
             return !$weeklyRevenueList ? [] : Json::decode($weeklyRevenueList);
         }
         $startTime = empty($date['start']) ? 0 :$date['start'];
         $endTime = empty($date['end']) ? 0 :$date['end'];
         $weeklyRevenueList = self::getBase("weekly-report-api/weekly-revenues-list.html" . '?start='.$startTime.'&end='.$endTime);
        return !$weeklyRevenueList ? [] : Json::decode($weeklyRevenueList);

    }

    public static function export($weeklyRevenueList)
    {
        if (empty($weeklyRevenueList['data'])) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('周报-营收数据信息')
            ->setCellValue('A1', '年份')
            ->setCellValue('B1', '日期')
            ->setCellValue('C1', '周次')
            ->setCellValue('D1', '设备台数(除mini)')
            ->setCellValue('E1', '设备总台数')
            ->setCellValue('F1', '本期营业额')
            ->setCellValue('G1', '增长率')
            ->setCellValue('H1', '日均营业额')
            ->setCellValue('I1', '运营设备台天数 (除mini)')
            ->setCellValue('J1', '总设备台天数')
            ->setCellValue('K1', '总杯数')
            ->setCellValue('L1', '付费杯数')
            ->setCellValue('M1', '总台日均（杯数)')
            ->setCellValue('N1', '总杯均价')
            ->setCellValue('O1', '付费台日均（杯数）')
            ->setCellValue('P1', '环比值')
            ->setCellValue('Q1', '付费杯均价')
            ->setCellValue('R1', '环比值')
            ->setCellValue('S1', '免费杯数')
            ->setCellValue('T1', '毛利润')
            ;
        $startNum = 2;
        foreach ($weeklyRevenueList['data'] as $weeklyRevenue){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('周报-营收数据信息')
                ->setCellValue('A'.$startNum, $weeklyRevenue['year'])
                ->setCellValue('B'.$startNum, $weeklyRevenue['cycle_str'])
                ->setCellValue('C'.$startNum, $weeklyRevenue['weekly_number'])
                ->setCellValue('D'.$startNum, $weeklyRevenue['equip_no_mini'])
                ->setCellValue('E'.$startNum, $weeklyRevenue['equip_total'])
                ->setCellValue('F'.$startNum, $weeklyRevenue['weekly_turnover'])
                ->setCellValue('G'.$startNum, $weeklyRevenue['weekly_growth'])
                ->setCellValue('H'.$startNum, $weeklyRevenue['average_daily_turnover'])
                ->setCellValue('I'.$startNum, $weeklyRevenue['equip_operating_no_mini'])
                ->setCellValue('J'.$startNum, $weeklyRevenue['equip_operating_days'])
                ->setCellValue('K'.$startNum, $weeklyRevenue['weekly_cups'])
                ->setCellValue('L'.$startNum, $weeklyRevenue['weekly_pay_cups'])
                ->setCellValue('M'.$startNum, $weeklyRevenue['equip_daily_average'])
                ->setCellValue('N'.$startNum, $weeklyRevenue['weekly_cups_average'])
                ->setCellValue('O'.$startNum, $weeklyRevenue['pay_equip_daily_average'])
                ->setCellValue('P'.$startNum, $weeklyRevenue['pay_equip_average_ratio'])
                ->setCellValue('Q'.$startNum, $weeklyRevenue['pay_cups_average'])
                ->setCellValue('R'.$startNum, $weeklyRevenue['pay_cups_ratio'])
                ->setCellValue('S'.$startNum, $weeklyRevenue['weekly_free_cups'])
                ->setCellValue('T'.$startNum, $weeklyRevenue['weekly_gross_profit'])
                ;
            $startNum++;
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-周报营收数据信息列表" . date("Y-m-d") . ".xlsx";
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

}