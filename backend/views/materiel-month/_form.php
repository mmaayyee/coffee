<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterielMonth */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materiel-month-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="materiel-month-form">

        <h4>楼宇名称:<?=$model['build_name']?></h4>
        <h4>时间:<?=date('Y年m月',$model['time'])?></h4>
            <table id="w0" class="table table-striped table-bordered detail-view">
                <tbody>
                    <?php foreach( $model['info'] as $k => $v): ?>
                        <tr><th><?=$v['materialTypeName']?></th><td><input name="materialTypeArr[]" value="<?=$v['change_value']?>"></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>

    <div class="form-group">
        <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
