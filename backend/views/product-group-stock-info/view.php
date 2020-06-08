<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductGroupStockInfo */

$this->title                   = '详情页';
$this->params['breadcrumbs'][] = ['label' => '产品组料仓信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-group-stock-info-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'product_group_stock_name',
            'value'     => $model->product_group_stock_name,
        ],
        [
            'attribute' => 'equip_type_id',
            'value'     => $model->equip_type_id,
        ],
    ],
])?>
    <div class="field-equipmentproductgroupstock-stock_code">
    <label>产品组料仓信息</label>
    <table class="table table-bordered table-striped">
        <tr>
            <th>编号</th>
            <th>料盒信息</th>
            <th>容量上限</th>
            <th>物料名称</th>
            <th>容量下限</th>
            <th>预警值</th>
            <th>出料速度(单位：克/秒)</th>
            <th>是否运维使用</th>
        </tr>
        <?php foreach ($stockList as $key => $stock) {?>
            <tr>
                <th><?php echo $key + 1; ?></th>
                <th><?php echo isset($stockCodeToStockName[$stock['stock_code']]) ? $stockCodeToStockName[$stock['stock_code']] : ''; ?></th>
                <th><?php echo $stock['stock_volume_bound'] ?></th>
                <th><?php echo $stock['materiel_name'] ?></th>
                <th><?php echo $stock['bottom_value']; ?></th>
                <th><?php echo $stock['warning_value']; ?></th>
                <th><?php echo $stock['blanking_rate']; ?></th>
                <th><?php echo $stock['is_operation'] == 1 ? '是' : '否'; ?></th>
            </tr>
        <?php }?>
    </table>
    </div>
</div>
