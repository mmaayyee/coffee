<?php

$this->title                   = '添加自组合套餐活动';
$this->params['breadcrumbs'][] = ['label' => '自组合套餐活动列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/js/element-ui/2.4.5/lib/theme-chalk/index.css');
$this->registerCssFile('/activity-combin-package-assoc/css/app.6e920a37b520e73155afcc18c2cea49d.css');
$this->registerJSFile('/js/vue/2.6.8/vue.js');
$this->registerJSFile('/js/element-ui/2.4.5/lib/index.js');
$this->registerJSFile('/activity-combin-package-assoc/js/manifest.e5fa88d23bd5f9deff5d.js');
$this->registerJSFile('/activity-combin-package-assoc/js/vendor.e0a577271c10949ff216.js');
$this->registerJSFile('/activity-combin-package-assoc/js/app.b02b21e3bef91171d11c.js');
?>
  <div id=app></div>
  <style>input[type="file"] {
    display: none;
  }</style>
  <script type=text/javascript>
  rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
  //城市
  rootCityList = <?php echo $mechanismList; ?>;
  //商品
  rootPointProductList = <?php echo $pointProductList ?>;
  // 优惠券套餐
  rootPackageCouponList = <?php echo $packageCouponList ?>;
  // 单优惠券
  rootSingleCouponList = <?php echo $singleCouponList ?>;
// 编辑时数据
var is_update = '<?php echo $is_update; ?>';
var rootActivityInfo= <?php echo $updateTemp ?>;
</script>


