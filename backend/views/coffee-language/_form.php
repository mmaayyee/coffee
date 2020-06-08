<?php

use backend\models\CoffeeLanguage;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

$this->registerJsFile("@web/js/jquery-2.0.0.min.js", ["depends" => [\backend\assets\AppAsset::className()]]);
$this->registerJsFile("@web/js/coffee-language.js", ["depends" => [JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<script type="text/javascript">
     var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
</script>
<div class="coffee-language-form">

    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'id')->hiddenInput(['name' => 'CoffeeLanguage[id]', 'id' => 'id', 'value' => $model['id']])->label(false);?>
    <?=$form->field($model, 'language_sort')->textInput(['maxlength' => true, 'placeholder' => '请输入咖语的指定顺序,只能输入数字'])?>
    <?=$form->field($model, 'language_name')->textInput(['maxlength' => true])?>
    <?=$form->field($model, 'language_type')->dropDownList(CoffeeLanguage::getLanguageTypeList())->label('咖语类型')?>
    <?=$form->field($model, 'language_static')->dropDownList(CoffeeLanguage::getOnlineStaticList())->label('咖语状态')?>
    <?=$form->field($model, 'language_product')->dropDownList($model->getAllProductName())?>
    <?=$form->field($model, 'language_content')->textarea(['maxlength' => true])?>
    <div class="form-group">
        <?=Html::button('提交', ['class' => 'btn btn-success btn-block', 'onclick' => 'uploadFile()'])?>
    </div>
    <?php ActiveForm::end();?>
</div>
