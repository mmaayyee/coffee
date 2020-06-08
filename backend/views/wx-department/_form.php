<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Organization;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use common\models\WxDepartment;

/* @var $this yii\web\View */
/* @var $model backend\models\WxDepartment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-department-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 30]) ?>
    

    <?= $form->field($model, 'org_id')->dropDownList(Organization::getBranchArray(),['data-url'=>'parent-depart-list']) ?>
        
    <?= $form->field($model, 'parentid')->dropDownList([''=>'请选择'],['data-id'=>$model->parentid]) ?>

    <?= $form->field($model, 'headquarter')->dropDownList(WxDepartment::$headquarter) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
    $this->registerJs('
        $("input[name=\'checkall\']").click(function(){
            if (this.checked){
                $("input[name=\'WxDepartment[org_id][]\']").each(function(){this.checked=true});
            }else{
                $("input[name=\'WxDepartment[org_id][]\']").each(function(){this.checked=false});
            }
        })

        $.get(
            $("#wxdepartment-org_id").data("url"),
            {org_id:$("#wxdepartment-org_id").val(),depart_id:$("#wxdepartment-parentid").data("id")},
            function(data){
                $("#wxdepartment-parentid").html(data);
            }
        )

        $("#wxdepartment-org_id").change(function(){
            $.get(
                $(this).data("url"),
                {org_id:$(this).val(),depart_id:$("#wxdepartment-parentid").data("id")},
                function(data){
                    $("#wxdepartment-parentid").html(data);
                }
            )
        })
    ');
?>
