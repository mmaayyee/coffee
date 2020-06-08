<?php
$this->registerJs('
 $("#download").click(function(){
        $(this).attr("disabled",false);
    })
')
?>
<form role="form" method="post">
  <div class="form-group">
    <label for="name">下载文件路径</label>
    <input type="text" class="form-control" name="filePth" placeholder="请输入下载文件路径">
  	<input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
  </div>
  <button id="download" type="submit" class="btn btn-default">下载</button>
</form>
