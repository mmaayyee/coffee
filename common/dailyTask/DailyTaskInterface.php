<?php
namespace common\dailyTask;

/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/7/4
 * Time: 下午3:08
 */
interface DailyTaskInterface
{
    //获取设备
    function getEquipments();

    //获取配送员ID
    function getDistributionIds();

    //获取节假日
    function getHoliday();

    //获取日常任务设置
    function getDailyTaskSetting();
}