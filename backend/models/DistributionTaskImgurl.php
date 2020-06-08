<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26
 * Time: 0:56
 */
namespace backend\models;

use \yii\db\ActiveRecord;

class DistributionTaskImgurl extends ActiveRecord
{
    public static function tableName()
    {
        return 'distribution_task_imgurl';
    }

    /**
     * 查询多条记录
     * @author sulingling
     * @version 2018-06-26
     * @param $where 数组 条件
     * @return object
     */
    public static function getInfo($where = [], $fields = '*')
    {
        return self::find()
            ->select($fields)
            ->where($where)
            ->all();
    }
}