<?php
namespace backend\models;

use backend\models\Manager;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ChangePasswordForm extends Model
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
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($config = [])
    {
        $this->_user = Manager::findIdentity(Yii::$app->user->id);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','rePassword','currentPassword'], 'required'],

            [['password','rePassword','currentPassword'], 'string', 'min' => 6, 'max'=>20],

            ['rePassword', 'compare','compareAttribute'=>'password','message'=>'新密码与确认密码必须一致'],
            ['currentPassword', 'validatePassword'],
        ];
    }

    /**
     * 检查原密码是否正确
     * @param type $attribute
     * @param type $params
     */
    public function validatePassword($attribute, $params){
        if(!$this->_user->validatePassword($this->$attribute)){
            $this->addError($attribute, '原密码错误');
        }
    }
     
    
    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = $this->password;

        return $user->save();
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
