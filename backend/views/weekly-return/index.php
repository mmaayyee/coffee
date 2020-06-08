<?php

use yii\helpers\Html;

$this->title                   = '周报复购数据';
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="/weekly-repurchase/css/app.6c6fa1f1c44e1f033e6bc6cc2f7aacb1.css" rel="stylesheet">
<div id="app"></div>
<script type="text/javascript">
    var rootLogin = '<?php echo $rules; ?>';
    var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
<script type="text/javascript" src="/js/echarts.min.js"></script>
<script type="text/javascript" src="/weekly-repurchase/js/manifest.f10c2e68de61c898265d.js"></script>
<script type="text/javascript" src="/weekly-repurchase/js/vendor.98d59b1a1db55aad0cb8.js"></script>
<script type="text/javascript" src="/weekly-repurchase/js/app.83b57755b3298359494b.js"></script>