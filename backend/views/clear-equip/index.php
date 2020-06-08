<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClearEquipSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '清洗设备类型列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clear-equip-index">
    
    <h1><?= Html::encode($this->title) ?></h1>

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?=Html::a('添加', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'     => '编码类型',
                'value'     => function ($model) {return $model->clear_code_name;},
            ],
            [
                'label'     => '编码号',
                'value'     => function ($model) {return $model->code;},
            ],
            [
                'label'     => '设备类型',
                'value'     => function ($model) {return $model->equipment_name;},
            ],
            [
                'label'     => '备注',
                'value'     => function ($model) {return $model->remark;},
            ],
            [
                'label'     => '消耗值',
                'value'     => function ($model) {return $model->consum_total;},
            ],
            [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{delete} {update}',
            'buttons'   => [
                'delete' => function ($url, $model) {
                    $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get(\'/clear-equip/delete?id='.$model->clear_equip_id.'\','
                            . 'function(data){'
                            . 'if(data == 1){location.reload()}'
                            . 'else{alert(\'删除失败\')}})'
                            . '};'
                            . 'return false;'
                        ]; 
                    return   Html::a('<span class="glyphicon glyphicon-trash"></span>', '/clear-equip/delete?id='.$model->clear_equip_id,$options);
                },
                'update' => function ($url, $model) {
                    return   Html::a('', '/clear-equip/update?id='.$model->clear_equip_id,['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']);
                }
            ],
        ],
        ],
    ]); ?>

</div>
