<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Building;
use backend\models\ScmEquipType;
use backend\models\Organization;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionTaskEquipSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '日常任务设置';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="distribution-task-equip-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加配送周期', ['create?flag=distribution'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('添加换料周期', ['create?flag=change'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('添加清洗周期', ['create?flag=clear'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'build_id',
                'value' => function ($model) {
                    return !empty($model->build_id) ? Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] : '';
                },
            ],
            [
                'attribute' => 'equip_type_id',
                'value' => function ($model) {
                    return !empty($model->equip_type_id) ? ScmEquipType::getEquipTypeDetail('model', ['id' => $model->equip_type_id])['model'] : '';
                },
            ],
            [
                'attribute' => 'org_id',
                'value' => function ($model) {
                    return !empty($model->org_id) ? Organization::getOrgNameByID($model->org_id) : '';
                },
            ],
            [
                'attribute' => 'material_id',
                'value' => function ($model) {
                    return !empty($model->material_id) ? \backend\models\ScmMaterial::getScmMaterialList()[$model->material_id] : '';
                },
            ],
            'cleaning_cycle',
            'refuel_cycle',
            'day_num',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    //已发布的可暂停
                    'update' => function ($url, $model, $key) {
                        //if (\Yii::$app->user->can('编辑日常任务设置')) {
                        $flag = 'distribution';
                        if ($model->cleaning_cycle > 0) {
                            $flag = 'clear';
                        } elseif ($model->refuel_cycle > 0) {
                            $flag = 'change';
                        }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/distribution-task-equip-setting/update', 'id' => $model->id, 'flag' => $flag]));
                        //}
                    },
                ],
            ],
        ],
    ]); ?>
</div>
