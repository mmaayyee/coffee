<?php
namespace frontend\models;

use frontend\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $sex = 0;
     public $email;
    public $password;
    public $repassword;
    public $verifycode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'uniqueCheck'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['repassword', 'required'],
            ['repassword', 'string', 'min' => 6],      
            ['repassword', 'compare','compareAttribute'=>'password','message'=>'密码与确认密码必须一致'],
            ['verifycode', 'required'],
            ['verifycode', 'integer', 'min' => 4],       
            ['verifycode', 'validateVerifycode'],
            ['sex', 'integer']
        ];
    }

    /**
     * 用户名唯一性检查
     * @param type $attribute
     * @param type $params
     */
    public function uniqueCheck($attribute, $params){
            $user = User::findOne(['username'=>$this->username]);
            if(!preg_match('/^(1)\d{10}$/',$this->username)) {
                $this->addError($attribute, '请输入正确的手机号');
            }
            if ($user) {
                $this->addError($attribute, '用户已存在');
            }        
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateVerifycode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne(["openid"=>Yii::$app->session->get("openid")]);
            if (!$user || !$user->validateVerifycode($this->verifycode)) {
                $this->addError($attribute, '错误的验证码');
            }
        }
    }    
    
        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '手机号',
            'password' => '密码',
            'repassword'=>'确认密码',
            'sex' => '性别',
            'verifycode'=> '验证码',
        ];
    }    

    
     /**
     * 获取性别数组
     * @return array 性别数组
     */
    public function getSexArray(){
        return array(
            '0'=>'女',
            '1'=>'男'
        );
    } 
    
}
