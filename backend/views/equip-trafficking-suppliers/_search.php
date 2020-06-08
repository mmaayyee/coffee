<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EquipTraffickingSuppliers;

/* @var $this yii\web\View */
/* @var $model backend\controllers\EquipTraffickingSuppliersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-trafficking-suppliers-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-group form-inline">
    <?= $form->field($model, 'name')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'source' => EquipTraffickingSuppliers::getColumn('name'),
            ],
            'options' => ['class' => 'form-control']
        ]) 
    ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
