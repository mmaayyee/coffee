<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MaterielBoxSpeedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '料盒速度列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-box-speed-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'     => '物料类型',
                'value'     => function ($model) {return $model->material_type_name;},
            ],
            [
                'label'     => '设备类型',
                'value'     => function ($model) {return $model->equipment_name;},
            ],
            [
                'label'     => '速度',
                'value'     => function ($model) {return $model->speed;},
            ],
            [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{delete} {update}',
            'buttons'   => [
                'delete' => function ($url, $model) {
                    $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get(\'/materiel-box-speed/delete?id='.$model->materiel_box_speed_id.'\','
                            . 'function(data){'
                            . 'if(data == 1){location.reload()}'
                            . 'else{alert(\'删除失败\')}})'
                            . '};'
                            . 'return false;'
                        ]; 
                    return   Html::a('<span class="glyphicon glyphicon-trash"></span>', '/materiel-box-speed/delete?id='.$model->materiel_box_speed_id,$options);
                },
                'update' => function ($url, $model) {
                    return   Html::a('', '/materiel-box-speed/update?id='.$model->materiel_box_speed_id,['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']);
                }
            ],
        ],
        ],
    ]); ?>

</div>
