<?php

namespace backend\models;

use backend\models\EquipRfidCardAssoc;
use backend\models\Organization;
use common\models\Api;
use common\models\Equipments;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "equip_rfid_card".
 *
 * @property string $id
 * @property string $rfid_card_code
 * @property string $rfid_card_pass
 * @property string $member_id
 * @property string $org_id
 * @property integer $area_type
 * @property string $create_time
 */
class EquipRfidCard extends \yii\db\ActiveRecord
{
    public $equipId; //正常选中设备（编号）
    public $verificateCode; //手机端验证码
    public $startTime;
    public $endTime;
    public $repassword; // 确认密码
    public $ownedEquipCode; // 设备所在楼宇
    public $offEquipCode; // 离线指定设备

    // 门禁卡接口
    public $available; // 合法卡（判断是否为合法的卡）
    public $owner; // 指定运维人员
    public $open; // 是否开门
    public $msg; // 错误信息
    public $code; // 错误编码
    public $orgArr; // 机构名称数组

    // ($ret = '00', $msg = '', $available = false, $password = false, $owner = false, $permission = false, $open = false)
    /*
     *  RFID卡状态
     */
    // 正常
    const RFID_NORMAL = 0;
    // 禁用
    const RFID_DISABLE = 1;
    // 废除
    const RFID_ABOLITION = 2;

    /*
     *  区域类型 1-全国全部设备，2-分公司全部设备，3-分公司下部分，4-全国中部分
     */
    // 全国全部设备，
    const COUNTRY_ALL = 1;
    // 分公司全部设备，
    const BRANCH_ALL = 2;
    // 分公司下部分，
    const BRANCH_PART = 3;
    // 全国中部分
    const COUNTRY_PART = 4;

    public static $rfidState = [
        ''                   => '请选择',
        self::RFID_DISABLE   => '禁用',
        self::RFID_NORMAL    => '正常',
        self::RFID_ABOLITION => '作废',
    ];

    /**
     * 定义数组 areaTypeArr
     * [areaTypeArr description]
     * @author  zmy
     * @version 2016-12-06
     * @return  [type]     [description]
     */
    public static $areaType = [
        ''                 => '请选择',
        self::BRANCH_PART  => '分公司部分设备',
        self::COUNTRY_PART => '全国部分设备',
        self::BRANCH_ALL   => '分公司全部设备',
        self::COUNTRY_ALL  => '全国全部设备',
    ];

    /**
     * 蓝牙锁  0-授权  1-禁止
     */
    //授权
    const BLUETOOTHGRANT = 0;
    //禁止
    const BLUETOOTHPROHIBIT    = 1;
    public static $isBluetooth = [
        self::BLUETOOTHGRANT    => '授权',
        self::BLUETOOTHPROHIBIT => '禁止',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_rfid_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rfid_card_code'], 'required'],
            [['area_type', 'create_time', 'rfid_state', 'is_bluetooth'], 'integer'],
            [['member_id', 'rfid_card_code', 'rfid_card_pass', 'org_id'], 'string'],
            [['rfid_card_pass', 'repassword'], 'string', 'min' => 6],
            ['repassword', 'compare', 'compareAttribute' => 'rfid_card_pass', 'message' => '密码与确认密码必须一致'],
            [['rfid_card_code'], 'unique'],
            [['is_bluetooth'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'rfid_card_code' => 'RFID卡号',
            'rfid_card_pass' => 'RFID密码',
            'member_id'      => '绑定人',
            'org_id'         => '分公司',
            'area_type'      => '选择区域类型',
            'create_time'    => '创建时间',
            'equipId'        => '设备所在楼宇',
            'rfid_state'     => 'RFID卡状态',
            'startTime'      => '开始查询时间',
            'endTime'        => '结束查询时间',
            'repassword'     => '确认密码',
            'is_bluetooth'   => '蓝牙锁',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRfidAssoc()
    {
        return $this->hasOne(EquipRfidCardAssoc::className(), ['rfid_card_code' => 'rfid_card_code']);
    }

    /**
     * 获取门禁卡绑卡人姓名
     * @author wangxiwen
     * @param string $cardCode 卡号
     * @return string 绑定人姓名
     */
    public static function getCardInfo($cardCode)
    {
        return EquipRfidCard::find()
            ->alias('erc')
            ->leftJoin('wx_member wx', 'erc.member_id=wx.userid')
            ->where(['erc.rfid_card_code' => $cardCode])
            ->select('wx.name')
            ->scalar();
    }
    /**
     * 条件查询的记录
     * @author  zmy
     * @version 2016-12-12
     * @return  [type]     [description]
     */
    public static function getRfifCardCodeArr($field = '*', $where = array())
    {
        return self::find()->select($field)->where($where)->all();
    }

    /**
     * 获取所有符合条件的卡号数组
     * @author  zmy
     * @version 2016-12-22
     * @return  [type]     [description]
     */
    public static function getRfidCodeAllArr()
    {
        $rfidCodeArr     = self::find()->where(['rfid_state' => self::RFID_NORMAL])->asArray()->all();
        $rfidCardCodeArr = [];
        foreach ($rfidCodeArr as $key => $value) {
            $rfidCardCodeArr[$value['rfid_card_code']] = $value['rfid_card_code'];
        }
        return $rfidCardCodeArr;
    }

    /**
     * 获取卡号列表
     * @author  zmy
     * @version 2016-12-12
     * @return  [type]     [description]
     */
    public static function getRfidCardCodeArr($where = array())
    {
        $rfidList         = self::getRfifCardCodeArr('rfid_card_code', $where);
        $RfifCardCodeList = array('' => '请选择');
        foreach ($rfidList as $key => $value) {
            $RfifCardCodeList[$value->rfid_card_code] = $value->rfid_card_code;
        }
        return $RfifCardCodeList;
    }

    /**
     *  处理RFID数据 插入卡表
     * [createRfidCard description]
     * @author  zmy
     * @version 2016-12-06
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function operationRfidCardData($param, $rfidObj, $sign = 'create')
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //if 修改
        if ($sign == 'update') {
            $rfidCardObj = self::judgeRfidCordOrMember($param, $rfidObj, $transaction);
            if (!$rfidCardObj) {
                return 0;
            }
        } else {
            if (!self::checkRfidCordOrMember($param, $transaction)) {
                return 0;
            }
        }

        $model               = self::createRfidCardParam($param, $rfidObj, $sign);
        $model->is_bluetooth = $param['is_bluetooth'];
        // if 修改 删除assoc 信息，重新添加
        if ($sign == 'update' && $rfidCardObj) {
            $retDeleteRfidAssoc = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $rfidCardObj->rfid_card_code])->all();
            if ($retDeleteRfidAssoc) {
                self::deleteRfidAssoc($rfidCardObj);
            }
        }
        if ($param["area_type"] == 1) {
            // 全国
            $transaction->commit();
            return $model->save();
        } else {
            // 2、分公司全部  3、分公司部分 4、全国部分
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', '对不起，添加门禁卡失败。');
                $transaction->rollBack();
                return 0;
            }
            if ($param["area_type"] && $param["area_type"] != 2) {
                //分公司全部，则查询，循环插入
                if ($param['ownedEquipCode'] || $param['offEquipCode']) {
                    $retCreateRfidAssoc = self::createRfidCardAssoc($param, $transaction, $rfidObj);
                    if (!$retCreateRfidAssoc) {
                        Yii::$app->getSession()->setFlash('error', '对不起，门禁卡关联表插入失败。');
                        $transaction->rollBack();
                        return 0;
                    }
                }

            }
        }
        //事务通过
        $transaction->commit();
        return 1;
    }

    /**
     * 修改时判断是否有此指定人员
     * [judgeRfidCordOrMember description]
     * @author  zmy
     * @version 2016-12-09
     * @param   [type]     $param       [description]
     * @param   [type]     $rfidObj     [description]
     * @param   [type]     $transaction [description]
     * @return  [type]                  [description]
     */
    public static function judgeRfidCordOrMember($param, $rfidObj, $transaction)
    {
        $isSame = true;
        //传输的值是否为空
        if (!empty($param['member_id'])) {
            // 不为空  2.id是否相同， （不同为 数据库中已有）
            // 检测数据库中是否有此人
            $rfidCardarr = self::find()->where(['member_id' => $param['member_id']])->asArray()->all();
            if ($rfidCardarr) {
                // 看id是否相同
                $arr = [];
                foreach ($rfidCardarr as $key => $value) {
                    $arr[] = $value['member_id'];
                }
                if ($param['member_id'] != $rfidObj->member_id && in_array($param['member_id'], $arr)) {
                    // 有相同的人员
                    $isSame = false;
                }
            }
        }
        if ($isSame) {
            return $rfidObj;
        } else {
            Yii::$app->getSession()->setFlash('error', '对不起，人员重复。');
            $transaction->rollBack();
            return 0;
        }

    }

    /**
     * 检查卡号与人员是否重复
     * [checkRfidCord description]
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $param       [页面传输参数]
     * @param   [type]     $transaction [事物]
     * @return  [type]                  [true]
     */
    public static function checkRfidCordOrMember($param, $transaction)
    {
        $rfidCardCodeObj = self::find()->where(['and', "member_id!=''", ['rfid_card_code' => $param['rfid_card_code']]])->asArray()->one();
        $rfidMemberIdObj = self::find()->where(["and", "member_id!=''", ['member_id' => $param['member_id']]])->asArray()->one();
        if ($rfidCardCodeObj && !$rfidMemberIdObj) {
            Yii::$app->getSession()->setFlash('error', '对不起，卡号重复。');
            $transaction->rollBack();
            return false;
        }
        if ($rfidMemberIdObj && !$rfidCardCodeObj) {
            Yii::$app->getSession()->setFlash('error', '对不起，人员重复。');
            $transaction->rollBack();
            return false;
        }
        if ($rfidMemberIdObj && $rfidCardCodeObj) {
            Yii::$app->getSession()->setFlash('error', '对不起，卡号和人员重复。');
            $transaction->rollBack();
            return false;
        }
        return true;
    }
    /**
     * 创建model 中参数值
     * [createRfidCardParam description]
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $param [description]
     * @param   [type]     $model [description]
     * @return  [type]            [description]
     */
    public static function createRfidCardParam($param, $model, $sign = "create")
    {
        if (isset($param["rfid_card_code"])) {
            $model->rfid_card_code = $param["rfid_card_code"];
        }

        $model->area_type = $param["area_type"] ? $param["area_type"] : "0";
        if ($sign != 'update') {
            $model->create_time    = time();
            $model->rfid_card_pass = $param['rfid_card_pass'] ? md5($param['rfid_card_pass']) : "";
        } else {
            if ($param['rfid_card_pass']) {
                $model->rfid_card_pass = md5($param['rfid_card_pass']);
            }
        }
        $model->repassword = $param['repassword'] ? md5($param['repassword']) : "";
        $model->rfid_state = $param['rfid_state'];
        if ($param['area_type'] == self::COUNTRY_ALL || $param['area_type'] == self::COUNTRY_PART) {
            // 全国
            $model->org_id = ",1,";
        } else {
            if ($param['area_type']) {
                $model->org_id = ',' . implode(',', $param['org_id']) . ',';
            } else {
                $model->org_id = ",0,";
            }
        }
        $model->member_id = isset($param["member_id"]) ? $param['member_id'] : "";
        return $model;
    }

    /**
     * 删除指定assoc数据 通过rfid_card_code
     * [deleteRfidAssoc description]
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $rfidCardObj [description]
     * @return  [type]                  [description]
     */
    public static function deleteRfidAssoc($rfidCardObj)
    {
        return EquipRfidCardAssoc::deleteALl(['rfid_card_code' => $rfidCardObj->rfid_card_code]);
    }

    /**
     * 添加入RFID Assoc表中 1个门禁卡号可以设置多台设备离线，但一个设备只有一个离线开门的卡号
     * @author  zmy
     * @version 2016-12-08
     * @param   [type]     $param       [description]
     * @param   [type]     $transaction [description]
     * @return  [type]                  [description]
     */
    public static function createRfidCardAssoc($param, $transaction, $rfidObj)
    {
        // 查询数据，修改assoc表
        $retAssocSave = true;
        // 先查询符合条件的设备，修改离线开门=0  不可作为判断条件，没有可修改时，也为0.
        $retUpdateRfidAssoc = EquipRfidCardAssoc::updateAccordEquipCode($param);
        if (!$rfidObj) {
            $rfidCardCode = $param['rfid_card_code'];
        } else {
            $rfidCardCode = $rfidObj->rfid_card_code;
        }
        // 组合数组，
        $equipCodeArr = EquipRfidCardAssoc::getCombinationArr($param, $rfidCardCode);
        if (!$equipCodeArr) {
            $transaction->rollBack();
            $retAssocSave = false;
        }
        // 批量插入数据库中
        $retUpdate = Yii::$app->db->createCommand()->batchInsert(EquipRfidCardAssoc::tableName(), ['rfid_card_code', 'is_designated_person', 'equip_code'], $equipCodeArr)->execute();
        if (!$retUpdate) {
            $retAssocSave = false;
            $transaction->rollBack();
        }

        return $retAssocSave;
    }

    /**
     * 根据org_id 查询数据，插入assoc
     * [createRfidAssocByOrgId description]
     * @param param       参数
     * @param transaction 事务
     * @author  zmy
     * @version 2016-12-06
     * @param   [type]     $param       [description]
     * @param   [type]     $transaction [description]
     * @return  [type]                  [description]
     */
    public static function createRfidAssocByOrgId($param, $transaction, $rfidObj = '')
    {
        $equipArr = Equipments::find()->where(['and', ['org_id' => $param['org_id']], ['not', ['build_id' => 0]], ['not', ['is_unbinding' => 0]]])->asArray()->all();
        if ($equipArr) {
            $orgIdSign = true;
            foreach ($equipArr as $key => $value) {
                $modelAssoc             = new EquipRfidCardAssoc();
                $modelAssoc->equip_code = $value['equip_code'];
                if (!$rfidObj) {
                    $modelAssoc->rfid_card_code = $param['rfid_card_code'];
                } else {
                    $modelAssoc->rfid_card_code = $rfidObj->rfid_card_code;
                }
                if (!$modelAssoc->save()) {
                    $orgIdSign = false;
                }
            }
            if (!$orgIdSign) {
                Yii::$app->getSession()->setFlash('error', '对不起，设备卡号关联表失败.');
                $transaction->rollBack();
                return 0;
            }
            return 1;
        } else {
            Yii::$app->getSession()->setFlash('error', '对不起，该分公司下无设备数据');
            $transaction->rollBack();
            return 0;
        }
    }

    /**
     * 获取门禁卡号
     * @return array 分公司数据
     */
    public static function getRfidCardArray($type = 0)
    {
        $allArray     = [];
        $rfidCardList = self::find()->where(["rfid_state" => 0])->all();
        if ($type == 1) {
            $allArray[0] = '请选择';
        }

        foreach ($rfidCardList as $rfidCard) {
            $allArray[$rfidCard->rfid_card_code] = $rfidCard->rfid_card_code;
        }
        return $allArray;
    }

    /**
     * 获取特殊许可RFID卡密码
     * @param  verificateCode   输入的验证码
     * @param  userid           微信当前用户
     * @author  zmy
     * @version 2016-12-07
     * @return  [type]     [description]
     */
    public static function getSpecialPermissionPass($verificateCode, $memberId, $equipCode, $textSign = '')
    {
        //检验是否为正确的设备编号
        $equip = Equipments::equip($equipCode);
        if (!$equip) {
            return 11;
        }
        // 特定人员，状态正常
        $rfidCardObj = self::rfidCardObj($memberId);
        if (!$rfidCardObj) {
            return false;
        }

        $equipIdArr = EquipRfidCardAssoc::getRfidAssocArr($rfidCardObj->rfid_card_code);

        if ($rfidCardObj->area_type != self::COUNTRY_ALL) {
            // 不是全国类型
            if ($rfidCardObj->area_type == self::BRANCH_ALL) {
                // 如果是分公司, 判断这个设备编号是否在改分公司下
                $equipObj = Equipments::getEquipmentsDetail('*', ['equip_code' => $equipCode]);
                $orgIdStr = trim($rfidCardObj->org_id, ',');
                $orgId    = explode(',', $orgIdStr);
                if (empty($equipObj) || !in_array($equipObj['org_id'], $orgId)) {
                    return 10;
                }
            } else {
                if (!in_array($equipCode, $equipIdArr)) {
                    // 判断不在assoc中
                    return 9;
                }
            }

        }

        // 特殊开门生成规则
        $md5Str = self::generatePass($equipCode, (int) $rfidCardObj->rfid_card_code, (int) $verificateCode, $textSign);
        return array('md5Str' => $md5Str, 'orgId' => $rfidCardObj->org_id);
    }

    /**
     * 输入设备编号，卡号，验证码，生成特殊开门密码（6位）
     * @author  zmy
     * @version 2016-12-29
     * @param   [string]     $equipCode      [设备编号]
     * @param   [string]     $rfidCard       [门禁卡号]
     * @param   [string]     $verificateCode [验证码]
     * @param   [string]     $textSign       [测试标识]
     * @return  [string]                     [密码串]
     */
    public static function generatePass($equipCode, $rfidCard, $verificateCode, $textSign = '')
    {
        // 判断环境，加密后的标识
        if (Yii::$app->params['coffeeUrlSign'] == 2) {
            // 正式环境
            $md5Str = md5($equipCode . ($rfidCard ^ $verificateCode));
        } else {
            // 测试及开发环境
            if ($textSign) // 是否测试标识
            {
                $md5Str = md5('coffeeTest' . $equipCode . ($rfidCard ^ $verificateCode));
            } else {
                $md5Str = md5($equipCode . ($rfidCard ^ $verificateCode));
            }
        }
        $passStr = strtoupper(substr($md5Str, -6));
        return $passStr;
    }

    /**
     * 整合方法，处理返回的错误信息
     * @author  zmy
     * @version 2016-12-22
     * @return  [type]     [description]
     */
    public static function retPrompt($param, $rfidCardObj, $error)
    {
        $retPassStrArr = EquipRfidCard::getSpecialPermissionPass($param['verificateCode'], $rfidCardObj->member_id, $param['equipId']);
        if (!$retPassStrArr) {
            // 此用户未在后台绑定
            $error = '对不起，此用户或设备未在后台绑定';
        } else if ($retPassStrArr == 9) {
            $error = '对不起，此用户未绑定本台设备';
        } else if ($retPassStrArr == 10) {
            $error = '对不起，此楼宇设备不在' . Organization::getField('org_name', $rfidCardObj->org_id) . '下';
        }
        return array('error' => $error, "retPassStrArr" => $retPassStrArr);
    }

    /**
     * 生成特殊开门密码，并且返回错误信息（全国符合条件的楼宇
     * @author  zmy
     * @version 2017-01-06
     * @param   [type]     $param       [description]
     * @param   [type]     $rfidCardObj [description]
     * @param   [type]     $error       [description]
     * @return  [type]                  [description]
     */
    public static function retGeneratePass($param, $rfidCardObj, $error)
    {
        // 特定人员，状态正常
        $rfidCardObj = EquipRfidCard::find()->where(['member_id' => $rfidCardObj->member_id, 'rfid_state' => self::RFID_NORMAL])->one();
        if (!$rfidCardObj) {
            return $error = '对不起，此设备编号不在' . Organization::getField('org_name', $rfidCardObj->org_id) . '下';
        }
        $textSign = (isset($param['text_sign']) && $param['text_sign']) ? $param['text_sign'] : "";
        // 特殊开门生成规则
        $md5Str        = self::generatePass($param['equipId'], (int) $rfidCardObj->rfid_card_code, (int) $param['verificateCode'], $textSign);
        $retPassStrArr = array('md5Str' => $md5Str, 'orgId' => $rfidCardObj->org_id);
        return array('error' => $error, "retPassStrArr" => $retPassStrArr);
    }

    /**
     * 检测卡号是否正常
     * @author  zmy
     * @version 2016-12-12
     * @param   [type]     $cardCode [卡号]
     * @return  [type]               [true/false]
     */
    public static function checkRfidCord($cardCode)
    {
        $rfidCardObj = EquipRfidCard::find()->where(['rfid_card_code' => $cardCode, 'rfid_state' => EquipRfidCard::RFID_NORMAL])->one();
        return $rfidCardObj;
    }

    /**
     * 获取当前用户的符号条件的数组（设备编号=》楼宇名称）
     * @author  zmy
     * @version 2016-12-14
     * @param   [type]     $rfidCardCode [门禁卡号]
     * @param   [type]     $equipArr     [设备数组]
     * @return  [type]                   [数组]
     */
    public static function getEquipListArr($rfidCardCode, $equipArr)
    {
        $rfidCardAssocArr = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $rfidCardCode])->asArray()->all();
        $equipListArr     = [];
        foreach ($equipArr as $equipkey => $equipVal) {
            foreach ($rfidCardAssocArr as $key => $value) {
                if ($equipkey == $value['equip_code']) {
                    $equipListArr[$value['equip_code']] = $value['equip_code'];
                }
            }
        }
        return $equipListArr;
    }

    /**
     * 根据用户获取不同条件下的数组 （设备编号->楼宇名称）
     * @author  zmy
     * @version 2016-12-14
     * @param   [type]     $userid      [用户ID]
     * @param   [type]     $rfidCardCode[门禁卡号]
     * @return  [type]                  [数组，对象]
     */
    public static function getRfidAssocEquipCodeArr($userid = '', $rfidCardCode = '')
    {
        if ($rfidCardCode) {
            $rfidCardObj = EquipRfidCard::find()->where(['rfid_card_code' => $rfidCardCode, 'rfid_state' => EquipRfidCard::RFID_NORMAL])->one();
        } else {
            // 后台添加搜素门禁卡号
            $rfidCardObj = EquipRfidCard::find()->where(['member_id' => $userid, 'rfid_state' => EquipRfidCard::RFID_NORMAL])->one();
        }
        if (!$rfidCardObj) {
            return false;
        }
        if ($rfidCardObj->area_type == 1) {
            // 全国
            $equipArr = Equipments::getEquipArr("", 1);
        } else {
            $orgId    = explode(',', trim(',', $rfidCardObj->org_id));
            $equipArr = Equipments::getEquipArr($orgId, 1);
            if ($rfidCardObj->area_type == 3 || $rfidCardObj->area_type == 4) {
                $equipArr = EquipRfidCard::getEquipListArr($rfidCardObj->rfid_card_code, $equipArr);
            }
        }
        return array('equipArr' => $equipArr, 'rfidCardObj' => $rfidCardObj);
    }

    /**
     * 获取字符串转换的数组； 以，, '' 换行 分割数组
     * @author  zmy
     * @version 2016-12-21
     * @param   [type]     $strs [传输的字符串]
     * @return  [type]     Array [数组]
     */
    public static function getStrTransforArr($strs)
    {
        $rfidCodeStrs    = $strs;
        $rfidCodeStrs    = str_replace('，', ',', $rfidCodeStrs);
        $rfidCodeStrs    = str_replace("\r\n", ',', $rfidCodeStrs);
        $rfidCodeStrs    = str_replace(' ', ',', $rfidCodeStrs);
        $rfidCardCodeArr = explode(',', trim($rfidCodeStrs));
        return $rfidCardCodeArr;
    }

    /**
     * 获取查询门禁卡号的结果（判断是否有重复号码）
     * @author  zmy
     * @version 2016-12-21
     * @return  [type]     [description]
     */
    public static function getRetSelectRfidCard($rfidCode)
    {
        $rfidCardArr = self::find()->where(['rfid_card_code' => $rfidCode])->asArray()->one();
        if ($rfidCardArr) {
            if ($rfidCardArr['rfid_card_code'] == $rfidCode) {
                return $rfidCode;
            }
        }
        return 'success';
    }

    /**
     * 初始化存入门禁卡号
     * @author  zmy
     * @param [type] rfidCode 门禁卡号 [<description>]
     * @version 2016-12-21
     * @param   [type]     $rfidCode [description]
     * @return  [type]               [description]
     */
    public static function rfidCodeSaveInit($rfidCode)
    {
        $equipRfidCardObj                 = new EquipRfidCard();
        $equipRfidCardObj->rfid_card_code = $rfidCode;
        $equipRfidCardObj->create_time    = time();
        $equipRfidCardObj->rfid_state     = EquipRfidCard::RFID_DISABLE;
        return $equipRfidCardObj->save();

    }

    /**
     *  特殊开门检测条件判断
     * @author  zmy
     * @version 2016-12-28
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function checkWhereJudgment($param, $error = '')
    {
        if (!isset($param['verificateCode']) || !$param['verificateCode']) {
            $error = '对不起，请输入验证码';
        }
        if (!isset($param['equipId']) || !$param['equipId']) {
            $error = '对不起，请输入设备编号';
        }
        // 查询rfid卡表中是否有符合的门禁卡号 状态：正常：用户名不为空
        $rfidCardObj = EquipRfidCard::find()->where(['and', ['rfid_card_code' => $param['rfid_card_code'], 'rfid_state' => EquipRfidCard::RFID_NORMAL], ['not', ['member_id' => null]]])->one();
        if (!$rfidCardObj) {
            $error = '对不起，请输入正确的门禁卡号';
        }
        return array('error' => $error, 'rfidCardObj' => $rfidCardObj);
    }

    /**
     *  要删除的人员，是否存在于门禁卡中，存在，则禁用
     * @author  zmy
     * @version 2016-12-29
     */
    public static function IsExistWxMemberId($userId)
    {
        $rfidObj = EquipRfidCard::find()->where(['member_id' => $userId])->one();
        if ($rfidObj) {
            $rfidObj->rfid_state = EquipRfidCard::RFID_DISABLE;
            $rfidObj->member_id  = '';
            if (!$rfidObj->save()) {
                return false;
            }
        }
        return true;
    }

    /**
     * 返回设备端门禁卡是否开门信息
     * @author  zmy
     * @version 2017-07-31
     * @param   [string]     $equipmentCode [设备编号]
     * @param   [string]     $cardCode      [卡号]
     * @param   [string]     $endPassword   [传输的加密密码]
     * @return  [json string]               [开门信息json字符串]
     */
    public static function retOpenRfidRes($equipmentCode, $cardCode, $endPassword)
    {
        // 初始化变量
        $available = false;
        if (!$equipmentCode || !$equipmentCode || !$cardCode || !$cardCode) {
            return Api::retResult('1001', '设备编号或卡号为空。');
        }

        $equip = Equipments::findOne(['equip_code' => $equipmentCode]);
        if (!$equip) {
            return Api::retResult('1002', '设备编号错误。');
        }
        // 检测卡号是否合法
        $rfidCardObj = self::checkRfidCord($cardCode);

        if (!$rfidCardObj) {
            return Api::retResult('1003', '此卡不合法。');
        }
        $available = true;

        if ($rfidCardObj->rfid_state != self::RFID_NORMAL) {
            return Api::retResult('1004', '此卡状态不正常。', $available);
        }

        //判断是否有人员，没有则无法开门
        if (!$rfidCardObj->member_id) {
            return Api::retResult('1005', '未绑定人员。', $available);
        }

        // 判断环境，加密后的标识
        if (Yii::$app->params['coffeeUrlSign'] == 2) {
            // 正式环境
            $sourceEncPassword = sha1($cardCode . $rfidCardObj->rfid_card_pass);
            if ($sourceEncPassword != $endPassword) {
                return Api::retResult('1006', '此卡密码错误。', $available);
            }
        } else {
            // 测试及开发环境
            $sourceEncPassword     = sha1("coffeeTest" . $cardCode . $rfidCardObj->rfid_card_pass);
            $sourceEncPasswordText = sha1($cardCode . $rfidCardObj->rfid_card_pass);
            if ($sourceEncPassword != $endPassword && $sourceEncPasswordText != $endPassword) {
                return Api::retResult('1006', '此卡密码错误。', $available);
            }
        }
        $wxMemberObj = WxMember::getMemberDetail('*', ['is_del' => 1, 'userid' => $rfidCardObj->member_id]);
        if (!$wxMemberObj) {
            return Api::retResult('1007', '此卡绑定人员已删除。', $available);
        }

        $rfidCardObj->available = $available;
        $rfidCardObj->owner     = false;
        $rfidCardObj->open      = true;
        $rfidCardObj->code      = "00";
        $rfidCardObj->msg       = '';

        // 判断是否为离线开门人员
        $rfidCardObj = EquipRfidCardAssoc::getIsOwnerEquipOpen($rfidCardObj, $equipmentCode);

        // 判断门禁卡权限，并返回rfidCardObj
        $rfidCardObj = EquipRfidCardAssoc::judgePermissions($equipmentCode, $rfidCardObj);
        if ($rfidCardObj->code == '00') {
            // 写入门禁开门日志
            $rfidCardObj = EquipRfidCardRecord::retCreateRfidRecord($rfidCardObj, $equipmentCode);

        }
        return Api::retResult($rfidCardObj->code, $rfidCardObj->msg, $rfidCardObj->available, $rfidCardObj->owner, $rfidCardObj->open);
    }

    /**
     * 判断该用户是否有开门的权限
     * @author sulingling
     * @version 2018-3-25
     * @param  Object  $wxMember  该用户的所有信息
     * @param  Array()  $equipmentData 设备表的信息
     * @return boolean true [有该权限]  false [没有该权限]
     * ->andWhere(['!=','rfid_state',self::RFID_DISABLE ])
     */
    public static function isOpenDoorJurisdiction($wxMember, $equipmentData)
    {
        $equipRfidCardData = self::find()
            ->where(['member_id' => $wxMember->userid])
            ->one();
        if (empty($equipRfidCardData) || $equipRfidCardData->is_bluetooth == 1) {
            return false;
        } else if ($equipRfidCardData->area_type == self::COUNTRY_ALL) {
            return true;
        } else if ($equipRfidCardData->area_type == self::BRANCH_ALL) {
            $orgIdStr = trim($equipRfidCardData->org_id, ',');
            $orgIdArr = explode(',', $orgIdStr);
            return in_array($equipmentData['org_id'], $orgIdArr) ? true : false;
        } else if ($equipRfidCardData->area_type == self::BRANCH_PART || $equipRfidCardData->area_type == self::COUNTRY_PART) {
            $equipRfidCardAssocData = EquipRfidCardAssoc::find()
                ->where(['equip_code' => $equipmentData['equip_code'], 'rfid_card_code' => $equipRfidCardData->rfid_card_code])
                ->one();
            return $equipRfidCardAssocData ? true : false;
        } else {
            return false;
        }
    }

    /**
     * 根据分公司ID，获取table表格, 显示在页面
     * @author  sulingling
     * @version 2018-06-21
     * @param   [Array]     $orgId [分公司ID]
     * @return  [string]                           [组合的div数据]
     */
    public function getOrgName($orgId)
    {
        $this->orgArr   = Api::getOrgIdNameArray();
        $orgIdStr       = trim($orgId, ',');
        $orgIdArr       = explode(',', $orgIdStr);
        $progressBarStr = "<table class='table table-striped table-bordered'>";
        foreach ($orgIdArr as $orgId) {
            $orgName = $this->orgArr[$orgId] ?? '';
            $progressBarStr .= $orgId ? ($orgId == '1' ? '<tr><td>全国</td></tr>' : "<tr><td>" . $orgName . "</td></tr>") : '';
        }
        $progressBarStr .= "</table>";
        return $progressBarStr;
    }
    /**
     * 获取门禁卡信息
     */
    public static function getCardUser($cardCode)
    {
        if (!$cardCode) {
            return '';
        }
        $cardObject = self::findOne(['rfid_card_code' => $cardCode]);
        if ($cardObject == null) {
            return '';
        }
        return $cardObject->member_id;
    }

    /**
     *  根据人员ID获取设备门禁卡的详细信息
     * @author sulingling
     * @dateTime 2018-08-15
     * @version  [version]
     * @param    int()     $memberId  [description]
     * @return   [type]               [description]
     */
    private static function rfidCardObj($memberId)
    {
        return self::find()
            ->where(['member_id' => $memberId, 'rfid_state' => self::RFID_NORMAL])
            ->one();
    }
}
