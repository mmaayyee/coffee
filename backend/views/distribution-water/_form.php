<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use common\models\Building;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWater */
/* @var $form yii\widgets\ActiveForm */

use yii\widgets\ActiveForm;
$this->registerJs("
    var buildId = $('#distributionwater-build_id').val();
    var supplierId = '" . $model->supplier_id . "';
    $.get(
        '/distribution-water/water-supplier',
        {buildId:buildId},
        function(data) {
            var html = '<option value=\"\">请选择</option>';
            $.each(data, function(index, e){
                if (index == supplierId) {
                    html += '<option value='+index+' selected=\"selected\">'+e+'</option>';

                } else{
                    html += '<option value='+index+'>'+e+'</option>';
                }
            })
            $('#distributionwater-supplier_id').html(html);
        },
        'json'
    )
    $('#distributionwater-build_id').change(function(){
        var buildId = $(this).val();
        $.get(
            '/distribution-water/water-supplier',
            {buildId:buildId},
            function(data) {
                var html = '<option value=\"\">请选择</option>';
                $.each(data, function(index, e){
                    html += '<option value='+index+'>'+e+'</option>';
                })
                $('#distributionwater-supplier_id').html(html);
            },
            'json'
        )
    });
")

?>

<div class="distribution-water-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'build_id')->widget(Select2::className(), [
    'data'          => Building::getDeliveryBuildList([Building::SERVED, Building::TRAFFICKING_IN]),
    'options'       => ['placeholder' => '请选择楼宇名称', 'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);?>
    <?php if ($model->isNewRecord) {?>

	<?=$form->field($model, 'surplus_water')->textInput(['maxLength' => 4])?>
    <?php } else {?>

	<?=$form->field($model, 'surplus_water')->textInput(['disabled' => 'disabled'])?>
	<?php }?>

    <?=$form->field($model, 'need_water')->textInput(['maxLength' => 2])?>

    <?=$form->field($model, 'supplier_id')->dropDownList(['' => '请选择'])?>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
