<?php

use backend\models\MaterielDay;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\MaterielDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '物料统计/消耗统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-day-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=Yii::$app->user->can('物料分类消耗统计运维导出') ? Html::a('运维Excel导出', ['/materiel-day/excel-export', 'param' => isset($param) ? $param : "", 'type' => 0], ['class' => 'btn btn-success btn-right-param']) : '';?>
    <?=Yii::$app->user->can('物料分类消耗统计财务导出') ? Html::a('财务Excel导出', ['/materiel-day/excel-export', 'param' => isset($param) ? $param : "", 'type' => 1], ['class' => 'btn btn-success btn-right-param']) : '';?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '物料名称',
            'value' => function ($model) use ($searchModel) {
                return $model->material_type_name;
            },
        ],
        [
            'label' => '消耗量',
            'value' => function ($model) use ($searchModel) {
                if ($model->material_type_name == '水') {
                    $unin = '毫升';
                } else if ($model->material_type_name == '杯子') {
                    $unin = '个';
                } else {
                    $unin = '克';
                }
                return floatval($model->consume_total_all) . $unin;
            },
        ],
        [
            'label' => '运营状态',
            'value' => function ($model) use ($searchModel) {
                return $searchModel->online == '' ? '全部' : MaterielDay::getOperateName($searchModel->online);
            },
        ],
        [
            'label' => '付费状态',
            'value' => function ($model) {
                return empty($model->payment_state) || empty($model::$paymentState[$model->payment_state]) ? '' : $model::$paymentState[$model->payment_state];
            },
        ],
        [
            'label' => '所属地区',
            'value' => function ($model) use ($searchModel) {
                return empty($searchModel->orgId) ? '全国' : MaterielDay::getOrgName($searchModel->orgId);
            },
        ],
        [
            'label' => '日期',
            'value' => function ($model) use ($searchModel) {
                return date('Y-m-d', $model->create_at);
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons'  => [
                'view' => function ($url, $model) use ($param) {
                    return Yii::$app->user->can('物料分类消耗统计详情') ? Html::a('', ['/materiel-day/view', 'create_at' => $model->create_at, 'material_type_id' => isset($model->material_type_id) ? $model->material_type_id : 0, 'payment_state' => $model->payment_state, 'param' => $param], ['class' => 'glyphicon glyphicon-eye-open', 'title' => '详情']) : '';
                },
            ],
        ],
    ],
]);?>
<?=$searchModel->countByMaterialType?>
</div>
