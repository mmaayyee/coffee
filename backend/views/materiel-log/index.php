<?php

use backend\models\MaterielLog;
use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MaterielLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '物料消耗记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-log-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'productIDNameList' => $productIDNameList]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '操作类型',
            'value' => function ($model) {
                return isset(MaterielLog::$operactionTypeList[$model->operaction_type]) ? MaterielLog::$operactionTypeList[$model->operaction_type] : '';
            },
        ],
        [
            'label' => '动作类型',
            'value' => function ($model) {
                return $model->activity_type;
            },
        ],
        [
            'label' => '执行时间',
            'value' => function ($model) {
                return date('Y-m-d H:i:s', $model->create_at);
            },
        ],
        [
            'label' => '设备编号',
            'value' => function ($model) {
                return $model->equipment_code;
            },
        ],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return $model->build_name;
            },
        ],
        [
            'label' => '产品名称',
            'value' => function ($model) use ($productIDNameList) {
                return $model->product_id && !empty($productIDNameList[$model->product_id]) ? $productIDNameList[$model->product_id] : '';
            },
        ],
        [
            'label'  => '消费记录ID',
            'format' => 'raw',
            'value'  => function ($model) {
                return Html::a($model->consume_id, '/user-consume/view?id=' . $model->consume_id, ['target' => "_blank"]);
            },
        ],
        [
            'label'  => '详情',
            'format' => 'html',
            'value'  => function ($model) {
                return MaterielLog::descJsonDecode($model->desc);
            },
        ],
    ],
]);?>
<?=$searchModel->countByMaterialType?>
</div>
