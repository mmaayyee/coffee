<?php

use backend\models\ScmStock;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmTotalInventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '库存信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-total-inventory-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,

    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'warehouse_id',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->warehouse_id && $model->warehouse ? $model->warehouse->name : '';
            },
        ],

        [
            'attribute' => 'material_id',
            'label'     => '物料',
            'format'    => 'html',
            'value'     => function ($model) {
                return ScmStock::getTotalInventory($model->warehouse_id);
                //$stockModel = new \backend\models\ScmStock();
                //return $stockModel->getCompanymaterial($model->material_id);
            },
        ],
        [
            'label'     => '散料',
            'format'    => 'html',
            'attribute' => 'total_number',
            'value'     => function ($model) {
                /*$num = isset($model->total_number) ? $model->total_number : 0;
                $unit = isset($model->material->materialType->unit) ? $model->material->materialType->unit : '';
                return $num . $unit;*/
                return \backend\models\ScmTotalInventoryGram::getTotalGram($model->warehouse_id);
            },
        ],

    ],
]);?>
    <h1>总统计</h1>
    <table class="table table-bordered ">
        <tr>
            <td>
                物料
            </td>
            <td>
                散料
            </td>
        </tr>
     <!--   --><?php /*foreach ($totalinventory as $totalKey => $totalValue) {
 */?>
        <tr>
            <td>
               <!-- --><?php /*$stockModel = new \backend\models\ScmStock();
echo $stockModel->getCompanymaterial($totalValue['material_id'])*/?>
                <?php echo \backend\models\ScmStock::getTotalInventoryByMaterialId(); ?>
            </td>
            <td>
                <?php echo \backend\models\ScmTotalInventoryGram::getTotalInventory(); ?>
                <?php //echo $totalValue['total_number'].' '.ScmMaterialType::getMaterialTypeDetail("*", ['id'=>ScmMaterial::getMaterialDetail("*", ['id'=>$totalValue['material_id']])['material_type']])['unit'] ?>
            </td>
        </tr>
        <?php //}?>
    </table>


</div>
