<?php
namespace backend\models;

use backend\models\Manager;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => Manager::STATUS_ACTIVE],
                'message' => '用户不存在.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => Manager::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            if (!Manager::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
                    ->setFrom(\Yii::$app->params['supportEmail'])
                    ->setTo($this->email)
                    ->setSubject('重设密码： ' . \Yii::$app->name)
                    ->send();
            }
        }

        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
           'email'=>'电子邮件',
        ];
    }     
}
