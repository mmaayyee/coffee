<?php

use backend\models\AuthItemSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
 <div class="form-group  form-inline">
     <label>请选择角色</label>
     <div class="select2-search">
         <?=Select2::widget([
    'model'         => $model,
    'attribute'     => 'role',
    'data'          => AuthItemSearch::getRoleNameList(),
    'options'       => ['placeholder' => '请选择角色'],
    'pluginOptions' => [
        'allowClear' => true,
    ]]);?>
     </div>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
</div>
    <?php ActiveForm::end();?>

</div>
