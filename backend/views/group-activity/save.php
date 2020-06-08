<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '添加商品活动';
$this->params['breadcrumbs'][] = ['label' => '拼团活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/js/element-ui/2.4.5/lib/theme-chalk/index.css');
$this->registerCssFile('/spell-group/css/app.f7a10ca9c3f56025dadbc760e6f2c268.css');
$this->registerJSFile('/js/vue/2.6.8/vue.js');
$this->registerJSFile('/js/element-ui/2.4.5/lib/index.js');
$this->registerJSFile('/spell-group/js/manifest.ef56619311e3fa8a8fc7.js');
$this->registerJSFile('/spell-group/js/vendor.4dadca250fcbbe907b3c.js');
$this->registerJSFile('/spell-group/js/app.a42fe0d7f59c5dd8b10e.js');
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