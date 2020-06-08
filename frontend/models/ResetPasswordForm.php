<?php
namespace frontend\models;

use frontend\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $rePassword;
    public $token;
    
    /**
     * @var \common\models\User
     */
    private $_user;




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'rePassword', 'token',], 'required'],
            [['password', 'rePassword'], 'string', 'min' => 6],
            ['rePassword', 'compare','compareAttribute'=>'password','message'=>'新密码与确认密码必须一致'],
            ['token', 'validateToken'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => '密码',
            'rePassword'=>'确认密码',
            'token'=>'验证码',
        ];
    }    

 
    
    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = User::findOne(["openid"=>Yii::$app->session->get("openid")]);
        $user->password = $this->password;
        $user->removePasswordResetToken();

        return $user->save();
    }
}
