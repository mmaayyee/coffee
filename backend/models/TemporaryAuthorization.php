<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 16:23
 */
namespace backend\models;

use yii;
use yii\db\ActiveRecord;

class TemporaryAuthorization extends ActiveRecord
{
    public $orgId;
    public $orgType;
//    主管审核的状态  0-未审核  1-已授权  2-已拒绝  3-已失效
    const TOEXAMINE   = 0;
    const AUTHORIZED  = 1;
    const HAVEREFUSED = 2;
    const FAILED      = 3;

//    表的名称
    public static function tableName()
    {
        return 'temporary_authorization';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => '编号',
            'build_name'       => '楼宇名称',
            'wx_member_name'   => '用户名',
            'application_time' => '申请时间',
            'audit_time'       => '审核时间',
            'state'            => '状态',
            'orgType'          => '机构类型',
        ];
    }

    /**
     * 主管审核状态
     * [$state description]
     * @var [type]
     */
    public static $state = [
        ''                => '请选择',
        self::TOEXAMINE   => '待审核',
        self::AUTHORIZED  => '已授权',
        self::HAVEREFUSED => '已拒绝',
        self::FAILED      => '已失效',
    ];

    /**
     * 查看主管审核时间是否在一个小时之间
     * @author sulingling
     * @dateTime 2018-07-26
     * @version  [version]
     * @param    [array]     $where [description]
     * @return   [type]             [description]
     */
    public static function getOne($where)
    {
        $data = self::find()
            ->where($where)
            ->orderBy('audit_time desc')
            ->one();
        if ($data) {
            $difference = time() - ($data->audit_time);
            return $difference > yii::$app->params['bluetoothLockValidTime'] ? false : true;
        } else {
            return false;
        }
    }

    /**
     * 查看主管是否在审核
     * @author sulingling
     * @param Array() $where
     * @return boolean
     */
    public static function findWhere($where)
    {
        $data = self::find()
            ->where($where)
            ->one();
        return $data ? $data : false;
    }

    /**
     * 看申请时间是否超过规定的时间
     */
    public static function isApplicationTime($where)
    {
        $data = self::find()
            ->where($where)
            ->orderBy('application_time desc')
            ->one();
        if ($data) {
            $difference = time() - ($data->application_time);
            return $difference > yii::$app->params['bluetoothLockValidTime'] ? false : true;
        } else {
            return false;
        }
    }

    public function getStatusName()
    {
        if ($this->state == self::TOEXAMINE && time() - $this->application_time > Yii::$app->params['bluetoothLockValidTime']) {
            return self::$state[self::FAILED];
        }
        return self::$state[$this->state];
    }

    public static function getStatusNameByState($state, $applicationTime)
    {
        if ($state == self::TOEXAMINE && time() - $applicationTime > Yii::$app->params['bluetoothLockValidTime']) {
            return self::$state[self::FAILED];
        }
        return self::$state[$state];
    }

    public function isCanUpdate()
    {
        return !\Yii::$app->user->can('查看蓝牙锁') || $this->state !== self::TOEXAMINE || ($this->state == 0 && time() - $this->application_time > yii::$app->params['bluetoothLockValidTime']);
    }
}
