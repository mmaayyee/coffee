<?php

use backend\models\ScmMaterialType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmMaterialTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-material-type-search">

    <?php $form = ActiveForm::begin([
    'action' =>
    ['index'],
    'method' => 'get',
]);?>
<div class="form-group form-inline">
    <?=$form->field($model, 'material_type_name')?>
    <?=$form->field($model, 'type')->dropDownList(ScmMaterialType::$type)?>
    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?></div>
</div>
<?php ActiveForm::end();?></div>