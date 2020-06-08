<?php

use backend\assets\AppAsset;
use backend\models\Grind;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Grind */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile("/js/grind_building.js?v=2.2", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile("@web/css/add-condition.css?v=1.0", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("@web/js/laypage.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/grind.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<script type="text/javascript">
    // 判断是否为新添加数据
    var isNewRecord         = <?php echo $model->isNewRecord; ?>;
    // 范围类型
    var updateGrindType     = <?php echo !empty($model->grind_type) ? $model->grind_type : 0; ?>;
    // 修改时已添加的楼宇信息
    var searchUpdateBuild   = <?php echo !empty($model->searchUpdateBuild) ? $model->searchUpdateBuild : 0; ?>;
</script>
<div class="grind-form">

    <?php $form = ActiveForm::begin();?>

        <?=$form->field($model, 'grind_type')->dropDownList(Grind::getGrindTypeList(0), ['onChange' => "grindTypeChange(this)"])?>

        <?=$form->field($model, 'grind_time')->textInput()->label('磨豆时间（秒）')?>

        <?=$form->field($model, 'interval_time')->textInput()->label('间隔时间（分）')?>

        <?=$form->field($model, 'grind_remark')->textInput()?>

        <?=$form->field($model, 'grind_switch')->radioList(array(0 => '否', 1 => '是'))?>

    <?php if ($model->isNewRecord == 0) {?>
       <?=$form->field($model, 'grind_id')->hiddenInput()->label(false)?>
    <?php } else {?>
        <?=$form->field($model, 'buildingList')->hiddenInput()->label(false)?>
        <div class="search-org" style="display:none;">
            <?=$form->field($model, 'org_id')->dropDownList(Grind::getOrgNameList())?>
        </div>
    <?php }?>

	<div class="search-building" style="display:none;">
		<?=$this->render('_grind_building_search_where.php', ['whereString' => $model->where_string]);?>
		<?=$this->render('building.php');?>
	</div>
    <div style="display:none;" class="help-block" id="buidlingVerify">
        <font color="#a94442;">请选择楼宇。</font>
    </div>
    <div class="form-group">
        <?=Html::button('保存', ['class' => ' buttonCss btn btn-primary', 'onclick' => 'formSubmit()'])?>
    </div>
    <?php ActiveForm::end();?>

</div>
