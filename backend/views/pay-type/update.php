<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '支付方式';
$this->params['breadcrumbs'][] = ['label' => '支付方式列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/pay-type/css/app.f0141ac8e5f3ba9e4328dd5058fd6047.css');
$this->registerJSFile('/pay-type/js/manifest.f5c1d0f473687e86a839.js');
$this->registerJSFile('/pay-type/js/vendor.1506af31fe9cb2e6aabb.js');
$this->registerJSFile('/pay-type/js/app.88f8b6884a7b89076906.js');
?>
<div id=app></div>
<style>
input[type="file"]{
  display:none;
}
</style>
<script type="text/javascript">
	var rootData = <?php echo $payType; ?>;
    console.log("rootData..",rootData);
    var payTypeHolicy = <?php echo $payTypeHolicy; ?>;
	var rootPayTypeData;
    if(payTypeHolicy.error_code==0){
        rootPayTypeData = payTypeHolicy.data;
    }
</script>