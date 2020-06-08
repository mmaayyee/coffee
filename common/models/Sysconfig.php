<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sysconfig".
 *
 * @property integer $config_id
 * @property string $config_key
 * @property string $config_value
 * @property string $config_desc
 */
class Sysconfig extends \yii\db\ActiveRecord
{
    
    /*
     * 可编辑状态
     */
    const  CANEDIT = 0;    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sysconfig';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config_key'], 'string', 'max' => 50],
            [['config_value', 'config_desc'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_id' => '配置ID',
            'config_key' => '配置键',
            'config_value' => '配置值',
            'config_desc' => '配置描述',
        ];
    }
    
    /**
     * 更新产品最新更新时间
     */
    public static function  updateProductLasttime(){
        
        $model = Sysconfig::findOne(['config_key'=>'product_last_update']);
        $model->config_value = (string)time();
        $rs = $model->save();
    }
    
    /**
     * 获取系统设置 
     * @param string $key 系统设置键
     * @return string 系统设置键值
     */
    public static function  getConfig($key){
        
        $model = Sysconfig::findOne(['config_key'=>$key]);
        return $model ? $model->config_value : 0;
    }     
    
}
