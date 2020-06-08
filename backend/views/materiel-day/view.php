<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MaterielMonthSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = $list['materialTypeName'] . date('Y.m.d', $searchModel->create_at);
$this->params['breadcrumbs'][] = ['label' => '物料统计/消耗统计', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if ($list['materialTypeName'] == '水') {
    $unin = '毫升';
} else if ($list['materialTypeName'] == '杯子') {
    $unin = '个';
} else {
    $unin = '克';
}
?>
<div class="materiel-month-index">

    <h1><?=Html::encode($list['materialTypeName']) . date('Y.m.d', $searchModel->create_at) . '总量:' . $list['totalCount'] . $unin?></h1>
    <?php echo $this->render('_search_view', ['model' => $searchModel]); ?>
    <div id="w1" class="grid-view"><div class="summary">共<b><?=!empty($list['materielDayBuildList']) ? count($list['materielDayBuildList']) : 0;?></b>条数据.</div>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>楼宇名称</th>
                <th>物料数量(<?php echo $unin; ?>)</th>
            </tr>
        </thead>
        <?php if (!empty($list['materielDayBuildList'])) {?>
    <tbody>
    <?php foreach ($list['materielDayBuildList'] as $k => $v): ?>
        <tr data-key=<?=$k?> >
            <td><?=$v['build_name']?></td>
            <td><?=$v['consume_total_all']?></td>
        </tr>
    <?php endforeach;?>
    </tbody>

    <?php }?>
    </table>

    </div>
</div>
