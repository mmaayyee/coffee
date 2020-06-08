<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\ScmStockNum;
use backend\models\ScmStockGram;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmStock */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '入库信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-stock-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'warehouse_id',
            'value'     => isset($model->warehouse) ? $model->warehouse->name : '暂无',
        ],
        [
            'attribute' => 'reason',
            'value'     => !isset($model->companyReasonArray[$model->reason]) ? '' : $model->companyReasonArray[$model->reason],
        ],
        [
            'attribute' => 'distribution_clerk_id',
            'value'     => isset($model->user) ? $model->user->name : '暂无',
        ],
        [
            'attribute' => 'material_id',
            'format' => 'html',
            'value'     => ScmStockNum::getScmStockNum($model->id)
        ],
        [
            'attribute' => 'material_num',
            'label'     => '散料',
            'format'    => 'html',
            'value'     => ScmStockGram::getScmStockGram($model->id)
        ],
        [
            'attribute' => 'ctime',
            'value'     => !empty($model->ctime) ? date('Y-m-d H:i:s', $model->ctime) : '暂无',
        ],
        [
            'attribute' => 'sure_time',
            'value'     => !empty($model->sure_time) ? date('Y-m-d H:i:s', $model->sure_time) : '暂无',
        ],
    ],
])?>

</div>
