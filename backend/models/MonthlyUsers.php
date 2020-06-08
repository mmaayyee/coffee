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
class MonthlyUsers extends \yii\db\ActiveRecord
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
            [['year', 'month', 'equip_no_mini', 'equip_total', 'equip_operating_no_mini', 'equip_operating_days', 'monthlycups', 'monthly_pay_cups', 'monthly_free_cups', 'monthly_count_at'], 'integer'],
            [['monthly_turnover', 'monthly_growth', 'average_daily_turnover', 'equip_daily_average', 'monthly_cups_average', 'pay_equip_daily_average', 'pay_equip_average_ratio', 'pay_cups_average', 'pay_cups_ratio', 'monthly_gross_profit'], 'number'],
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
     * 用户数据导出
     * @Author  : GaoYongLi
     * @DateTime: 2018/8/23
     */
    public static function exportWeeklyUserList($date)
    {
        if (!$date) {
            $monthlyUserList = self::getBase("monthly-report-api/monthly-user-list.html");
            return !$monthlyUserList ? [] : Json::decode($monthlyUserList);
        }
        $startTime = empty($date['start']) ? 0 : $date['start'];
        $endTime = empty($date['end']) ? 0 : $date['end'];
        $monthlyUserList = self::getBase("monthly-report-api/monthly-user-list.html" . '?start=' . $startTime . '&end=' . $endTime);
        return !$monthlyUserList ? [] : Json::decode($monthlyUserList);


    }

    /**
     * 月报-用户数据导出
     * @Author:   GaoYongLi
     * @DateTime: 2018-08-27
     * @param     [type]     $weeklyUserList [需导出的用户数据]
     * @return    [type]                     [.xls]
     */
    public static function export($monthlyUserList)
    {
        if (empty($monthlyUserList['data'])) {
            echo '当前日期无数据,请返回重新选择日期!';
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setTitle('月报-用户数据信息')
            ->setCellValue('A1', '年份')
            ->setCellValue('B1', '月份')
            ->setCellValue('C1', '总用户数')
            ->setCellValue('D1', '新增用户')
            ->setCellValue('E1', '用户增长率')
            ->setCellValue('F1', '注册用户')
            ->setCellValue('G1', '新增注册用户')
            ->setCellValue('H1', '注册用户增长率')
            ->setCellValue('I1', '非注册用户')
            ->setCellValue('J1', '活跃人数')
            ->setCellValue('K1', '活跃增长率')
            ->setCellValue('L1', '付费活跃人数')
            ->setCellValue('M1', '付费活跃增长率')
            ->setCellValue('N1', '免费活跃人数');
        $startNum = 2;
        foreach ($monthlyUserList['data'] as $monthlyUser) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('月报-营收数据信息')
                ->setCellValue('A' . $startNum, $monthlyUser['year'])
                ->setCellValue('B' . $startNum, $monthlyUser['month'])
                ->setCellValue('C' . $startNum, $monthlyUser['users_total'])
                ->setCellValue('D' . $startNum, $monthlyUser['new_users_total'])
                ->setCellValue('E' . $startNum, $monthlyUser['new_users_growth'])
                ->setCellValue('F' . $startNum, $monthlyUser['registered_user'])
                ->setCellValue('G' . $startNum, $monthlyUser['new_registered_user'])
                ->setCellValue('H' . $startNum, $monthlyUser['new_registered_growth'])
                ->setCellValue('I' . $startNum, $monthlyUser['guest_user'])
                ->setCellValue('J' . $startNum, $monthlyUser['active_user'])
                ->setCellValue('K' . $startNum, $monthlyUser['active_user_growth'])
                ->setCellValue('L' . $startNum, $monthlyUser['pay_active_user'])
                ->setCellValue('M' . $startNum, $monthlyUser['pay_user_growth'])
                ->setCellValue('N' . $startNum, $monthlyUser['free_active_user']);
            $startNum++;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $outputFileName = "咖啡零点吧-月报用户数据信息列表" . date("Y-m-d") . ".xlsx";
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
