
<?php
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="/js/element-ui/2.4.5/lib/theme-chalk/index.css">
<link href="/order-info-detail/css/app.a4a71dbc71c0ab4317f02e55abcdf299.css" rel="stylesheet">
<div id="app"></div>
<script type="text/javascript">
    rootOrderID = '<?php echo $orderInfoID; ?>';
    rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
<script type="text/javascript" src='/js/vue/2.6.8/vue.js'></script>
<script type="text/javascript" src='/js/element-ui/2.4.5/lib/index.js'></script>
<script type="text/javascript" src="/order-info-detail/js/manifest.7d35cbf38d97659db7af.js"></script>
<script type="text/javascript" src="/order-info-detail/js/vendor.a46fa7f5ca7abb490596.js"></script>
<script type="text/javascript" src="/order-info-detail/js/app.ddbfbc680d3a792e0302.js"></script>