<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\OutStatistics */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="out-statistics-form">

    <?php $form = ActiveForm::begin();?>

    <?php echo $examineMaterialDetail; ?>
    <div class="form-group">
        <?=Html::submitButton('复审', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
