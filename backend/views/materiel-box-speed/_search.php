<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielBoxSpeedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materiel-box-speed-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="form-inline">

    <?= $form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList()) ?>

    <?= $form->field($model, 'material_type_id')->dropDownList(Api::getMaterialTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
