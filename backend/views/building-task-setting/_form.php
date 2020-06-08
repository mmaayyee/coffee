<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Building;
use backend\models\BuildingTaskSetting;
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/buildingTaskSetting.js?v=3.3', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingTaskSetting */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .form-inline .form-group .form-control{
        margin-left:20px;
        margin-right:10px;
    }
</style>
<script>
    var refuel_cycle = '<?php echo $model->refuel_cycle;?>';
</script>
<div class="building-task-setting-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->building_id) { ?>
        <?= $form->field($model, 'building_id')->dropDownList([$model->building_id =>Building::getField('name',['id' => $model->building_id])], ['data-url' => Yii::$app->request->baseUrl . '/building-task-setting/ajax-get-product-group-material','disabled' => 'disabled']) ?>
    <?php }else{ ?>
        <?= $form->field($model, 'building_id')->widget(\kartik\select2\Select2::classname(), [
            'data' => BuildingTaskSetting::getBuildList($model->building_id),
            'options' => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/building-task-setting/ajax-get-product-group-material'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>
    <?php } ?>
    <?= $form->field($model, 'cleaning_cycle')->textInput() ?>

    <?= $form->field($model, 'day_num')->textInput() ?>

    <?= $form->field($model, 'error_value')->textInput() ?>

    <div id="refuelCycleId">

    </div>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? '添加' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
.form-group{
    display: block;
    clear:both;
}
.form-group label{
    display: inline-block;
    float:left;
    height: 34px;
    line-height: 34px;
    margin-right:10px;
    width:80px;

}
.form-control{
    width:25%;
    float:left;
}
.help-block{
    height: 34px;
    line-height: 34px;
    float:left;
    margin:0;
}
.select2{
    width:25% !important;
}
</style>
