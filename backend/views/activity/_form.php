<?php
use backend\models\Activity;
use common\models\ActivityApi;
use frontend\assets\AppAsset;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\Activity */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '营销游戏列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("@web/css/nine_lottery_activity.css?v=1.2", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/ajaxfileupload.min.js?v=1.1", ["depends" => ["backend\assets\AppAsset"]]);

$this->registerJsFile("/js/ueditor.config.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/ueditor.all.min.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/jquery.serializejson.min.js?v=1", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/uploadPreview.min.js?v=1.4", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/laytpl.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/bootstrap3-validation.js?v=2.4", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/regular_verification.js", ["depends" => ["backend\assets\AppAsset"]]);

?>
<script>
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
</script>
<div class="activity-form">

    <?php $form = ActiveForm::begin();?>
    <div class="form-inline">
        <input type="hidden" id="activity-activity_id" class="form-control" name="Activity[activity_id]" value="<?php echo $model->activity_id ?>">
        <input type="hidden" id="lottery_activity_copy" name="Activity[is_copy]" value="<?php echo $model->isCopy ?>">

        <?=$form->field($model, 'activity_name')->textInput(['maxlength' => '20'])?>

        <?=$form->field($model, 'start_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly', 'check-type' => 'required'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
    ]]);?>

        <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly', 'check-type' => 'required compareDate'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'hour'       => 23,
        'minute'     => 59,
    ]]);?>

        <?=$form->field($model, 'activity_sort')->textInput(['maxlength' => '6'])?>

        <?=$form->field($model, 'status')->dropDownList(Activity::activityStatusList())?>

        <?=$form->field($model, 'activity_type_id')->dropDownList(ActivityApi::getActivityTypeList(2, 1), ['onchange' => 'activityTypeChange(this);'])?>

    </div>

    <div class="table- field-activity-activity_desc">
        <label class="control-label" for="activity-activity_desc">活动描述</label>
        <script id="editor" type="text/plain"></script>
        <span class="help-block" id="valierr"></span>
        <input id="activityDesc" type="hidden" name="Activity[activity_desc]" value="<?=Html::encode($model->activity_desc)?>"/>
    </div>

    <?=$this->render('_scratchable_latex.php', ['model' => $model, 'form' => $form]);?>

    <div class="submit-error"></div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">保存</button>
    </div>

    <?php ActiveForm::end();?>

</div>
