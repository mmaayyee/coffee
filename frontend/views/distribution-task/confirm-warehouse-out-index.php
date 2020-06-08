<?php
$this->registerJsFile('@web/js/vconsole.min.js');
$this->title = "确认领料";

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\ScmWarehouseOutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<script>
    var vConsole = new VConsole();
</script>
<style type="text/css">
	.line-text{
		display: inline-block;
	}
	.confirm{
		margin-top: 5px;
	}
	label{
		width:40%;
	}
	.border{
		border:1px solid #ccc;
		padding:2% 3% 5% ;
		margin-bottom:10% ;
		border-radius: 10px
	}
	h5{
		text-align: center;
		font-size:16px ;
		font-weight: bold;
	}
</style>
 <div>
	<?php if (!isset($packetArr) || !$packetArr) {?>
        <div style="margin: 20% 0;text-align: center;">
            <div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:3rem;margin-bottom: 8%;"></div>
            <p style="font-size: 1.4rem">暂无数据</p>
        </div>
	<?php } else {
    ?>
	<?php foreach ($packetArr as $date => $packets) {
        foreach ($packets as $author => $statusArr) {
            foreach ($statusArr as $status => $packet) {
                ?>
<div class="confirm-the-picking">
	<div class="information">
		<div class="title">领料信息</div>
		<div class="txt">
			<p><span>领料时间：</span><?php echo $date; ?></p>
			<p><span>领料的仓库：</span><?php echo $packet['warehouseName']; ?></p>
		</div>
	</div>
    <form action="/distribution-task/confirm" method="post">
		<p class="title">出库物料信息</p>
		<table class="table table-bordered">
			<tr>
				<td>物料名称</td>
				<td>物料规格</td>
				<td>物料数量(包/个)</td>
			</tr>
			<?php if (!empty($packet['data'])) {
                    foreach ($packet['data'] as $materialId => $material) {
                        if ($material['material_out_num'] > 0) {
                            echo '<tr><td>' . $material['material_name'] . '</td><td>' . $material['format'] . '</td><td><button type="button" class="delete-btn"></button><input class="input" type="text" name="material[' . $materialId . ']" value="' . $material['material_out_num'] . '" readonly="readonly"/><button type="button" class="add-btn"></button></td></tr>';
                        }
                    }
                }
                ?>
		</table>
        <input type="hidden" name="author" value="<?php echo $author ?>">
        <input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" name="_csrf" />
		<input type="submit" class="btn btn-block btn-primary" value="确认领料">
	</form>
</div>
</div>
</div>
<?php }}}}?>
</div>
<link rel="stylesheet" href="/css/confirm-the-picking.css">
<script src="/js/operations/layout.js"></script>
<script src="/js/zepto.min.js"></script>
<script src="/js/operations/confirm-the-picking.js"></script>
