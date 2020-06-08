<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '水单记录管理';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
p{
    height: 40px;
}
</style>
<div class="distribution-water-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
    <?= Html::a('返回上一页',['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left']) ?> 
    </p>
    <?php $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => '楼宇名称',
                'value' => function($model) {
                    return \common\models\Building::getBuildingDetail('name', ['id'=> $model->build_id])['name'];
                }
            ],
            [
                'label' => '配送水量',
                'value' => function($model) {
                    return $model->need_water.'桶';
                }
            ],
            [
                'label' => '下单时间',
                'value' => function($model) {
                    return $model->order_time ? date("Y-m-d H:i", $model->order_time) : '';
                }
            ],
            [
                'label' => '供应商',
                'value' => function($model) {
                    return \backend\models\ScmSupplier::getSurplierDetail('name', ['id'=> $model->supplier_id])['name'];
                }
            ],
            [
                'label' => '到达时间',
                'value' => function($model) {
                    return $model->upload_time ? date("Y-m-d H:i", $model->upload_time) : '';
                }
            ],
            
        ];
        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_CSV => false
            ]
        ]);
    ?>
    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns
        ]); 
    ?>

</div>
