<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $model common\models\Equipments */
$this->title                   = '本机配方调整修改日志';
$this->params['breadcrumbs'][] = ['label' => '本机配方调整', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="formula-wrap">
<div class="formula-adjustment-view">
    <p>
         <?=Html::a('配方调整', ['equipments/formula-adjustment', 'equipTypeId' => $equipTypeId, 'equip_code' => $equipCode], ['class' => 'btn btn-default']);?>
         <?=Html::a('配方调整修改日志', ['equipments/formula-adjustment-log', 'equipTypeId' => $equipTypeId, 'equip_code' => $equipCode], ['class' => 'btn btn-default']);?>
    </p>
    <?php echo $this->render('_formula_log_search', ['model' => $model, 'equipCode' => $equipCode]); ?>
</div>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'attribute' => '设备编号',
            'value'     => function ($model) {
                return !empty($model->equipment_code) ? $model->equipment_code : '';
            },
        ],
        [
            'attribute' => '操作人员',
            'value'     => function ($model) {
                return !empty($model->username) ? $model->username : '';
            },
        ],
        [
            'attribute' => '修改时间',
            'value'     => function ($model) {
                return !empty($model->update_time) ? date('Y-m-d H:i:s', $model->update_time) : '';
            },
        ],
        [
            'attribute' => '修改内容',
            'format'    => 'html',
            'value'     => function ($model) {
                $formulaInfo = !empty($model->formula_info) ? Json::decode($model->formula_info) : [];
                $msg         = '';
                foreach ($formulaInfo as $stockCode => $values) {
                    $formulaValue = explode('|', $values);
                    $oldValue     = $formulaValue[0];
                    $newValue     = $formulaValue[1];
                    $msg .= $stockCode . '号料仓由' . $oldValue . '%改为' . $newValue . '%<br/>';
                }
                return $msg;
            },
        ],
    ],
]);?>
</div>

