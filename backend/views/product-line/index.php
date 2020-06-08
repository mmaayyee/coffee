<?php
use backend\models\productOfflineRecord;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title                   = '产品下架列表管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="advert-index">
    <?php echo $this->render('_search', ['model' => $model]); ?>
    <div id="error" style="color: red; display: none;">上架失败，请重新提交</div>
    <table class="table table-bordered">
        <tr>
            <td>楼宇名称</td>
            <td>设备编号</td>
            <td>下架产品</td>
            <td>下架来源</td>
            <td>下架时间</td>
            <td>上架操作</td>
        </tr>
        <?php if (isset($productOfflineLists) && $productOfflineLists) {
    ?>
        <?php foreach ($productOfflineLists as $productOffline) {
        ?>
            <tr>
                <td><?php echo $productOffline['build_name'] ?></td>
                <td>
                    <?php echo $productOffline['equipment_code']; ?>
                </td>
                <td>
                    <?php echo $productOffline['product_name']; ?>
                </td>
                <td>
                    <?php echo productOfflineRecord::$lockFrom[$productOffline['lock_from']] ?>
                </td>
                <td>
                    <?php echo !$productOffline['created_at'] ? '' : date("Y-m-d H:i", $productOffline['created_at']); ?>
                </td>
                <td>
                    <?php if ($productOffline['lock_from'] == 1 && Yii::$app->user->can('产品上架处理')) {
            echo Html::a(
                '<span class="glyphicon glyphicon-arrow-up product_line" id="' . $productOffline['equip_offline_id'] . '" build_id="' . $productOffline['build_id'] . '" equip_code="' . $productOffline['equipment_code'] . '" product_id="' . $productOffline['product_id'] . '" product_name="' . $productOffline['product_name'] . '" ></span>',
                "#"
            );
        }
        ?>
                </td>


            </tr>
        <?php }?>
        <?php }?>
    </table>
    <?php if (!isset($productOfflineLists) || !$productOfflineLists) {?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php }?>
    <?=
LinkPager::widget([
    'pagination' => $pages,
]);
?>

</div>
<?php
$url = Url::to(["product-line/product-shelves"]);
$this->registerJs('
    $(".product_line").click(function(){
        var id = $(this).attr("id");
        var buildID   =   $(this).attr("build_id");
        var equipCode   =   $(this).attr("equip_code");
        var productName =   $(this).attr("product_name");
        var productID   =   $(this).attr("product_id");
        // return confirm("确定删除吗？")
        if(confirm("确定上架此产品吗？"))
        {
            $.post(
                "' . $url . '",
                {id: id, build_id: buildID, equip_code: equipCode, product_id: productID, product_name: productName },
                    function(data){
                        if(!data){
                            $("#error").show();
                        }else{
                            $("#error").hide();
                            document.location.reload();
                        }
                    },
                "json"
            );
        }else{
            return false;
        }

    })


');

?>
