<?php

use backend\models\SpeechControl;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SpeechControlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '语音控制';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speech-control-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('添加语音控制', ['create'], ['class' => 'btn btn-success'])?>
    </p>
     <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => '语音控制标题',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->speech_control_title;
            },
        ],
        [
            'attribute' => '语音控制内容',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->speech_control_content;
            },
        ],
        [
            'attribute' => '创建时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->create_time);
            },
        ],
        [
            'attribute' => '审核时间',
            'value'     => function ($model) {
                return $model->examine_time > 0 ? date('Y-m-d H:i:s', $model->examine_time) : '暂无';
            },
        ],
        [
            'attribute' => '开始时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->start_time);
            },
        ],
        [
            'attribute' => '结束时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->end_time);
            },
        ],
        [
            'attribute' => '状态',
            'value'     => function ($model) {
                $status = '';
                if ($model->status == SpeechControl::NO_CONFIRM) {
                    $status = '待审核';
                } elseif ($model->status == SpeechControl::NO_ONLINE) {
                    $status = '待上线';
                } elseif ($model->status == SpeechControl::IS_REFUSE) {
                    $status = '已拒绝';
                } elseif ($model->status == SpeechControl::IS_ONLINE) {
                    $status = '上线';
                } elseif ($model->status == SpeechControl::IS_DOWNLINE) {
                    $status = '下线';
                }
                return $status;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}{update}{examine}',
            'buttons'  => [
                'view'    => function ($url, $model) {
                    return Yii::$app->user->can('查看语音控制') ? Html::a('', '/index.php/speech-control/view?id=' . $model['id'], ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                },
                'update'  => function ($url, $model) {

                    return Yii::$app->user->can('编辑语音控制') ? Html::a('', '/index.php/speech-control/update?id=' . $model['id'], ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';

                },
                'examine' => function ($url, $model) {
                    if ($model['status'] == SpeechControl::NO_CONFIRM) {
                        return Yii::$app->user->can('审核语音控制') ? Html::a('', 'javascript:void(0);', ['class' => 'glyphicon glyphicon-tags', 'title' => '审核', 'onClick' => 'return checkTip(' . $model['id'] . ')']) : '';
                    }
                },

            ],
        ],
    ],
]);?>
</div>
<div class="dialog">
    <h3>审核语音控制</h3>
    <p><br/><br/>
        <a href="/speech-control/examine?result=1&id=" type="button" class="btn btn-primary">审核通过</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/speech-control/examine?result=0&id=" type="button" class="btn btn-primary">审核失败</a>
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
