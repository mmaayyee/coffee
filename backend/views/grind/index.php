<?php

use backend\models\Grind;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrindSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '预磨豆设置管理';
$this->params['breadcrumbs'][] = ['label' => '预磨豆设置'];
?>


<div class="grind-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('预磨豆设置编辑') ? Html::a('添加', ['create'], ['class' => 'btn btn-success']) : '';?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,

    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '范围类型',
            'value' => function ($model) {return Grind::$type[$model->grind_type];},
        ],
        [
            'label'  => '地区',
            'format' => 'html',
            'value'  => function ($model) {return Grind::getGrindBuilding($model);},
        ],
        [
            'label' => '磨豆时间',
            'value' => function ($model) {return $model->grind_time . '秒';},
        ],
        [
            'label' => '间隔时间',
            'value' => function ($model) {return Grind::countTime($model->interval_time);},
        ],
        [
            'label' => '楼宇数量',
            'value' => function ($model) {return $model->getBuildingNumber();},
        ],
        [
            'label' => '是否开启',
            'value' => function ($model) {return $model::$switchType[$model->grind_switch];},
        ],
        [
            'label' => '备注',
            'value' => function ($model) {return $model->grind_remark;},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons'  => [
                'delete' => function ($url, $model) {
                    $options = [
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/grind/delete?id=' . $model->grind_id . '\','
                        . 'function(data){'
                        . 'if(data == 1){location.reload()}'
                        . 'else{alert(\'删除失败,网络问题\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return Yii::$app->user->can('预磨豆设置删除') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', '/grind/delete?id=' . $model->grind_id, $options) : '';
                },
                'update' => function ($url, $model) {
                    return Yii::$app->user->can('预磨豆设置编辑') ? Html::a('', '/grind/update?id=' . $model->grind_id, ['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                },
            ],
        ],
    ],
]);?>

</div>

