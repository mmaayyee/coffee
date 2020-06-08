<?php
use backend\models\Manager;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
use backend\models\SaleBuildingAssoc;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-build-assoc-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
    <div class="form-inline">
        <?=$form->field($model, 'sale_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => SaleBuildingAssoc::getSaleNameList()], 'options' => ['class' => 'form-control']])?>
        <?=$form->field($model, 'build_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => SaleBuildingAssoc::getBuildNameList()], 'options' => ['class' => 'form-control']])?>
        <?= $form->field($model, 'sale_phone') ?>
        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
