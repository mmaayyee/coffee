<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '添加用户筛选任务';
$this->params['breadcrumbs'][] = ['label' => '筛选用户任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/js/element-ui/2.4.5/lib/theme-chalk/index.css');
$this->registerCssFile('/user-selection-task/css/app.131aa209adb4938f52560e001cfa63c3.css');
$this->registerJSFile('/js/vue/2.6.8/vue.js');
$this->registerJSFile('/js/element-ui/2.4.5/lib/index.js');
$this->registerJSFile('/user-selection-task/js/manifest.00d878c9da207ad9f726.js');
$this->registerJSFile('/user-selection-task/js/app.939eb018be1e777b29e3.js');
?>
<div id=app></div>
<style>
input[type="file"]{
  display:none;
}
</style>
<script>
  var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
  var rootData = <?php echo $taskOptionsList;?>;
  console.log(rootCoffeeStieUrl);
  console.log("数据",rootData);
</script>