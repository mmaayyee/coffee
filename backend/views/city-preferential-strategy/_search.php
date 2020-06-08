<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="build-type-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline">
        <div class="form-group">
            <label class="control-label">城市名称</label>
            <input type="text" class="form-control" name="city_name" value="<?php echo $cityName; ?>">
            <div class="help-block"></div>
        </div>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
