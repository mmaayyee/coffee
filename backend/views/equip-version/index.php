<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipVersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备版本信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-version-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel,'groupList' => $groupList]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'equip_code',
        [
            'attribute' => 'build_name',
            'value'     => function ($model) {
                return !isset($model->equip->build->name) ? '' : $model->equip->build->name;
            },
        ],
        [
            'attribute' => 'group_id',
            'value'     => function ($model)use($groupList) {
                return isset($groupList[$model->group_id]) ? $groupList[$model->group_id] : '';
            },
        ],
        'app_version',
        'main_control_version',
        'group_version',
        'io_version',
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return !$model->create_time ? '' : date('Y-m-d H:i:s', $model->create_time);
            },
        ],
        // ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>
</div>
