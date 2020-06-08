<?php

namespace backend\models;
use Yii;
use common\models\Api;

/**
 * This is the model class for table "auth_item".
 *
 */
class Sale extends \yii\db\ActiveRecord
{
    public $sale_id;
    public $sale_name;
    public $sale_phone;
    public $isNewRecord;
    public $sale_email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_name', 'sale_phone','sale_email'], 'required'],
            ['sale_name', 'string', 'min' => 1,'max' => 20],
            ['sale_email', 'string', 'min' => 6, 'max' => 32],
            [['sale_id'], 'integer'],
            [['sale_phone','sale_email','sale_name'], 'string'],
            [['sale_phone'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],
            [['sale_id'],'safe'],
            [['sale_phone'], 'number'],
            ['sale_email','email'],
            ['sale_name', "requiredByASpecial",'on' => 'create'],
            ['sale_name', "requiredByASpecialUpdate",'on' => 'update'],
        ];
    }

    /**
     *  自定义验证sale_name
     */
    public function requiredByASpecial($attribute, $params)
    {   
        $params = array('Sale' => array('sale_name' => $this->sale_name));
        if(Api::verifySaleCreate($params)){
            $this->addError($attribute, "姓名已存在");
        }
        
    }
    /**
     *  编辑自定义验证sale_name
     */
    public function requiredByASpecialUpdate($attribute, $params)
    {   
        $params = array('Sale' => array('sale_name' => $this->sale_name,'sale_id' => $this->sale_id));
        if(Api::verifySaleUpdate($params)){
            $this->addError($attribute, "姓名已存在");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sale_name'     => '姓名',
            'sale_id'       => '姓名',
            'sale_email'    => '邮箱',
            'sale_phone'    => '手机号'
        ];
    }
    /**
     * 新增销售
     * 
     */
    public function saleCreate($params){
        if(Api::saleCreate($params)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 修改销售
     * 
     */
    public function saleUpdate($params){
        if(Api::saleUpdate($params)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取详情
     * @author  zgw
     * @version 2017-08-26
     * @param   integer     $id 任务id
     * @return  object          任务详情
     */
    public static function getSaleInfo($params)
    {
        $model    = new self();
        $info = Api::getSaleInfo($params);
        $model->load(['Sale' => $info]);
        return $model;
    }

    /**
     * 删除
     * 
     */
    public function saleDelete($params){
        if(Api::saleDelete($params)){
            return true;
        }else{
            return false;
        }
    }
}