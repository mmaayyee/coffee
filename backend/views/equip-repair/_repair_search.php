<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/5/23
 * Time: 上午10:07
 */

use common\models\Building;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipRepairSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-repair-search">

    <?php $form = ActiveForm::begin([
        'action' => ['repair-list?equip_id='.$equip_id.'&type='.$type],
        'method' => 'get',
    ]);?>

    <div class="form-inline">

        <?= $form->field($model, 'content')->dropDownList(\backend\models\EquipSymptom::getSymptomIdNameArr(true));?>

        <?=$form->field($model, 'author')->textInput()?>

        <?=$form->field($model, 'process_status')->dropDownList(['' => '请选择', 1 => '未处理', 2 => '处理中', 3 => '成功解决', 4 => '解决失败']);?>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
