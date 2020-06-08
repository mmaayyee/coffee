<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\EquipExtraLog;
use common\models\EquipExtra;
use common\models\Equipments;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipExtraLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备附件记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-extra-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $_GET['EquipExtraLogSearch']['equip_id'], ['class' => 'btn btn-success'])?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'build_id',
                'value'     => function ($model) {
                    return \common\models\Building::getBuildingDetail('name', array('id' => $model->build_id))['name'];
                },
            ],
            [
                'attribute' => 'equip_extra_id',
                'value'     => function ($model) {
                    return isset($model->equip_extra_id) ? EquipExtra::getEquipExtra(false)[$model->equip_extra_id] : '';
                },
            ],
            [
                'attribute' => 'status',
                'value'     => function ($model) {
                    return isset($model->status) ? EquipExtraLog::$status[$model->status] : '';
                },
            ],
            'create_user',
            [
                'attribute'     => 'create_time',
                'value'         => function ($model) {
                    return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
                },
            ],
        ],
    ]); ?>
</div>
