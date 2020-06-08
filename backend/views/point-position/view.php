<?php

use backend\models\PointPosition;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PointEvaluation */

$this->title                   = $model->point_id;
$this->params['breadcrumbs'][] = ['label' => '楼宇列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="point-evaluation-view">
    <h1><?=Html::encode($this->title)?></h1>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'label' => '楼宇名称',
            'value' => $model->point_name,
        ],
        [
            'label' => '状态',
            'value' => PointPosition::$pointStatusList[$model->point_status] ?? '',
        ],
        [
            'label' => '渠道',
            'value' => $pointTypeList[$model->point_type_id] ?? '',
        ],
        [
            'label' => '省份',
            'value' => $model->province,
        ],
        [
            'label' => '城市',
            'value' => $model->city,
        ],
        [
            'label' => '行政区',
            'value' => $model->area,
        ],
        [
            'label' => '楼宇地址',
            'value' => $model->address,
        ],
        [
            'label' => '日人流量',
            'value' => $model->day_peoples . "万",
        ],
        [
            'label' => '合作方式',
            'value' => PointPosition::$cooperationTypeList[$model->cooperation_type] ?? '',
        ],
        [
            'label' => '付费周期',
            'value' => PointPosition::$payCycleList[$model->pay_cycle] ?? '',
        ],
        [
            'label'  => '照片',
            'format' => 'raw',
            'value'  => '<img src="' . Yii::$app->params['fcoffeeUrl'] . $model->point_img . '"/>',
        ],
        [
            'label' => '楼宇介绍',
            'value' => $model->point_description,
        ],
    ],
])?>
<p>包含点位</p>
<table class="table table-striped table-bordered detail-view"><tbody>
<tr>
    <th>点位名称</th>
    <td>点位状态</td>
    <td>销量星级</td>
</tr>
<?php foreach ($pointList as $point): ?>
    <tr>
        <th><?=$point[0]?></th>
        <td><?=PointPosition::$pointStatusList[$point[1]] ?? ''?></td>
        <td><?=$point[1] == PointPosition::STATUS_PUT_IN && isset($point[2]) ? PointPosition::$starLevelList[$point[2]] ?? '' : ''?></td>
    </tr>
<?php endforeach?>
</tbody></table>
</div>
