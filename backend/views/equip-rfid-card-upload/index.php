<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '批量添加门禁卡';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-upload-index">
    
    <?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    
    <?=$form->field($model,'rfid_card_code')->textarea(['rows' => 6])->label("RFID卡号（请以中文逗号，英文逗号，空格，换行 进行分隔！）")?>
    
    <?php if(isset($signRfidSave) && $signRfidSave && !$errorStr){ ?>
    <div style="color: green;margin-left: 75%;">
        <label for="">添加成功！</label>
        <?php if($similarRfidCode){ ?>
        <div style="color: red;">相同的卡号有：<?php echo $similarRfidCode; ?></div>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if(isset($errorStr) && $errorStr){ ?>
    <div>
        <div style="color: red;margin-left: 75%;">错误信息：<?php echo $errorStr; ?></div>
    </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
