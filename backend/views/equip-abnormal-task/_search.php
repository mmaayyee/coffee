<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Building;
use backend\models\EquipAbnormalTask;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipAbnormalTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-abnormal-task-search">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>
        <div class="form-group form-inline">
            <div class="form-group">
            <label>楼宇</label>
            <div class="select2-search">
                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'build_id',
                    'data' =>  Building::getDeliveryBuildList([Building::SERVED, Building::TRAFFICKING_IN]),
                    'options' => ['multiple' => false, 'placeholder' => '请选择楼宇'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            </div>
            <div class="form-group">
                <label>任务状态</label>
                <div class="select2-search">
                    <?php
                    echo Select2::widget([
                        'model' => $model,
                        'attribute' => 'task_status',
                        'data' => EquipAbnormalTask::$task_status,
                        'options' => ['multiple' => false, 'placeholder' => '请选择状态'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <?= $form->field($model, 'create_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
            <div class="form-group">
                <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
</div>
