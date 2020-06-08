<?php

use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = $type == 1 ? '出库明细' : '入库明细';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-water-index">

    <?php echo $this->render('_search', ['model' => $model, 'action' => $type == 1 ? 'out-warehouse' : 'in-warehouse', 'managerOrgId' => $managerOrgId]); ?>
    <p>
    <?=Html::a('返回上一页', ['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left'])?>
    <?=Html::a('Excel导出', ['distribution-warehousing-details/excel-expord', 'param' => isset($param) ? $param : "", 'type' => $type], ['class' => 'btn btn-success btn-right-param pull-left'])?>
    <br/>
    </p>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style type="text/css">
            tr{
                text-align: center;
            }
            td{
                width:5%;
                border:1px solid black;
                height:20px;
            }
            .btn-right-param{
                margin-left: 10px;
            }
            p{
                height: 40px;
            }
        </style>
    <head>
    <body>
        <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td  colspan="3">物料名称</td>
            <?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
                <td colspan="<?php echo count($materialSpecificationV); ?>"><?php echo ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $materialSpecificationK])['material_type_name']; ?></td>
            <?php }?>
        </tr>

        <tr >
            <td colspan="3">单位</td>
            <?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
            <?php foreach ($materialSpecificationV as $key => $value) {?>
                <td><?php echo ScmSupplier::getSurplierDetail('name', ['id' => $value['supplier_id']])['name'] . '-' . $value['weight'] ?></td>
            <?php }}?>
        </tr>
        <tr >
            <td colspan="3">规格</td>
            <?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
            <?php foreach ($materialSpecificationV as $key => $value) {?>
                <td><?php echo $value['unit'] ?></td>
            <?php }}?>
        </tr>
        <tr >
            <td>日期</td>
            <td>项目</td>
            <td>经手人</td>
            <?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
            <?php foreach ($materialSpecificationV as $key => $value) {?>
                <td>数量</td>
            <?php }}?>

        </tr>
        <?php if (isset($warehousingDetails)) {
    ?>
        <?php
foreach ($warehousingDetails as $date => $authorArr) {
        foreach ($authorArr as $author => $projectArr) {
            foreach ($projectArr as $project => $detailsVal) {
                ?>
        <tr>
            <td><?php echo $date ?></td>
            <td><?php echo $project ?></td>
            <td><?php echo $author; ?></td>

            <?php foreach ($materialSpecificationArr as $materialTypeId => $materialTypeArr) {?>
                <?php if (isset($detailsVal[$materialTypeId])) {?>
                    <?php foreach ($materialTypeArr as $materialId => $materialArr) {?>
                        <?php if (isset($detailsVal[$materialTypeId][$materialId])) {?>
                            <td><?php echo $detailsVal[$materialTypeId][$materialId] ?></td>
                        <?php } else {?>
                            <td>0</td>
                        <?php }?>
                    <?php }?>
                <?php } else {?>
                    <?php foreach ($materialTypeArr as $key => $value) {?>
                        <td>0</td>
                <?php }}?>
            <?php }?>
        </tr>
        <?php }}}}?>

    </table>
    <?php echo LinkPager::widget(['pagination' => $pages]); ?>
    </body>

</div>
