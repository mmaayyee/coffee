<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;

$this->title = '拼团活动设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.group-activity-set{
	margin-left:50px;
}
form table tr{
	display: block;
	margin-bottom: 15px;
}
.td-title{
	width:100px;
}
.el-checkbox{
    display: inline-block;
    width:auto;
    min-width:300px;
    max-width:220px;
    margin-right:20px;
}
</style>

<div class="group-activity-set">

    <h1><?= Html::encode($this->title) ?></h1>

	<form action="<?php echo Yii::$app->params['fcoffeeUrl']; ?>group-booking-api/add-setting.html" onsubmit="return onsub()" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td class="td-title">banner:<br>750X240</td>
					<td>
						 <input type="file" id="file" name="activity_details_img[]" onchange="changepic(this)" value="" multiple="multiple"/>
						 <div id="img-resource">
						 		<?php
							$config_value['banner_img'] = Json::decode($config_value['banner_img']);
							foreach ($config_value['banner_img'] as $key => $value) {
								echo '<img src="'.$value.'" id="show" class="show-img"  height="80" width="80">';
							}
						?>
						 </div>
					</td>
				</tr>
				<tr>
					<td class="td-title">不参与机构:</td>
					<td>
						<?php foreach ($Organization as $k => $v): ?>
							<?php if (is_array($config_value['participation_organization'])): ?>
                                <span class="el-checkbox">
								<input type="checkbox" class="participation_organization" name="participation_organization[]" value="<?=$v['org_id']?>" <?=in_array($v['org_id'],$config_value['participation_organization']) ? 'checked' : ''; ?>><?=$v['org_name']?>
                                    </span>
							<?php else: ?>
                                <span class="el-checkbox">
								<input type="checkbox" class="participation_organization" name="participation_organization[]" value="<?=$v['org_id']?>" ><?=$v['org_name']?>
                                    </span>
							<?php endif ?>
							
						<?php endforeach ?>
					</td>
				</tr>
				<tr>
					<td class="td-title">点位商品类型:</td>
					<td>
						<?php if (is_array($config_value['goods_type'])): ?>
							<input type="checkbox" class="goods_type" name="goods_type[]" <?=in_array(0,$config_value['goods_type']) ? 'checked' : ''; ?> value="0">普通
							<input type="checkbox" class="goods_type" name="goods_type[]" <?=in_array(1,$config_value['goods_type']) ? 'checked' : ''; ?> value="1">臻选
						<?php else: ?>
							<input type="checkbox" class="goods_type" name="goods_type[]"  value="0">普通
							<input type="checkbox" class="goods_type" name="goods_type[]"  value="1">臻选
						<?php endif ?>
					</td>
				</tr>
				<tr>
					<td class="td-title"></td>
					<td>
						<a href="index" >取消</a>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
							if (Yii::$app->user->can('拼团活动设置修改')) {
					            echo "<input type='submit' value='提交'>";
					        }
						 ?>
					</td>
				</tr>
			</table>
	</form>

</div>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
	function changepic(obj) {
		getPhotoSize(obj)
		var file = document.getElementById("file");
        var str=""
        var fileSrc = file.files;
        var result=document.getElementById("img-resource");
        result.innerHTML="";
            for (var i = 0; i <fileSrc.length; i++) {
	            var reader= new FileReader();
	            reader.readAsDataURL(fileSrc[i]);
	            reader.onload = function (e) {
	            	 result.innerHTML+='<img src="' + this.result +'" alt="" id="show" height="80" width="80"/>';
	            };
	        }
    }
	function onsub(){
		//判断checkbox 是否选中
		var str1 = $(".goods_type").is(":checked");//选中，返回true，没选中，返回false
		if (str1 == false) {
			alert('您有选项未选择!')
			location.reload();
			return false;
		}
		return true;
	}
    function getPhotoSize(obj){
        photoExt=obj.value.substr(obj.value.lastIndexOf(".")).toLowerCase();//获得文件后缀名
        if(photoExt!=".jpg"&&photoExt!=".gif"&&photoExt!=".bmp"&&photoExt!=".png"&&photoExt!=".jpeg"){
            //jpg和jpeg格式是一样的只是系统Windows认jpg，Mac OS认jpeg，
            alert('请选择格式为*.jpg、*.gif、*.bmp、*.png、*.jpeg 的图片');
            obj.outerHTML=obj.outerHTML; //对象重新赋值的一个操作.原理和FORM.RESET()一样,将用户操作去掉
            return (false);
        }
        var fileSize = 0;
        var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
        if (isIE && !obj.files) {
            var filePath = obj.value;
            var fileSystem = new ActiveXObject("Scripting.FileSystemObject");
            var file = fileSystem.GetFile (filePath);
            fileSize = file.Size;
        }else {
            fileSize = obj.files[0].size;
        }
        fileSize=Math.round(fileSize/1024*100)/100; //单位为KB
        if(fileSize>=200){
            alert("照片最大尺寸为200KB，请重新上传!");
            obj.outerHTML=obj.outerHTML; //对象重新赋值的一个操作.原理和FORM.RESET()一样,将用户操作去掉
            return (false);
        }
    }
</script>