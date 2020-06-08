<?php

use backend\models\Grind;
use backend\models\Organization;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\GrindSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile("/js/grind_search.js", ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<script type="text/javascript">
var grind_type        = "<?php echo $model->grind_type; ?>";
</script>
<div class="grind-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">

    <?=$form->field($model, 'grind_switch')->dropDownList(Grind::$switchType);?>

    <?=$form->field($model, 'grind_type')->dropDownList(Grind::getGrindTypeList(1));?>

    <div style="display:none;" class="orgClass form-group">
        <?=$form->field($model, 'org_id')->dropDownList(Organization::getBranchArray(1));?>
    </div>

    <div style="display:none;" class="buildSearch form-group">
        <?=$form->field($model, 'buildName')->textInput();?>
        <?=$form->field($model, 'equipmentCode')->textInput();?>
    </div>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
