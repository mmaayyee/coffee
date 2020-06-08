<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class WeeklyUser extends \yii\db\ActiveRecord
{

    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
    /**
     * 用户数据导出
     * @Author  : GaoYongLi
     * @DateTime: 2018/8/23
     */
    public static function exportWeeklyUserList($date)
    {
        if(!$date){
            $weeklyUserList = self::getBase("weekly-report-api/weekly-user-list.html");
            return !$weeklyUserList ? [] : Json::decode($weeklyUserList);
        }
        $startTime = empty($date['start']) ? 0 :$date['start'];
        $endTime = empty($date['end']) ? 0 :$date['end'];
        $weeklyUserList = self::getBase("weekly-report-api/weekly-user-list.html" . '?start='.$startTime.'&end='.$endTime);
        return !$weeklyUserList ? [] : Json::decode($weeklyUserList);


    }
    /**
     * 月报-用户数据导出
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-27
     * @param     [type]     $weeklyUserList [需导出的用户数据]
     * @return    [type]                     [.xls]
     */
    public static function export($weeklyUserList)
    {
        if (empty($weeklyUserList['data'])) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('周报-用户数据信息')
            ->setCellValue('A1', '年份')
            ->setCellValue('B1', '日期')
            ->setCellValue('C1', '周次')
            ->setCellValue('D1', '总用户数')
            ->setCellValue('E1', '新增用户')
            ->setCellValue('F1', '用户增长率')
            ->setCellValue('G1', '注册用户')
            ->setCellValue('H1', '新增注册用户')
            ->setCellValue('I1', '注册用户增长率')
            ->setCellValue('J1', '非注册用户')
            ->setCellValue('K1', '活跃人数')
            ->setCellValue('L1', '活跃增长率')
            ->setCellValue('M1', '付费活跃人数')
            ->setCellValue('N1', '付费活跃增长率')
            ->setCellValue('O1', '免费活跃人数')
        ;
        $startNum = 2;
        foreach ($weeklyUserList['data'] as $weeklyUser) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('周报-营收数据信息')
                ->setCellValue('A' . $startNum, $weeklyUser['year'])
                ->setCellValue('B' . $startNum, $weeklyUser['cycle_str'])
                ->setCellValue('C' . $startNum, $weeklyUser['weekly_number'])
                ->setCellValue('D' . $startNum, $weeklyUser['users_total'])
                ->setCellValue('E' . $startNum, $weeklyUser['new_users_total'])
                ->setCellValue('F' . $startNum, $weeklyUser['new_users_growth'])
                ->setCellValue('G' . $startNum, $weeklyUser['registered_user'])
                ->setCellValue('H' . $startNum, $weeklyUser['new_registered_user'])
                ->setCellValue('I' . $startNum, $weeklyUser['new_registered_growth'])
                ->setCellValue('J' . $startNum, $weeklyUser['guest_user'])
                ->setCellValue('K' . $startNum, $weeklyUser['active_user'])
                ->setCellValue('L' . $startNum, $weeklyUser['active_user_growth'])
                ->setCellValue('M' . $startNum, $weeklyUser['pay_active_user'])
                ->setCellValue('N' . $startNum, $weeklyUser['pay_user_growth'])
                ->setCellValue('O' . $startNum, $weeklyUser['free_active_user'])
            ;
            $startNum++;
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-周报用户数据信息列表" . date("Y-m-d") . ".xlsx";
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
