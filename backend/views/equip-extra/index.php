<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\EquipExtra;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EquipExtraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备附件';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-extra-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加设备附件', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'extra_name',
            [
                'attribute' => 'is_del',
                'value' => function($model){
                    return $model->is_del ? EquipExtra::$status[$model->is_del] : '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'],

        ],
    ]); ?>
</div>
