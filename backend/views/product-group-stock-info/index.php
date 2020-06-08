<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductGroupStockInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '产品组料仓信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-group-stock-info-index">

<div class="advert-index">
    <?php echo $this->render('_search', ['model' => $model]); ?>
    <div id="error" style="color: red; display: none;">该产品组料仓存在关联数据，请确认后在删除</div>

    <?php if (Yii::$app->user->can('添加产品组料仓')) {?>
    <p>
        <?=Html::a('添加', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?php }?>

    <table class="table table-bordered">
        <tr>
            <td>序号</td>
            <td>产品组料仓信息名称</td>
            <td>设备类型</td>
            <td>操作</td>
        </tr>
        <?php if (isset($proGroupStockList) && $proGroupStockList) {
    ?>
        <?php foreach ($proGroupStockList as $key => $proGroupStock) {
        ?>
            <tr>
                <td><?php echo ($page - 1) * $pageSize + $key + 1 ?></td>
                <td><?php echo $proGroupStock['product_group_stock_name'] ?></td>
                <td><?php echo $proGroupStock['equip_type_name'] ?></td>
                <td>
                    <?php echo Html::a(
            '<span class="glyphicon glyphicon-eye-open" id=' . $proGroupStock['id'] . ' title="查看"></span>',
            Url::to(['product-group-stock-info/view', 'id' => $proGroupStock['id']])
        ); ?>
                    <?php if (Yii::$app->user->can('编辑产品组料仓')) {
            echo Html::a(
                '<span class="glyphicon glyphicon-pencil" title="编辑"></span>',

                Url::to(['product-group-stock-info/update', 'id' => $proGroupStock['id']])
            );
        }?>
                    <?php if (Yii::$app->user->can('删除产品组料仓')) {
            echo Html::a(
                '<span class="glyphicon glyphicon-trash del_group" productGroupStockName=' . $proGroupStock['product_group_stock_name'] . ' id=' . $proGroupStock['id'] . ' title="删除"></span>',
                '#'
            );
        }?>

                </td>
            </tr>
        <?php }}?>
    </table>
    <?php if (!isset($proGroupStockList) || !$proGroupStockList) {?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php }?>
    <?=
LinkPager::widget([
    'pagination' => $pages,
]);
?>
</div>
</div>
<?php
$url = Url::to(["product-group-stock-info/delete"]);
$this->registerJs('
    $(".del_group").click(function(){
        if(!confirm("确认要删除？")){
            return false;
        }else {
            var proGroupStockId = $(this).attr("id");
            var productGroupStockName = $(this).attr("productGroupStockName");
            $.post("' . $url . '",{id: proGroupStockId,productGroupStockName: productGroupStockName},function(data){
                if(data == 0){
                    $("#error").show();
                }else{
                    window.location.reload();
                }
            });
        }
    })

');

?>