<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

您好 <?= Html::encode($user->username) ?>,

请点击以下链接重设密码：

<?= Html::a(Html::encode($resetLink), $resetLink) ?>
