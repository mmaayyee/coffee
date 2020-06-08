<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class ConsumeChannelDaily extends \yii\db\ActiveRecord
{

    /**
     * 导出获取数据接口
     * @Author  : GaoYongLi
     * @DateTime: 2018/7/18
     * @param $params
     * @return array|mixed
     */
    public static function exportUserConsumeList($params)
    {
        $productNameList = self::getBase("consume-channel-daily-api/export.html" . '?date=' . $params);
        return !$productNameList ? [] : Json::decode($productNameList);
    }

    /**
     * 导出专用方法
     * @Author  : GaoYongLi
     * @DateTime: 2018/7/18
     * @param $consumeList
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public static function export($consumeList)
    {
        $consumeCountList        = $consumeList['data']['consumeCountList'];
        $consumeOrganizationList = $consumeList['data']['consumeOrganizationList'];
        if (empty($consumeCountList) || empty($consumeOrganizationList)) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        // 获取表头
        $objPHPExcel = self::getHeadTable();
        // 获取汇总表数据
        $startNum = self::getConsumeCountList($consumeCountList, $objPHPExcel);
        // 下个表格开始的长度  $startNum
        $startOneNum   = $startNum + 1;
        $startTwoNum   = $startNum + 2;
        $startThreeNum = $startNum + 3;
        $startFourNum  = $startNum + 4;
        // 机构表数据
        if (!empty($consumeOrganizationList)) {
            foreach ($consumeOrganizationList as $key => $org) {
                if ($key == 0) {
                    $objPHPExcel->setActiveSheetIndex()
                        ->setCellValue('A' . $startOneNum, '渠道日报-机构渠道信息列表')
                        ->mergeCells("A" . $startOneNum . ":H" . $startOneNum)
                        ->setCellValue("A" . $startTwoNum,  date('Y-m-d', strtotime($org['list'][0]['orgList'][0]['total_at'])).'区域数据日报-' . $org['organizationGroupName'])
                        ->mergeCells("A" . $startTwoNum . ":H" . $startTwoNum)
                        ->setCellValue('A' . $startThreeNum, '区域')
                        ->setCellValue('B' . $startThreeNum, '渠道')
                        ->setCellValue('C' . $startThreeNum, '总台数')
                        ->setCellValue('D' . $startThreeNum, '商用台数')
                        ->setCellValue('E' . $startThreeNum, '开机商用台数')
                        ->setCellValue('F' . $startThreeNum, '新增注册人数')
                        ->setCellValue('G' . $startThreeNum, '付费销量')
                        ->setCellValue('H' . $startThreeNum, '销售额')
                        ->setCellValue('I' . $startThreeNum, '付费台日均');
                } else {
                    $objPHPExcel->setActiveSheetIndex()
                        ->setCellValue("A" . $startThreeNum, date('Y-m-d', strtotime($org['list'][0]['orgList'][0]['total_at'])) . '区域数据日报-' . $org['organizationGroupName'])
                        ->mergeCells("A" . $startThreeNum . ":H" . $startThreeNum);
                }

                foreach ($org['list'] as $orgk => $orgValue) {
                    $mergeEnd   = count($orgValue['orgList']) + $startFourNum - 1;
                    $mergeStart = $startFourNum;
                    $objPHPExcel->setActiveSheetIndex()
                        ->mergeCells("A" . $mergeStart . ":A" . $mergeEnd)
                        ->setCellValue('A' . $startFourNum, $orgValue['name']);
                    foreach ($orgValue['orgList'] as $number => $v) {
                        $objPHPExcel->setActiveSheetIndex()
                            ->setCellValue('B' . $startFourNum, $v['build_type_code'])
                            ->setCellValue('C' . $startFourNum, $v['equipments_count'])
                            ->setCellValue('D' . $startFourNum, $v['commercial_operation'])
                            ->setCellValue('E' . $startFourNum, $v['equipments_be_count'])
                            ->setCellValue('F' . $startFourNum, $v['new_register_users'])
                            ->setCellValue('G' . $startFourNum, $v['consume_pay_cups'])
                            ->setCellValue('H' . $startFourNum, $v['consume_pay_price'])
                            ->setCellValue('I' . $startFourNum, $v['pay_daily_average'])
                        ;
                        $startFourNum++;
                    }
                    $startFourNum = $mergeEnd + 1;
                }
                $objPHPExcel->setActiveSheetIndex()
                    ->mergeCells("A" . $startFourNum . ":B" . $startFourNum)
                    ->setCellValue('A' . $startFourNum, '合计')
                    ->setCellValue('C' . $startFourNum, $org['total']['equipments_count'])
                    ->setCellValue('D' . $startFourNum, $org['total']['commercial_operation'])
                    ->setCellValue('E' . $startFourNum, $org['total']['equipments_be_count'])
                    ->setCellValue('F' . $startFourNum, $org['total']['new_register_users'])
                    ->setCellValue('G' . $startFourNum, $org['total']['consume_pay_cups'])
                    ->setCellValue('H' . $startFourNum, $org['total']['consume_pay_price'])
                    ->setCellValue('I' . $startFourNum, $org['total']['pay_daily_average'])
                ;
                $startOneNum   = $startFourNum - 1;
                $startTwoNum   = $startFourNum;
                $startThreeNum = $startFourNum + 1;
                $startFourNum  = $startFourNum + 2;

            }
        }

        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-渠道日报-汇总信息列表" . date("Y-m-d") . ".xlsx";
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
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $params);
    }
    /**
     * @Author  : GaoYongLi
     * @DateTime: 2018/7/18
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    private static function getHeadTable()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex()
            ->setCellValue('A1', '日报总表-信息列表')
            ->mergeCells("A1:H1")
            ->setCellValue('A2', '数据项')
            ->setCellValue('B2', '本日数据')
            ->setCellValue('C2', '上周同期')
            ->setCellValue('D2', '周同比')
            ->setCellValue('E2', '上月同期')
            ->setCellValue('F2', '月同比')
            ->setCellValue('G2', 'MTD')
            ->setCellValue('H2', '日期');
        return $objPHPExcel;
    }
    /**
     * @Author  : GaoYongLi
     * @DateTime: 2018/7/18
     * @param $consumeCountList
     * @param $objPHPExcel
     * @return int
     */
    private static function getConsumeCountList($consumeCountList, $objPHPExcel)
    {
        if (isset($consumeCountList) && !empty($consumeCountList)) {
            $startNum = 3;
            foreach ($consumeCountList as $key => $count) {
                $objPHPExcel->setActiveSheetIndex()
                    ->setCellValue('A' . $startNum, $count['title'])
                    ->setCellValue('B' . $startNum, $count['today'])
                    ->setCellValue('C' . $startNum, $count['last_week_today'])
                    ->setCellValue('D' . $startNum, $count['week_compare'])
                    ->setCellValue('E' . $startNum, $count['last_month_today'])
                    ->setCellValue('F' . $startNum, $count['month_compare'])
                    ->setCellValue('G' . $startNum, $count['month_begin_and_end'])
                    ->setCellValue('H' . $startNum, date('Y-m-d', strtotime($count['date'])));
                $startNum += 1;
            }
        }
        return $startNum;
    }
}
