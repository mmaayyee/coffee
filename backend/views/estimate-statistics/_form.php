<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatistics */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="estimate-statistics-form">

    <?php $form = ActiveForm::begin();?>
    <table cellpadding="5" cellspacing="5" border="1" class="table table-striped table-bordered">
        <tr style="center">
            <td width="10%"><label class="control-label">姓名</label></td>
            <td width="50%"><label class="control-label">物料量</label></td>
            <td width="40%"><label class="control-label">明日楼宇</label></td>
        </tr>
        <tr>
            <td style=""><label class="control-label">#</label></td>
            <td>
                    <?php echo $estimateMaterialDetail; ?>
            </td>
            <td>

            </td>
        </tr>
        <?php echo $estimateShowData; ?>
    </table>
    <div class="form-group">
        <?=Html::submitButton('发送', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
