<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;


/**
 * This is the model class for table "monthly_revenue".
 *
 * @property int $id
 * @property int $year 年份
 * @property int $month 月份
 * @property int $equip_no_mini 设备台数除mini
 * @property int $equip_total 总设备台数
 * @property double $monthly_turnover 本周 周期营业额
 * @property double $monthly_growth 本周 营业额增长率 %
 * @property double $average_daily_turnover 日均营业额
 * @property int $equip_operating_no_mini 设备运行天数除mini
 * @property int $equip_operating_days 总设备台天数
 * @property int $monthlycups 本月总杯数
 * @property int $monthly_pay_cups 本月付费总杯数
 * @property int $monthly_free_cups 本月免费总杯数
 * @property double $equip_daily_average 本月总台日均（杯数）
 * @property double $monthly_cups_average 总杯均价
 * @property double $pay_equip_daily_average 付费台日均
 * @property double $pay_equip_average_ratio 付费台日均的环比值
 * @property double $pay_cups_average 付费杯均价
 * @property double $pay_cups_ratio 付费杯均价的环比值
 * @property double $monthly_gross_profit 本月毛利润
 * @property int $monthly_count_at 统计时间
 */
class MonthlyRevenue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_revenue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'year',
                    'month',
                    'equip_no_mini',
                    'equip_total',
                    'equip_operating_no_mini',
                    'equip_operating_days',
                    'monthlycups',
                    'monthly_pay_cups',
                    'monthly_free_cups',
                    'monthly_count_at'
                ],
                'integer'
            ],
            [
                [
                    'monthly_turnover',
                    'monthly_growth',
                    'average_daily_turnover',
                    'equip_daily_average',
                    'monthly_cups_average',
                    'pay_equip_daily_average',
                    'pay_equip_average_ratio',
                    'pay_cups_average',
                    'pay_cups_ratio',
                    'monthly_gross_profit'
                ],
                'number'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'month' => 'Month',
            'equip_no_mini' => 'Equip No Mini',
            'equip_total' => 'Equip Total',
            'monthly_turnover' => 'Monthly Turnover',
            'monthly_growth' => 'Monthly Growth',
            'average_daily_turnover' => 'Average Daily Turnover',
            'equip_operating_no_mini' => 'Equip Operating No Mini',
            'equip_operating_days' => 'Equip Operating Days',
            'monthlycups' => 'Monthlycups',
            'monthly_pay_cups' => 'Monthly Pay Cups',
            'monthly_free_cups' => 'Monthly Free Cups',
            'equip_daily_average' => 'Equip Daily Average',
            'monthly_cups_average' => 'Monthly Cups Average',
            'pay_equip_daily_average' => 'Pay Equip Daily Average',
            'pay_equip_average_ratio' => 'Pay Equip Average Ratio',
            'pay_cups_average' => 'Pay Cups Average',
            'pay_cups_ratio' => 'Pay Cups Ratio',
            'monthly_gross_profit' => 'Monthly Gross Profit',
            'monthly_count_at' => 'Monthly Count At',
        ];
    }

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
    public static function exportMonthlyRevenueList($date)
    {
        if (!$date) {
            $monthlyRevenueList = self::getBase("monthly-report-api/monthly-revenues-list.html");
            return !$monthlyRevenueList ? [] : Json::decode($monthlyRevenueList);
        }
        $startTime = empty($date['start']) ? 0 : $date['start'];
        $endTime = empty($date['end']) ? 0 : $date['end'];
        $monthlyRevenueList = self::getBase("monthly-report-api/monthly-revenues-list.html" . '?start=' . $startTime . '&end=' . $endTime);
        return !$monthlyRevenueList ? [] : Json::decode($monthlyRevenueList);

    }

    public static function export($monthlyRevenueList)
    {
        if (empty($monthlyRevenueList['data'])) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('月报-营收数据信息')
            ->setCellValue('A1', '年份')
            ->setCellValue('B1', '月份')
            ->setCellValue('C1', '设备台数(除mini)')
            ->setCellValue('D1', '设备总台数')
            ->setCellValue('E1', '本期营业额')
            ->setCellValue('F1', '营业额增长率')
            ->setCellValue('G1', '日均营业额')
            ->setCellValue('H1', '运营设备台天数 (除mini)')
            ->setCellValue('I1', '总设备台天数')
            ->setCellValue('J1', '总杯数')
            ->setCellValue('K1', '付费杯数')
            ->setCellValue('L1', '总台日均（杯数)')
            ->setCellValue('M1', '总杯均价')
            ->setCellValue('N1', '付费台日均（杯数）')
            ->setCellValue('O1', '环比值')
            ->setCellValue('P1', '付费杯均价')
            ->setCellValue('Q1', '环比值')
            ->setCellValue('R1', '免费杯数')
            ->setCellValue('S1', '毛利润');
        $startNum = 2;
        foreach ($monthlyRevenueList['data'] as $monthlyRevenue) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('月报-营收数据信息')
                ->setCellValue('A' . $startNum, $monthlyRevenue['year'])
                ->setCellValue('B' . $startNum, $monthlyRevenue['month'])
                ->setCellValue('C' . $startNum, $monthlyRevenue['equip_no_mini'])
                ->setCellValue('D' . $startNum, $monthlyRevenue['equip_total'])
                ->setCellValue('E' . $startNum, $monthlyRevenue['monthly_turnover'])
                ->setCellValue('F' . $startNum, $monthlyRevenue['monthly_growth'])
                ->setCellValue('G' . $startNum, $monthlyRevenue['average_daily_turnover'])
                ->setCellValue('H' . $startNum, $monthlyRevenue['equip_operating_no_mini'])
                ->setCellValue('I' . $startNum, $monthlyRevenue['equip_operating_days'])
                ->setCellValue('J' . $startNum, $monthlyRevenue['monthly_cups'])
                ->setCellValue('K' . $startNum, $monthlyRevenue['monthly_pay_cups'])
                ->setCellValue('L' . $startNum, $monthlyRevenue['equip_daily_average'])
                ->setCellValue('M' . $startNum, $monthlyRevenue['monthly_cups_average'])
                ->setCellValue('N' . $startNum, $monthlyRevenue['pay_equip_daily_average'])
                ->setCellValue('O' . $startNum, $monthlyRevenue['pay_equip_average_ratio'])
                ->setCellValue('P' . $startNum, $monthlyRevenue['pay_cups_average'])
                ->setCellValue('Q' . $startNum, $monthlyRevenue['pay_cups_ratio'])
                ->setCellValue('R' . $startNum, $monthlyRevenue['monthly_free_cups'])
                ->setCellValue('S' . $startNum, $monthlyRevenue['monthly_gross_profit']);
            $startNum++;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-月报营收数据信息列表" . date("Y-m-d") . ".xlsx";
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