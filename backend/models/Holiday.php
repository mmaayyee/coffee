<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "holiday".
 *
 * @property integer $id
 * @property string $date_day
 * @property integer $is_holiday
 */
class Holiday extends \yii\db\ActiveRecord
{
    const IS_HOLIDAY = 1; //节假日运维
    const NO_HOLIDAY = 2; //节假日不运维
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'holiday';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_day'], 'safe'],
            [['is_holiday'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'date_day'   => 'Date Day',
            'is_holiday' => 'Is Holiday',
        ];
    }

    /**
     * 批量插入节假日不运维日期
     * @param $data 节假日不运维日期数组
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function addAll($data)
    {
        //表中存在则更新，不存在则新增
        $holidayData = self::find()->select(['date_day'])->column();
        //差集数据
        $changeData = array_diff($data, $holidayData);
        if (!empty($changeData)) {
            foreach ($changeData as $k => $item) {
                $diffData[] = [$item, Holiday::NO_HOLIDAY];
            }
            //插入差集中的数据
            Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['date_day', 'is_holiday'], $diffData)->execute();
        }
        $holidayData = self::find()->select(['date_day'])->column();
        //插入数据完成后，除了提交的数据更新为节假日不运维其他一律更新为节假日
        $changeData = array_diff($holidayData, $data);
        if (!empty($changeData)) {
            self::updateAll(['is_holiday' => Holiday::IS_HOLIDAY], ['in', 'date_day', $changeData]);
        }
        self::updateAll(['is_holiday' => Holiday::NO_HOLIDAY], ['in', 'date_day', $data]);
        return true;
    }
    /**
     * 批量插入节假日日期
     * @param $data 节假日日期数组
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function addHolidayAll($dateData)
    {
        $transaction = Yii::$app->db->beginTransaction();
        self::deleteAll(['not in', 'date_day', $dateData]);
        $holidayDate    = self::find()->select(['date_day'])->column();
        $addHolidayData = array_diff($dateData, $holidayDate);
        $date           = [];
        foreach ($addHolidayData as $k => $item) {
            $date[] = [$item, Holiday::IS_HOLIDAY];
        }
        $addres = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['date_day', 'is_holiday'], $date)->execute();
        if ($addres === false) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**
     * 查询制定条件数据
     * @param $filed
     * @param $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getFiled($filed, $where)
    {
        return self::find()->select($filed)->where($where)

            ->asArray()->all();
    }

    /**
     * 获取过去五天节假日日期
     * @author wangxiwen
     * @version 2018-10-13
     * @return
     */
    public static function getHolidayDate()
    {
        $dateList    = self::getLastHoliday();
        $holidayDate = [];
        foreach ($dateList as $date) {
            $holidayDate[] = str_replace('-', '', $date);
        }
        return $holidayDate;
    }

    /**
     * 获取过去五天工作日日期
     * @author wangxiwen
     * @version 2018-10-13
     * @return
     */
    public static function getWorkDate()
    {
        //过去30天日期
        for ($i = 0; $i < 30; $i++) {
            $LastDateList[] = date('Y-m-d', time() - 60 * 60 * 24 * ($i + 1));
        }
        //过去5天节假日日期
        $dateList = self::getLastHoliday();
        $workDate = [];
        foreach ($LastDateList as $date) {
            if (count($workDate) == 5) {
                break;
            }
            if (!in_array($date, $dateList)) {
                array_push($workDate, str_replace('-', '', $date));
            }
        }
        return $workDate;
    }

    /**
     * 获取过去五天的节假日
     * @author wangxiwen
     * @version 2018-10-16
     * @return
     */
    private static function getLastHoliday()
    {
        return self::find()->orderBy('date_day DESC')
            ->where(['<', 'date_day', date('Y-m-d')])
            ->limit(5)
            ->select('date_day')
            ->asArray()
            ->column();
    }

    /**
     * 获取今后三天中的节假日不运维日期
     * @author wangxiwen
     * @version 2018-07-19
     * @return array
     */
    public static function getFutureInoperate()
    {
        $dateList = [];
        for ($i = 0; $i < 3; $i++) {
            $date          = date('Y-m-d', time() + $i * 60 * 60 * 24);
            $inoperateDate = self::getHolidayInoperate($date);
            if (!$inoperateDate) {
                $dateList[] = $date;
            }
        }
        return $dateList;
    }

    /**
     * 获取节假日不运维日期
     * @author wangxiwen
     * @version 2018-10-16
     * @param string $date 日期
     * @return
     */
    private static function getHolidayInoperate($date)
    {
        return self::find()
            ->where(['date_day' => $date, 'is_holiday' => self::NO_HOLIDAY])
            ->select('date_day')
            ->scalar();
    }

    /**
     * 获取当前日期后30天的节假日日期数据
     * @author wangxiwen
     * @version 2018-5-28
     * @param $days 查询天数
     * @return array
     */
    public static function getFutureHoliday()
    {
        $beginDate = date('Y-m-d');
        $endDate   = date('Y-m-d', time() + 60 * 60 * 24 * 30);
        return self::find()
            ->andWhere(['between', 'date_day', $beginDate, $endDate])
            ->select('date_day')
            ->orderBy('date_day ASC')
            ->asArray()
            ->column();
    }

    /**
     * 获取当前日期前后十五天的节假日日期
     * @author wangxiwen
     * @version 2018-10-23
     * @return array
     */
    public static function getHolidayArray()
    {
        $beginDate = date('Y-m-d', (time() - 60 * 60 * 24 * 15));
        $endDate   = date('Y-m-d', (time() + 60 * 60 * 24 * 15));
        return self::find()
            ->select('date_day')
            ->where(['between', 'date_day', $beginDate, $endDate])
            ->asArray()
            ->column();
    }
}
