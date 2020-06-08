<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LightBeltProductGroup;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord)
{
    $this->title = '添加灯带饮品组管理';
}else
{
    $this->title = '修改灯带饮品组管理';
}
$this->params['breadcrumbs'][] = ['label' => '灯带饮品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="light-belt-product-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_group_name')->textInput(['maxlength' => '50']) ?>

	<div class="form-group form-inline checkboxs">
        <?php foreach ($productArr as $id => $product) { ?>
            <div class="form-group form-inline">
                <?php if( isset($chooseProduct[$id]) && $chooseProduct[$id]){ ?>
                    <input type='checkbox' checked="checked" name='LightBeltProductGroup[choose_product][]' id='<?php echo $id; ?>' value='<?php echo $id; ?>'/>
                    <label style='margin-right:20px;' for='<?php echo $id; ?>'><?php echo $product; ?></label>
                <?php }else{ ?>
                    <input type='checkbox' name='LightBeltProductGroup[choose_product][]' id='<?php echo $id; ?>' value='<?php echo $id; ?>'/>
                    <label style='margin-right:20px;' for='<?php echo $id; ?>'><?php echo $product; ?></label>
                <?php } ?>
            </div>
        <?php } ?>
        <div class='help-block'></div>
	</div>
    <div class="form-group">
        <?= Html::Button($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs('
   $("button[type=button]").on("click", function() {
       if ($("#lightbeltproductgroup-product_group_name").val() == "") {
            $(".field-lightbeltproductgroup-product_group_name").addClass("has-error");
            $(".field-lightbeltproductgroup-product_group_name .help-block").text("请输入内容");
            return false ;
        } else {
            $(".field-lightbeltproductgroup-product_group_name").removeClass("has-error");
            $(".field-lightbeltproductgroup-product_group_name .help-block").text(" ");
        };
        if ($("input[type=checkbox]:checked").length < 1) {
            $(".checkboxs").addClass("has-error");
            $(".checkboxs .help-block").text("必须选中一款咖啡");
            return false ;
        } else {
            $(".checkboxs .help-block").text("");
            $(".checkboxs").removeClass("has-error");
        }
        $(this).attr("disabled",true);
        $("#w0").submit();
   });
');
?>
