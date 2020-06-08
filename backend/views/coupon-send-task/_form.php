<?php

$this->title                   = '发券任务添加';
$this->params['breadcrumbs'][] = ['label' => '任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/js/element-ui/2.4.5/lib/theme-chalk/index.css');
$this->registerCssFile('/coupon-task/css/app.e2b74982682ce52c6606401d9b3eb3ef.css');
$this->registerJSFile('/js/vue/2.6.8/vue.js');
$this->registerJSFile('/js/element-ui/2.4.5/lib/index.js');
$this->registerJSFile('/coupon-task/js/manifest.13e6d8b97987e10cda34.js');
$this->registerJSFile('/coupon-task/js/vendor.0cef2f2626d55c64fc2e.js');
$this->registerJSFile('/coupon-task/js/app.85c492a9b158fb7ad681.js');
?>
<div id=app></div>
<style>
input[type="file"] {
    display: none;
}
</style>
<script type="text/javascript">
    rootList=<?php echo $list; ?>;
    rootTaskId = '<?php echo $id; ?>';
    rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
