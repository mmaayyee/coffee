<?php

/**
 * Created by PhpStorm.
 * User: GYL
 * Date: 18/03/15
 * Time: 16:01
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JqueryAsset;

$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/regular_verification.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/shopGoods.js", ["depends" => [JqueryAsset::className()]]);


/* @var $this yii\web\View */
/* @var $model backend\models\ServiceQuestionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-question-search">
    <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-left">
        <?php if (Yii::$app->user->can('添加问题')) : ?>
            <li><a href="<?= Url::to(['/service-question/create']); ?>"><span class="glyphicon glyphicon-plus"></span> 添加问题</a></li>
        <?php endif ?>
         <?php if (Yii::$app->user->can('问题详情')) : ?>
            <li><a href="#" id="view_question"><span class="glyphicon glyphicon-eye-open"></span> 查看</a></li>
             <?php endif ?>
              <?php if (Yii::$app->user->can('修改问题')) : ?>
            <li><a href="#" id="update_question"><span class="glyphicon glyphicon-pencil"></span> 修改</a></li>

                 <?php endif ?>
                  <?php if (Yii::$app->user->can('删除问题')) : ?>
             <li><a href="javascript:void(0);" id="delete_question" onClick="deleteQuestion();"><span class="glyphicon glyphicon-minus"></span> 删除</a></li>  
                 <?php endif ?>
        </ul>
    </div>

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']); ?>
    <div class="form-group form-inline">
        <?= $form->field($model, 'question')->textInput() ?>
        <?= $form->field($model, 'question_key')->textInput() ?>
        <?= $form->field($model, 's_c_id')->dropDownList($category)?>
        <?=$form->field($model, 'static')->dropDownList(['' => '请选择'] + $model->getStatus())?>
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </nav>
</div>
<script type="text/javascript">
     function deleteQuestion(){
        if (selectCheck(2) && confirm('确定要删除选择的问题吗？')) {
           var questionID = [];
            checkList.each(function(){
                questionID.push(this.value);
            });
            console.log(questionID);
            $.post(
                "/service-question/delete",
                {"id":questionID},
                function(res){
                    res = JSON.parse(res);
                    if(res.code == '200'){
                        alert(res.message);
                        window.location.reload();
                    } else {
                        var msg = res.data ? res.data : res.message;
                        alert(msg);
                    }
                }
            )
        }
    }


    function selectCheck()
    {
        checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        var statusText=checkList.parent().siblings().find(".status").text();
        if (statusText != '下线') {
            alert("请选择下线的问题进行删除!");
            return false;
        }
        return true;
    }
</script>

<?php
$this->registerJS('
$("#view_question").on("click",function(){
        var checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        window.location.replace("'.Url::toRoute('service-question/view').'" + "?id=" + checkList.attr("value"));
});
$("#update_question").on("click",function(){
        var checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        window.location.replace("'.Url::toRoute('service-question/update').'" + "?id=" + checkList.attr("value"));
});

$("#check_goods").on("click",function(){
        var checkId = [];
        var checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length < 1){
            alert("请至少选中一项进行操作");
            return false;
        }

        $("input[name=\'selection[]\']:checked").each(function(){
          checkId.push(this.value);
        });
        window.location.replace("'.Url::toRoute('service-question/check').'" + "?id=" + checkId);
});
', View::POS_READY);

