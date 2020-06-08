<?php

use common\helpers\Tools;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '工厂模式操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .coffee-label-index dl{
        float: left;
        min-width: 160px;
    }
    .coffee-label-index dl dt{
        float: left;
        margin-right: 10px;
    }
    .coffee-label-index dl dd{
        float: left;
    }
    #w1{
        clear:both;
    }
</style>
<div class="coffee-label-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', [
    'model'           => $searchModel,
    'orgIdNameList'   => $orgIdNameList,
    'buildIdNameList' => $buildIdNameList]); ?>
<h4>物料消耗汇总：</h4>
<div><?php echo $masterialInfo; ?></<div>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'label' => '联营方名称',
            'value' => function ($model) {
                return $model->org_name;
            },
        ],
        [
            'label' => '设备编号',
            'value' => function ($model) {
                return $model->equip_code;
            },
        ],
        [
            'label' => '点位名称',
            'value' => function ($model) {
                return $model->build_name;
            },
        ],
        [
            'label' => '操作',
            'value' => function ($model) {
                return $model->operaLogIdNameList[$model->operation_id] ?? '';
            },
        ],
        [
            'label' => '是否消耗物料',
            'value' => function ($model) {
                return $model->is_consume_material == 1 ? '是' : '否';
            },
        ],
        [
            'label'  => '详情',
            'format' => 'raw',
            'value'  => function ($model) {
                return $model->getMaterialInfo();
            },
        ],
        [
            'label' => '时间',
            'value' => function ($model) {
                return Tools::getDateByTime($model->create_time);
            },
        ],
    ],
]);?>
</div>
