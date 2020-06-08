<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\tagsinput\TagsinputWidget;
use yii\web\JqueryAsset;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [\backend\assets\AppAsset::className()]]);
$this->registerJsFile("@web/js/ajaxfileupload.min.js", ["depends" => [JqueryAsset::className()]]);
/* @var $this yii\web\View */
/* @var $model backend\models\ServiceQuestion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-question-form">
    <?php $form = ActiveForm::begin(); ?>
    <?=$form->field($model, 'id')->hiddenInput(['name' => 'ServiceQuestion[question_id]', 'id' => 'id', 'value' => $model['id']])->label(false);?>
    <?= $form->field($model, 'question_key')->widget(TagsinputWidget::classname(), [
        'clientOptions' => [
            'trimValue' => true,
            'allowDuplicates' => false,
            'maxChars'=> 5
        ]

    ]) ?>
    <?= $form->field($model,'s_c_id')->dropDownList($category)?>
    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'answer')->textarea(['maxlength' => true]) ?>
    <?= $form->field($model, 'static')->radioList($model->getStatus()); ?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-success btn-block','onclick'=>'uploadFile()']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    var id = "<?php echo $model->id; ?>";
    var type = id==""?"1":"2";
    var saveUrl = '/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=9';
    // console.log("id..",id);
    window.onload=function(){
        $("form").on('submit', function (e) {
            e.preventDefault();
     });
    }
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
    function uploadFile() {
        var formData = $("form").serializeArray();
        if($(".has-error").html()){
             return false;
        }
        $.ajax({
            url: url+"service-api/question-add.html?"+verifyPassword,
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(data) {
                console.log(data);
                if(data.code == 200) {
                    $.ajax({
                        type: "GET",
                        url: saveUrl,
                        success: function(data) {
                            window.location.href="/service-question/index";
                        },
                        error: function() {
                            window.location.href="/service-question/index";
                        }
                    });
                }else {
                    alert(data.message);
                    $(".submit-error").html("问题创建失败!");
                }
            },
            error: function() {
                $(".submit-error").html('问题添加失败!');
            }
        });
    }
</script>
