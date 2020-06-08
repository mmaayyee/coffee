<?php

use backend\models\LotteryWinningHint;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\LotteryWinningHint */
/* @var $form yii\widgets\ActiveForm */
// lottery_winning_hint.js
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/ajaxfileupload.min.js?v=1.1", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/uploadPreview.min.js?v=1.4", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/lottery_winning_hint.js?v=1.2", ["depends" => ["backend\assets\AppAsset"]]);

$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '提示语管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
@media (min-width: 768px){
   .lottery .form-group{
        width: 45%;
    }
	.losing-lottery{
        margin-top: 10px;
	}
    .losing-lottery .field-lotterywinninghint-hint_error_photo,
    .losing-lottery .field-lotterywinninghint-second_button_photo,
    .losing-lottery .field-lotterywinninghint-thank_participate_photo{
	    display: inline-block;
        width: 32%;
	}
}
.size-tip {
    font-weight: 400;
    font-size: 12px;
    color: red;
}
</style>
<script>
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
</script>

<div class="lottery-winning-hint-form">

    <?php $form = ActiveForm::begin();?>

    <input type="hidden" id="lotterywinninghint-hint_id" class="form-control" name="LotteryWinningHint[hint_id]" value="<?php echo $model->hint_id; ?>">
    <h3>中奖提示</h3>
    <div class="form-inline lottery">
        <?=$form->field($model, 'hint_success_text')->textInput(['maxlength' => 8])->label('恭贺文本')?>

        <div class="form-group field-lotterywinninghint-hint_success_photo">
            <label class="control-label" for="lotterywinninghint-hint_success_photo">获奖图片<span class="size-tip">（尺寸：263&times;281px）</spsn></label>
            <input type="file" id="lotterywinninghint-hint_success_photo" name="LotteryWinningHint[0]" check-type="required">
            <div class="imgdiv"><img src="<?php echo $model->hint_success_photo ?>" id="lotterywinninghint-hint_success_photo_img" width="120" height="120" /></div>
        </div>
    </div>
    <div class="losing-lottery">
        <h3>未中奖提示</h3>
        <div class="form-inline">
            <?=$form->field($model, 'hint_error_text')->textInput(['maxlength' => 8])->label('安慰文本')?>
        </div>

        <div class="form-group field-lotterywinninghint-hint_error_photo">
            <label class="control-label" for="lotterywinninghint-hint_error_photo">未获奖图片<span class="size-tip">（尺寸：580&times;720px）</spsn></label>
            <input type="file" id="lotterywinninghint-hint_error_photo" name="LotteryWinningHint[1]" check-type="required">
            <div class="imgdiv"><img src="<?php echo $model->hint_error_photo ?>" id="lotterywinninghint-hint_error_photo_img" width="120" height="120" /></div>
        </div>

        <div class="form-group field-lotterywinninghint-second_button_photo">
            <label class="control-label" for="lotterywinninghint-second_button_photo">二次按钮<span class="size-tip">（尺寸：260&times;50px）</spsn></label>
            <input type="file" id="lotterywinninghint-second_button_photo" name="LotteryWinningHint[2]" check-type="required">
            <div class="imgdiv"><img src="<?php echo $model->second_button_photo ?>" id="lotterywinninghint-second_button_photo_img" width="120" height="120" /></div>
        </div>

        <div class="form-group field-lotterywinninghint-thank_participate_photo">
            <label class="control-label" for="lotterywinninghint-thank_participate_photo">谢谢参与<span class="size-tip">（尺寸：260&times;50px）</spsn></label>
            <input type="file" id="lotterywinninghint-thank_participate_photo" name="LotteryWinningHint[3]" check-type="required">
            <div class="imgdiv"><img src="<?php echo $model->thank_participate_photo ?>" id="lotterywinninghint-thank_participate_photo_img" width="120" height="120" /></div>
        </div>
    </div>
    <?php if (!$model->activity_type_id) {?>
        <?=$form->field($model, 'activity_type_id')->dropDownList(LotteryWinningHint::getActivityTypeList())?>
    <?php }?>
    <div class="submit-error"></div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">保存</button>
    </div>


    <?php ActiveForm::end();?>

</div>
