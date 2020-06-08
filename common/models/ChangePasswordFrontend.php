<?php
namespace common\models;

use backend\models\EquipRfidCard;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ChangePasswordFrontend extends Model
{
    /*
     *新密码
     */
    public $password;
    
    /*
     * 确认新密码
     */
    public $rePassword;    
    
    /*
     * 当前密码
     */
    public $currentPassword;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','rePassword','currentPassword'], 'required'],

            [['password','rePassword','currentPassword'], 'string', 'min' => 6, 'max'=>20],

            ['rePassword', 'compare','compareAttribute'=>'password','message'=>'新密码与确认密码必须一致'],
        ];
    }

    /**
     * 检查原密码是否正确
     * @param type $attribute
     * @param type $params
     */
    public static function validatePass($currentPassword, $rfidCardObj){
        //加密后的数据库密码
        $md5CurrentPassword   =   $rfidCardObj->rfid_card_pass;
        // 验证原密码是否正确
        if($md5CurrentPassword != md5($currentPassword)){
            return 7;
        }
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
           'password'=>'新密码',
            'currentPassword' => '原密码',
            'rePassword' => '确认新密码',            
        ];
    }     
}
