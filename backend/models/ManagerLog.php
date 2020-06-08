<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "manager_log".
 *
 * @property integer $log_id
 * @property integer $manager_id
 * @property integer $module_name
 * @property integer $operate_type
 * @property string $operate_content
 * @property integer $created_at
 */
class ManagerLog extends \yii\db\ActiveRecord
{
    
    /*
     * 申请起始日期
     */
    public $createdFrom;

    /*
     * 申请截止日期
     */
    public $createdTo;    
    
    /*
     * 真实姓名
     */
    public $realname; 
    
    /*
     * 创建
     */
    const CREATE = 0;
    
    /*
     * 编辑
     */
    const UPDATE = 1;
    
    /*
     * 删除
     */
    const DELETE = 2;
        
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manager_id', 'operate_type', 'created_at'], 'integer'],
            [['module_name', 'operate_content', 'created_at'], 'required'],
            [['operate_content', 'module_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * 用户表关联
     * @return User 用户
     */
    public function getManager(){  
        return $this->hasOne(Manager::className(),['id'=>'manager_id']); 
    }     
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'manager_id' => '管理员ID',
            'module_name' => '模块名称',
            'operate_type' => '操作类型',
            'operate_content' => '操作内容',
            'created_at' => '操作时间',
            'createdTo' => '截止日期',
            'createdFrom'=>'开始日期',
            'realname'=>'管理员姓名'
        ];
    }
    
    /**
     * 获取商品状态
     * @return string 商品状态
     */
    public function getType(){
        $statusArray = $this->getTypeArray();
        return $statusArray[$this->operate_type];
    }
    
    /**
     * 获取日志状态数组
     * @return array 日志状态数组
     */
    public function getTypeArray(){
        return array(
            ''=>'请选择',
            self::CREATE=>'添加',
            self::UPDATE=>'编辑',
            self::DELETE=>'删除',
        );
    }     
    
    /**
     * 保存返回失败记录
     * @param type $managerID 管理员ID
     * @param type $moduleName 模块名称
     * @param type $operateType 操作类型（0添加，1编辑,2删除）
     * @param type $operateContent 操作内容（如产品名称）
     * @return bool 保存结果
     */
    public static function saveLog($managerID, $moduleName, $operateType, $operateContent){
        $log = new ManagerLog();
        $log->manager_id = $managerID;
        $log->module_name = $moduleName;
        $log->operate_type = $operateType;
        $log->operate_content = $operateContent;
        $log->created_at = time();
        return $log->save();
    }    
}
