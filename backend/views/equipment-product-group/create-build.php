<?php

use yii\helpers\Html;

?>
<script type="text/javascript">
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
</script>
    <div class="block-file">
        <div class="form-group">
            <label>导入文件</label>
            <div class="hint-block">
                （<span id="autoreqmark">*</span>导入文件必须是TXT格式的，且每个楼宇独占一行）
            </div>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'add-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadTextFile(this)"])?>
            <!-- , 'check-type' => 'required fileFormat', 'fileFormat-message' => '文件上传格式不正确' -->
            <input id="add-file-name" type="hidden" name="EquipmentProductGroup[fileUrl]" value=""/>
        </div>
        <div class="form-group verify-result" id="div_verify" ></div>
    </div>

