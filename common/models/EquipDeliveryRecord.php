<?php

namespace common\models;

use backend\models\EquipAcceptance;
use backend\models\EquipDelivery;
use Yii;

/**
 * This is the model class for table "equip_delivery_record".
 *
 * @property integer $Id
 * @property string $equip_id
 * @property string $build_id
 * @property string $delivery_id
 * @property integer $bind_status
 * @property string $create_time
 * @property integer $delivery_record_result
 * @property string $un_bind_time
 */
class EquipDeliveryRecord extends \yii\db\ActiveRecord
{
    public $orgArr;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_delivery_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'build_id', 'delivery_id', 'bind_status', 'create_time', 'delivery_record_status', 'un_bind_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'                     => 'ID',
            'equip_id'               => '设备',
            'build_id'               => '楼宇',
            'delivery_id'            => '投放单',
            'bind_status'            => '绑定状态',
            'create_time'            => '创建时间',
            'delivery_record_status' => '投放记录结果',
            'un_bind_time'           => '解绑时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['id' => 'equip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDelivery()
    {
        return $this->hasOne(EquipDelivery::className(), ['Id' => 'delivery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcceptance()
    {
        return $this->hasOne(EquipAcceptance::className(), ['delivery_id' => 'delivery_id']);
    }

    /**
     * 添加投放记录(投放验收时使用)
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $buildId        [description]
     * @param   [type]     $equipId        [description]
     * @param   [type]     $deliveryId     [description]
     * @param   [type]     $bindStatus     [description]
     * @param   [type]     $deliveryStatus [description]
     * @return  [type]                     [description]
     */
    public static function deliveryRecord($buildId, $equipId, $deliveryId, $deliveryStatus)
    {
        $recordModel                         = new EquipDeliveryRecord();
        $recordModel->build_id               = $buildId;
        $recordModel->equip_id               = $equipId;
        $recordModel->delivery_id            = $deliveryId;
        $recordModel->bind_status            = 1;
        $recordModel->create_time            = time();
        $recordModel->delivery_record_status = $deliveryStatus;
        if ($recordModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '添加投放记录失败');
            return false;
        }
        return true;
    }

    /**
     *  统计撤回的机器数
     * @param $start  解绑开始区间时间
     * @param $end    解绑结束区间时间
     * @return
     */
    public static function equipDeliveryRecord($start, $end)
    {
        $equipList = EquipDeliveryRecord::find()
            ->alias('er')
            ->select('e.equip_code,e.equip_type_id')
            ->leftJoin('equipments e', 'e.id=er.equip_id')
            ->andWhere(['delivery_record_status' => 2])
            ->andWhere(['>=', 'un_bind_time', $start])
            ->andWhere(['<=', 'un_bind_time', $end])
            ->asArray()
            ->all();
        return \common\helpers\Tools::map($equipList, 'equip_code', 'equip_type_id', null, null);
    }

    /**
     * 获取楼宇点位设备运营结束时间(最后一次)
     * @author wangxiwen
     * @version 2018-09-26
     * @return
     */
    public static function getBuildUntieTime()
    {
        $deliveryRecordArray = self::find()->orderBy('ed.un_bind_time ASC')
            ->alias('ed')
            ->leftJoin('building b', 'b.id = ed.build_id')
            ->select('ed.un_bind_time,b.build_number')
            ->asArray()
            ->all();
        $deliveryRecordList = [];
        foreach ($deliveryRecordArray as $delivery) {
            $buildNumber = $delivery['build_number'];
            $unBindTime  = $delivery['un_bind_time'];
            if (empty($buildNumber) || empty($unBindTime)) {
                continue;
            }
            $unBindDate = date('Y-m-d', $unBindTime);

            $deliveryRecordList[$buildNumber] = $unBindDate;
        }
        return $deliveryRecordList;
    }

    /**
     * 获取楼宇投放记录(楼宇点位统计定时任务使用)
     * @author wangxiwen
     * @version 2018-09-27
     * @return array
     */
    public static function getBuildReleaseRecord()
    {
        //当前日期
        $current   = date('Y-m-d', time());
        $beginTime = strtotime($current);
        $endTime   = strtotime($current . ' 23:59:59');
        //十二点
        $TwelveClock = $beginTime + 12 * 60 * 60;
        //如果设备绑定时间或解绑时间在当前日期范围内，则认为当前日期下该点位存在换机行为
        $deliveryRecordArray = self::find()->orderBy('ed.create_time ASC')
            ->alias('ed')
            ->leftJoin('equipments e', 'e.id = ed.equip_id')
            ->leftJoin('building b', 'b.id = e.build_id')
            ->andWhere(['or',
                ['and',
                    ['>', 'ed.create_time', $beginTime],
                    ['<=', 'ed.create_time', $endTime]],
                ['and',
                    ['>', 'ed.un_bind_time', $beginTime],
                    ['<=', 'ed.un_bind_time', $endTime]],
            ])
            ->select('ed.create_time,ed.un_bind_time,e.equip_code,b.build_number')
            ->asArray()
            ->all();
        $deliveryRecordList = [];
        foreach ($deliveryRecordArray as $delivery) {
            //楼宇点位设备是否为新设备类型
            $isNewType   = 0;
            $createTime  = $delivery['create_time'];
            $unBindTime  = $delivery['un_bind_time'];
            $equipCode   = $delivery['equip_code'];
            $buildNumber = $delivery['build_number'];
            //存在换机行为时，最后一次换机时间在12点之前默认显示新设备，12点之后默认显示旧设备
            //如果撤机或投放时间大于12点，默认设备不变,否则默认为新设备
            if ($unBindTime || $createTime > $TwelveClock) {
                $equipCode = '';
            }
            $deliveryRecordList[$buildNumber] = $equipCode;
        }
        return $deliveryRecordList;
    }

    /**
     * 获取所有楼宇点位的投放记录(按设备、只保留最早那次)
     * @author wangxiwen
     * @version 2018-11-01
     * @return
     */
    public static function getEquipDeliveryRecord()
    {
        $deliveryRecordArray = self::find()->orderBy('dr.create_time DESC')
            ->alias('dr')
            ->leftJoin('equipments e', 'e.id = dr.equip_id')
            ->select('e.equip_code,dr.create_time')
            ->asArray()
            ->all();
        $deliveryRecordList = [];
        foreach ($deliveryRecordArray as $record) {
            $equipCode = $record['equip_code'];
            $beginDate = date('Y-m-d', $record['create_time']);
            if ($equipCode == '') {
                continue;
            }
            $deliveryRecordList[$equipCode] = $beginDate;
        }
        return $deliveryRecordList;
    }

    /**
     * 获取所有楼宇点位的投放记录(按楼宇、只保留最早那次)
     * @author wangxiwen
     * @version 2018-11-01
     * @return
     */
    public static function getBuildDeliveryRecord()
    {
        $deliveryRecordArray = self::find()->orderBy('dr.create_time DESC')
            ->alias('dr')
            ->leftJoin('building b', 'b.id = dr.build_id')
            ->select('b.build_number,dr.create_time')
            ->asArray()
            ->all();
        $deliveryRecordList = [];
        foreach ($deliveryRecordArray as $record) {
            $buildNumber = $record['build_number'];
            $beginDate   = date('Y-m-d', $record['create_time']);
            if ($buildNumber == '') {
                continue;
            }
            $deliveryRecordList[$buildNumber] = $beginDate;
        }
        return $deliveryRecordList;
    }

}
