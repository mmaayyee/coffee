<?php

use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\MaterielDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '物料统计/楼宇消耗统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-day-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search_building', ['model' => $searchModel]); ?>
    <?=Yii::$app->user->can('物料楼宇消耗统计运维导出') ? Html::a('运维Excel导出', ['/materiel-day/excel-export-build', 'param' => !empty($param) ? $param : "", 'type' => 0], ['class' => 'btn btn-success btn-right-param']) : '';?>
    <?=Yii::$app->user->can('物料楼宇消耗统计财务导出') ? Html::a('财务Excel导出', ['/materiel-day/excel-export-build', 'param' => !empty($param) ? $param : "", 'type' => 1], ['class' => 'btn btn-success btn-right-param']) : '';?>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return $model->build_name;
            },
        ],
        [
            'label' => '物料名称',
            'value' => function ($model) {
                return $model->material_type_name;
            },
        ],
        [
            'label' => '消耗量',
            'value' => function ($model) {
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
            'label' => '日期',
            'value' => function ($model) {
                return date('Y-m-d', $model->create_at);
            },
        ],
    ],
]);?>
<?=$searchModel->countByMaterialType?>
</div>
