<?php

use backend\models\ScmWarehouse;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmTotalInventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-total-inventory-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">

        <div class="form-group">
        <label>请选择库名</label>
        <div class="select2-search">
        <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'warehouse_id',
    'data'          => ScmWarehouse::getWarehouseIdNameArr(['use' => ScmWarehouse::MATERIAL_USE]),
    'options'       => ['placeholder' => '请选择库名'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
        </div>
        </div>


        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
