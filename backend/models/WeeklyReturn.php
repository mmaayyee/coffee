<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class WeeklyReturn extends \yii\db\ActiveRecord
{

    public static function getBase($action, $params = '')
    {
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
    /**
     * 复购数据导出
     * @Author  : GaoYongLi
     * @DateTime: 2018/8/23
     */
    public static function exportWeeklyReturnList($date)
    {
        if(!$date){
            $weeklyReturnList = self::getBase("weekly-report-api/weekly-repurchase-list.html");
            return !$weeklyReturnList ? [] : Json::decode($weeklyReturnList);
        }
        $startTime = empty($date['start']) ? 0 :$date['start'];
        $endTime = empty($date['end']) ? 0 :$date['end'];
        $weeklyReturnList = self::getBase("weekly-report-api/weekly-repurchase-list.html" . '?start='.$startTime.'&end='.$endTime);
        return !$weeklyReturnList ? [] : Json::decode($weeklyReturnList);
    }

    public static function export($weeklyReturnList)
    {
        if (empty($weeklyReturnList['data'])) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('周报-营收数据信息')
            ->setCellValue('A1', '年份')
            ->setCellValue('B1', '日期')
            ->setCellValue('C1', '周次')
            ->setCellValue('D1', '总转化率')
            ->setCellValue('E1', '周转化率')
            ->setCellValue('F1', '总复购率')
            ->setCellValue('G1', '周复购率')
        ;
        $startNum = 2;
        foreach ($weeklyReturnList['data'] as $weeklyReturn){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('周报-营收数据信息')
                ->setCellValue('A'.$startNum, $weeklyReturn['year'])
                ->setCellValue('B'.$startNum, $weeklyReturn['cycle_str'])
                ->setCellValue('C'.$startNum, $weeklyReturn['weekly_number'])
                ->setCellValue('D'.$startNum, $weeklyReturn['total_conversion'])
                ->setCellValue('E'.$startNum, $weeklyReturn['weekly_conversion'])
                ->setCellValue('F'.$startNum, $weeklyReturn['total_repurchase'])
                ->setCellValue('G'.$startNum, $weeklyReturn['weekly_repurchase'])
            ;
            $startNum++;
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-周报复购数据信息列表" . date("Y-m-d") . ".xlsx";
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