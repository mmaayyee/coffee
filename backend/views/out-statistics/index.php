<?php

use backend\models\OutStatistics;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\OutStatisticsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '运维出库单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="out-statistics-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class' => 'yii\grid\SerialColumn',
        ],
        [
            'attribute' => 'org_id',
            'label'     => '机构名称',
            'value'     => function ($model) use ($organization) {
                return $organization[$model['org_id']] ?? '';
            },
        ],
        [
            'attribute' => 'material_info',
            'label'     => '物料详情',
            'format'    => 'html',
            'value'     => function ($model) use ($scmMaterial) {
                $materialArray = !empty($model['material_info']) ? Json::decode($model['material_info']) : [];
                return OutStatistics::getMaterialDetail($materialArray, $scmMaterial);
            },
        ],

        [
            'attribute' => 'status',
            'label'     => '状态',
            'value'     => function ($model) {
                return OutStatistics::$statusArray[$model['status']];
            },
        ],
        [
            'attribute' => 'date',
            'label'     => '创建时间',
            'value'     => function ($model) {
                return $model['date'];
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}{review}{update}',
            'buttons'  => [
                'view'   => function ($url, $model) {
                    return Yii::$app->user->can('查看出库单') ? Html::a('', '/index.php/out-statistics/view?id=' . $model['id'], ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                },
                'review' => function ($url, $model) {
                    if ($model['status'] == OutStatistics::OUTTED) {
                        return Yii::$app->user->can('审核出库单') ? Html::a('', 'javascript:void(0);', ['class' => 'glyphicon glyphicon-ok', 'title' => '审核', 'onClick' => 'return checkTip(' . $model['id'] . ')']) : '';
                    }
                },
                'update' => function ($url, $model) {
                    if ($model['status'] == OutStatistics::AUDIT_FAILURE) {
                        return Yii::$app->user->can('复审出库单') ? Html::a('', '/index.php/out-statistics/update?id=' . $model['id'], ['class' => 'glyphicon glyphicon-tags', 'title' => '复审']) : '';
                    }
                },
            ],
        ],
    ],
]);?>
</div>

<div class="dialog">
    <h3>出库单审核</h3>
    <p><br/><br/>
        <a href="/out-statistics/review?result=1&id=" type="button" class="btn btn-primary">审核通过</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/out-statistics/review?result=0&id=" type="button" class="btn btn-primary">审核失败</a>
    </p>
    <img src="/images/flag_close.png" alt="关闭" class="close-btn">
</div>


<script>
    function checkTip(taskid){
        $(".dialog a").each(function(e,i){
            var href =$(this).attr('href')+taskid;
                $(this).attr('href',href);
        });
        $(".dialog").show(200);
        $(".close-btn").on("click",function(){
            $(".dialog").hide(200);
        })
        return false;
    }
</script>

<style>
    .dialog{
        position:relative;
        width: 500px;
        height: 200px;
        border: solid 1px #b5b0b0;
        border-radius: 5px;
        position: absolute;
        left: 30%;
        bottom: 30%;
        background: #eee;
        line-height: 40px;
        text-align: center;
        display: none;
        font-size:16px;
    }
    .close-btn{
        position:absolute;
        right:0;
        top:0;
        width:40px;
        height:40px;
        cursor:pointer;
    }
</style>
