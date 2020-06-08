<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use backend\models\Manager;
/* @var $this yii\web\View */
/* @var $model backend\models\ManagerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-group  form-inline">
        <div class="form-group">
            <label>请选择角色</label>
        <div class="select2-search">
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'role',
                'data' => Manager::getRoleArr(),
                'options' => ['placeholder' => '请选择角色'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    <?php if(count(\backend\models\Organization::getBranchArray()) > 2 ):?>
        <?= $form->field($model, 'branch')->dropDownList(\backend\models\Organization::getBranchArray())  ?>  
    <?php endif;?>     

    <?= $form->field($model, 'realname') ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
