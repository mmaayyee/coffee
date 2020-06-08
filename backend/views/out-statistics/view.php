<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\models\OutStatistics */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '运维出库单详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="out-statistics-view">

    <h1><?=Html::encode($this->title)?></h1>

<div class="out-statistics-view">
    <label>出库单详情</label>
    <table border="1" class="table table-striped table-bordered">
        <tr>
            <td>物料种类</td>
            <td>名称</td>
            <td>数量</td>
        </tr>
        <?php echo $outStatisticsDetail; ?>
    </table>
</div>
    <span></span>
<div class="out-statistics-view">
    <label>预估单详情</label>
    <table border="1" class="table table-striped table-bordered">
        <tr>
            <td>物料种类</td>
            <td>名称</td>
            <td>数量</td>
        </tr>
        <?php echo $estimateStatisticsDetail; ?>
    </table>
</div>
    <span></span>
<div class="out-statistics-view">
    <label>出库单与预估单差值</label>
    <table border="1" class="table table-striped table-bordered">
        <tr>
            <td>物料种类</td>
            <td>名称</td>
            <td>数量</td>
        </tr>
        <?php echo $diffStatisticsDetail; ?>
    </table>
</div>
<div class="out-statistics-view">
    <label>运维专员领料详情</label>
    <table border="1" class="table table-striped table-bordered">
        <tr>
            <td>运维专员</td>
            <td>预估物料值</td>
            <td>出库物料值</td>
            <td>差值</td>
            <td>确认领料时间</td>
        </tr>
        <?php echo $collectMaterialDetail; ?>
    </table>
</div>
