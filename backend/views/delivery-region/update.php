<?php

use yii\helpers\Html;

$this->title                   = '配送区域详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="/js/element-ui/2.4.5/lib/theme-chalk/index.css">
<link href=/delivery-region-map/css/app.da5361c89d74b6dcd8c58dcc53f980d7.css rel=stylesheet>
<div id="app"></div>
<script type="text/javascript">
    var rootCoffeeUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
<script type="text/javascript" src='/js/vue/2.6.8/vue.js'></script>
<script type="text/javascript" src='/js/element-ui/2.4.5/lib/index.js'></script>
<script type=text/javascript src=/js/jquery-1.9.1.min.js></script>
  <script charset=utf-8 src="https://map.qq.com/api/js?v=2.exp&key=GBWBZ-ILMWK-LMZJU-AJ642-J36CZ-ODBH5"></script>
  <script type=text/javascript src=/delivery-region-map/js/manifest.18b0a6036afcc990d2b4.js></script>
  <script type=text/javascript src=/delivery-region-map/js/vendor.4dadca250fcbbe907b3c.js></script>
  <script type=text/javascript src=/delivery-region-map/js/app.567f553667177e3a265b.js></script>