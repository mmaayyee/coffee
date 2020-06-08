<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\ScmEquipType;
use backend\models\Organization;
use backend\models\BuildingTaskSetting;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipmentTaskSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '公司设备类型日常任务管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-task-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Yii::$app->user->can('添加公司设备类型日常任务') ? Html::a('添加公司设备类型日常任务', ['create'], ['class' => 'btn btn-success']) : ''; ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'equipment_type_id',
                'value' => function($model){
                    return $model->equipment_type_id ? ScmEquipType::getModel($model->equipment_type_id) : '';
                }
            ],
            [
                'attribute' => 'organization_id',
                'value' => function($model){
                    return $model->organization_id ? Organization::getOrgNameByID($model->organization_id) : '';
                }
            ],
            'cleaning_cycle',
            'day_num',
            [
                'attribute' => 'refuel_cycle',
                'value' => function($model){
                    return $model->refuel_cycle ? BuildingTaskSetting::getRuelCycle($model->refuel_cycle) : '';
                }
            ],
            [
                'attribute' => 'error_value',
                'value' => function($model){
                    return $model->error_value.'g';
                }
            ],

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons'  => [
                    'view'   => function ($url) {
                        return Yii::$app->user->can('查看公司设备类型日常任务') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                    },
                    'update' => function ($url) {
                        return Yii::$app->user->can('编辑公司设备类型日常任务') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                    },
                    'delete' =>function ($url, $model) {
                        $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get(\''.$url.'\','
                                . 'function(data){'
                                . 'if(data == 1){location.reload()}'
                                . 'else{alert(\'删除失败，请检查是否存在管理员\')}})'
                                . '};'
                                . 'return false;'
                        ];
                        return Yii::$app->user->can('删除公司设备类型日常任务') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options) : '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
