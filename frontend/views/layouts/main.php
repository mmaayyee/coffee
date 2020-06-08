<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\bootstrap\Progress;

/* @var $this \yii\web\View */
/* @var $content string */
//ios禁止双放大
Yii::$app->view->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no']);
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head();?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">


        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?php
            if (Yii::$app->getSession()->hasFlash('success')) {
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-success', //这里是提示框的class
                    ],
                    'body'    => Yii::$app->getSession()->getFlash('success'), //消息体
                ]);
            }
            if (Yii::$app->getSession()->hasFlash('error')) {
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-error',
                    ],
                    'body'    => Yii::$app->getSession()->getFlash('error'),
                ]);
            }
        ?>
        <?= $content ?>
        </div>
    </div>

    <!-- <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; 咖啡零点吧 <?= date('Y') ?></p>
        </div>
    </footer> -->

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
