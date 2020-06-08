<?php

use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/distributionTask.js?v=1', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="distribution-task-form">
    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'build_id')->widget(Select2::className(), [
        'data' => \common\models\Building::getOperationBuildList(),
        'options' => ['placeholder' => '请选择楼宇',  'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
    <p>所选楼宇：<span class="build" id="build_name"></span></p>
    <p>设备编号：<span class="buildCode" id="equip_code"></span></p>
    <p>设备类型：<span class="equipCode" id="equip_model"></span></p>
    <div id="allmap" style="width:20%;height:200px;"></div>

    <?=$form->field($model, 'content')->textarea(['maxlength' => 255, 'rows' => 6])?>

    <?=$form->field($model, 'assign_userid')->widget(Select2::className(), [
        'data'    => [],
        'options' => ['placeholder' => '请选择负责人'],
    ])?>

    <input type="hidden" class="hide_assign_userid" value="<?php echo $model->assign_userid ?>">
    <input type="hidden" class="hide_assign_userName" value="<?php echo isset($model->assignUser->name) ? $model->assignUser->name : '' ?>">

    <input type="hidden" class="address" value="">

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>



