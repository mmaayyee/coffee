<?php

namespace backend\models;

use backend\models\Manager;
use common\models\Api;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "product_offline_record".
 *
 * @property integer $id
 * @property string $build_id
 * @property string $equip_id
 * @property string $operator
 * @property string $create_time
 * @property string $product_id
 */
class ProductOfflineRecord extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    public $lock_from;
    public $build_id; // 楼宇编号

    const ON_SHELEVES  = 0; // 下架
    const OFF_SHELEVES = 1; // 上架

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_offline_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'create_time'], 'integer'],
            [['operator', 'equip_code', 'product_name', 'product_id'], 'string', 'max' => 255],
            [['start_time', 'end_time', 'lock_from', 'build_id'], 'safe'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Manager::className(), ['username' => 'operator']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'build_id'     => '楼宇名称',
            'equip_code'   => '设备编号',
            'start_time'   => '开始时间',
            'end_time'     => '结束时间',
            'operator'     => '操作人',
            'create_time'  => '操作时间',
            'product_name' => '产品名称',
            'type'         => '类型',
        ];
    }

    /**
     * 上下架来源
     */
    public static $lockFrom = array(
        ''  => '请选择',
        "0" => '设备端下架',
        "1" => '远程端下架',
    );

    /* 上下架类型
     * @var array
     */
    public static $shelvesType = array(
        ''                 => '请选择',
        self::ON_SHELEVES  => '下架',
        self::OFF_SHELEVES => '上架',
    );

    /**
     * 获取ajax获取的产品数据
     * @author  zmy
     * @version 2017-05-22
     * @param   [type]     $type       [上下架类型]
     * @param   [type]     $equipModel []
     * @return  [type]                 [设备对象]
     */
    public static function getProductInput($buildId, $type)
    {
        $equip       = Equipments::findOne(['build_id' => $buildId]);
        $productList = json_decode(Api::getProductOfflineList($equip->equip_code, $equip->pro_group_id, $type), true);
        $input       = "";
        foreach ($productList as $id => $product) {
            if ($product) {
                $input .= "<input type='checkbox' name='productIdArr[]' id='" . $id . "' value='" . $id . "'/><label style='margin-right:20px;' for='" . $id . "'>" . $product . "</label>";
            }
        }
        if ($input) {
            $input = $input . "<div class='help-block'></div>";
        } else {
            $input = "<input type='hidden' id='no_product' value='0' ><div style='color:#a94442;'>暂无产品数据，请先进行下架操作</div>";
        }
        return $input;
    }

    /**
     * 通过接口操作上下架产品，
     * @author  zmy
     * @version 2017-05-19
     * @param   [type]     $param [description]
     * @return  [type]            [description]
     */
    public static function getRetProductOffline($param)
    {
        $productArr                 = [];
        $equip                      = Equipments::findOne(['build_id' => $param['ProductOfflineRecord']['build_id']]);
        $productArr["equip_code"]   = $equip->equip_code;
        $productArr['productIdArr'] = $param['productIdArr'];
        $productArr['type']         = $param['ProductOfflineRecord']['type'];
        return Api::equipProductOfflineSync($productArr);
    }

    /**
     * 产品下架操作记录表  添加
     * @author  zmy
     * @version 2017-05-22
     * @param   [type]     $param    [参数值]
     * @param   [type]     $userName [用户名称]
     * @return  [type]               [true/false]
     */
    public static function createProductOffline($param, $userName)
    {
        $equip = Equipments::findOne(['build_id' => $param['ProductOfflineRecord']['build_id']]);

        $type           = isset($param['ProductOfflineRecord']['type']) ? $param['ProductOfflineRecord']['type'] : "";
        $productNameArr = explode(',', $param['productName']);

        $retResult = self::saveProOffRecord($equip->equip_code, json_encode($param['productIdArr']), $userName, $type, $param['productName']);
        return $retResult;
    }

    /**
     * 添加产品上下架记录表
     * @author  zmy
     * @version 2017-05-22
     * @param   [type]     $equipCode [设备编号]
     * @param   [type]     $productID [产品ID]
     * @param   [type]     $userName  [用户名称]
     * @param   [type]     $type      [类型]
     * @return  [type]                [添加结果]
     */
    public static function saveProOffRecord($equipCode, $productID, $userName, $type, $productName = '')
    {
        $model               = new ProductOfflineRecord();
        $model->equip_code   = $equipCode;
        $model->product_id   = $productID;
        $model->operator     = $userName;
        $model->create_time  = time();
        $model->type         = $type;
        $model->product_name = $productName;
        return $model->save();
    }

}
