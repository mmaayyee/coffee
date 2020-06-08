<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Building;


/* @var $this yii\web\View */
/* @var $model backend\models\BuildingTaskSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .field-buildingtasksettingsearch-building_id {
        width: 40%;
    }
</style>

<div class="building-task-setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div>
        <div class="form-group form-inline">
            <?= $form->field($model, 'building_id')->widget(\kartik\select2\Select2::classname(), [
                'data' => Building::getOperationBuildList(),
                'options' => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/material-safe-value/ajax-get-equipment'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>

            <?= Html::submitButton('检索', ['class' => 'btn btn-primary', 'style' => 'margin-top:20px;']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
