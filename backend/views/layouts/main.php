<?php
use backend\assets\AppAsset;
use yii\bootstrap\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

if (Yii::$app->controller->id != 'scm-stock' && Yii::$app->controller->id != 'distribution-temporary-task' && Yii::$app->controller->id != 'distribution-task' && Yii::$app->controller->id != 'equip-warn' && Yii::$app->controller->id != 'equip-rfid-card' && Yii::$app->controller->id != 'special-permission' && Yii::$app->controller->id != 'scm-material') {
    $this->registerJsFile('@web/js/common.js?v=2.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
}
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=Html::csrfMetaTags()?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
    <style>
    .form-inline .form-group{
        vertical-align: top;
    }
    </style>
</head>
<body>
   
    <div class="wrap">
        

        <div class="container">
        <?=Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
])?>
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
        <?=$content?>
        </div>
    </div>

   

    <?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
<script type="text/javascript">
window.setTimeout(function() {
	$('.alert-success, .alert-error').hide();
}, 3000);
window.parent.onscroll = function(e){
    scrollModal();
}
function scrollModal(){
    if(self!=top){
        var scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop+50;
        // console.log("scrollTop..",scrollTop);
        $(".modal-content,.dialog").css({top: scrollTop+"px"});
    }
}
scrollModal();
</script>
