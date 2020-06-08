<?php
namespace backend\models;

use common\models\Api;
use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $city_id
 * @property string $city_name
 */
class DiscountHolicy extends \yii\db\ActiveRecord
{
    public $holicy_id;
    public $holicy_type;
    public $holicy_price;
    public $holicy_name;
    public $holicy_number;
    public $holicy_time;
    public $payment;
    public $holicy_payment;
    public $isNewRecord;
    public $holicy_status;
    public $holicy_introduction;

    //价格
    const HOLICY_TYPE_PRICE = 1;
    //折扣
    const HOLICY_TYPE_DIS = 2;
    //补贴
    const HOLICY_TYPE_SUBSIDY = 3;

    // 属性列表
    public $holicy_type_list = [
        ''                        => '全部',
        self::HOLICY_TYPE_PRICE   => '价格',
        self::HOLICY_TYPE_DIS     => '折扣',
        self::HOLICY_TYPE_SUBSIDY => '补贴',
    ];
    //待发布
    const HOLICY_RELEASED = 0;
    //已删除
    const HOLICY_DELETE = 2;
    // 状态列表
    public $holicy_delete_list = [
        self::HOLICY_RELEASED => '正常',
        self::HOLICY_DELETE   => '已删除',
    ];

    //银联闪付
    const PAYMENT_UNIONPAY = 0;
    //建行
    const PAYMENT_CCB = 1;
    //银联二维码
    const PAYMENT_UNIONQRCODE = 2;

    // 支付方式列表
    public $payment_list = [
        ''                        => '全部',
        self::PAYMENT_UNIONPAY    => '银联闪付',
        self::PAYMENT_CCB         => '建行龙卡支付',
        self::PAYMENT_UNIONQRCODE => '银联二维码',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['holicy_type', 'holicy_name', 'holicy_price'], 'required'],
            [['holicy_id', 'holicy_type', 'holicy_status', 'holicy_payment', 'holicy_number', 'holicy_time'], 'integer'],
            [['holicy_price'], 'double', 'min' => 1],
            [['holicy_name'], 'string', 'min' => 1, 'max' => 32],
            [['holicy_introduction'], 'string', 'max' => 3],
            ['holicy_name', "requiredByASpecial", 'on' => 'create'],
            ['holicy_name', "requiredByASpecialUpdate", 'on' => 'update'],
            ['holicy_price', "requiredByValidate", 'on' => ['create', 'update']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'holicy_type'         => '优惠策略类型',
            'holicy_id'           => '优惠id',
            'holicy_price'        => '优惠价格',
            'holicy_name'         => '策略名称',
            'holicy_number'       => '优惠楼宇',
            'holicy_time'         => '创建时间',
            'payment'             => '支付方式',
            'build_name'          => '楼宇',
            'holicy_payment'      => '支付方式',
            'holicy_status'       => '状态',
            'holicy_introduction' => '优惠策略说明',
        ];
    }
    /**
     *  自定义验证sale_name
     */
    public function requiredByASpecial($attribute, $params)
    {
        $params = array('DiscountHolicy' => array('holicy_name' => $this->holicy_name));
        if (Api::verifyDiscountHolicyCreate($params)) {
            $this->addError($attribute, "策略名称重复");
        }

    }
    /**
     *  编辑自定义验证sale_name
     */
    public function requiredByASpecialUpdate($attribute, $params)
    {
        $params = array('DiscountHolicy' => array('holicy_name' => $this->holicy_name, 'holicy_id' => $this->holicy_id));
        if (Api::verifyDiscountHolicyUpdate($params)) {
            $this->addError($attribute, "策略名称重复");
        }
    }
    /**
     *  编辑自定义验证sale_name
     */
    public function requiredByValidate($attribute, $params)
    {
        if ($this->holicy_type == 2) {
            if (floor($this->holicy_price) != $this->holicy_price) {
                $this->addError($attribute, "请输入一个整数");
            }
            if ($this->holicy_price > 100) {
                $this->addError($attribute, "请输入1-100之间的折扣");
            }
        } else {
            if ($this->holicy_price > 999999999.99) {
                $this->addError($attribute, "最大不能超过999999999.99");
            }
        }
    }
    /**
     * 获取优惠名称
     * @author  tuqiang
     * @version 2017-09-07
     * @return  string     $name  优惠名称
     */
    public function getHolicyTypeName($holicyType)
    {
        if (!$holicyType) {
            return '';
        } else {
            return $this->holicy_type_list[$holicyType];
        }
    }
    /**
     * 根据优惠id获取相关的优惠楼宇
     * @author  tuqinag
     * @version 2017-09-04
     * @param   array      优惠id
     * @return  array      优惠楼宇列表
     */
    public static function getDisBuildingList($batchId)
    {
        return Api::getDisBuildingList($batchId);
    }

    /**
     * 获取优惠策略详情
     * @author  tuqinag
     * @version 2017-09-04
     * @param   array      优惠id
     * @return  array      详情
     */
    public static function getHolicyInfo($params)
    {
        return Api::getHolicyInfo($params);
    }
    /**
     * 获取详情
     * @author  tuqiang
     * @version 2017-08-26
     * @param   integer
     * @return  object          任务详情
     */
    public static function getDiscountHolicyInfo($params)
    {
        $model = new DiscountHolicy();
        $info  = Api::getDiscountHolicyInfo($params);
        $model->load(['DiscountHolicy' => $info]);
        return $model;
    }

    /**
     * 获取支付方式
     * @author zhenggangwei
     * @date   2018-12-13
     * @param  string/integer     $isAll  0-全部 1-支持优惠策略的支付方式
     * @return array/string
     */
    public function getPaymentList($isAll = 0)
    {
        return PayTypeApi::getPayTypeIdNameList($isAll)['data'];
    }
}
