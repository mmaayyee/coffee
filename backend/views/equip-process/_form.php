<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipProcess */
/* @var $form yii\widgets\ActiveForm */

$this->title = '设备工序管理';
$this->params['breadcrumbs'][] = ['label' => '设备工序', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile("/css/jquery.minicolors.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/jquery.minicolors.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('
//调用取色器
    $("#equipprocess-process_color").each( function() {
        $(this).minicolors({
            control: $(this).attr("data-control") || "hue",
            defaultValue: $(this).attr("data-defaultValue") || " ",
            inline: $(this).attr("data-inline") === "true",
            letterCase: $(this).attr("data-letterCase") || "lowercase",
            opacity: $(this).attr("data-opacity"),
            position: $(this).attr("data-position") || "bottom left",
            change: function(hex, opacity) {
                if( !hex ) return;
                if( opacity ) hex += ", " + opacity;
                try {
                } catch(e) {}
            },
            theme: "bootstrap"
        });
    });
');

?>
<div class="equip-process-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'process_name')->textInput(['maxlength' => 12]) ?>

    <?= $form->field($model, 'process_english_name')->textInput(['maxlength' => 20]) ?>

    <?= $form->field($model, 'process_color')->textInput(['maxlength' => 50, "data-control"=>"hue"]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	
    <?php ActiveForm::end(); ?>

</div>


