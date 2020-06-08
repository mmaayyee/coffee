<?php
use backend\models\Manager;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Sale;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-build-assoc-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
    <div class="form-inline">
    	<?= $form->field($model, 'sale_name') ?>
    	<?= $form->field($model, 'sale_email') ?>
    	<?= $form->field($model, 'sale_phone') ?>
        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
