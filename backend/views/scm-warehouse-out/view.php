<?php

use backend\models\ScmStock;
use yii\helpers\Html;
use yii\widgets\DetailView;
$stockModel = new ScmStock();
/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseOut */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '出库单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-out-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'warehouse_id',
            'value'     => isset($model->warehouse) ? $model->warehouse->name : '暂无',
        ],
        'author',
        [
            'attribute' => 'material_id',
            'value'     => $stockModel->getCompanymaterial($model->material_id),
        ],
        'material_out_num',
        [
            'attribute' => 'status',
            'value'     => $model->status == 1 ? '未领料' : '已领料',
        ],
        [
            'attribute' => 'ctime',
            'value'     => !empty($model->ctime) ? date('Y-m-d H:i:s', $model->ctime) : '未领料',
        ],
    ],
])?>

</div>
