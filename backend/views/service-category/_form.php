<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JqueryAsset;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [\backend\assets\AppAsset::className()]]);
$this->registerJsFile("@web/js/ajaxfileupload.min.js", ["depends" => [JqueryAsset::className()]]);
?>
<div class="service-category-form">
    <?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]) ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->radioList($model->getStatus()); ?>
    <div class="form-group">
        <?=Html::button('提交', ['class' => 'btn btn-success', 'onclick'=>'uploadFile()'])?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
  var id = "<?php echo $model->id; ?>";
    var type = id==""?"1":"2";
    var saveUrl = '/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=10';
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
   function uploadFile() {
       var formData = $("form").serializeArray();
       $.ajax({
           url: url+"service-api/category-add-id.html?"+verifyPassword,
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
                          window.location.href="/service-category/index";
                      },
                      error: function() {
                          window.location.href="/service-category/index";
                      }
                  });
               }else {
                   alert(data.message);
                   $(".submit-error").html("修改失败!");
               }
           },
           error: function() {
               $(".submit-error").html('分类修改失败!');
           }
       });
   }
</script>