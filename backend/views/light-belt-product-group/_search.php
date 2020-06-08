<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LightBeltProductGroup;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="light-belt-product-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-group form-inline">
        <?= $form->field($model, 'product_group_name') ?>
        
        <div class="form-group form-inline">
            <div class="form-group form-inline"><label>所选饮品</label></div>
            <div class="form-group form-inline" style="width: 180px;">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'choose_product',
                    'data' => LightBeltProductGroup::getProductArr(),
                    'options' => [
                        'placeholder' => '所选饮品',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
