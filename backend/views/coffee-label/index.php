<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CoffeeLabelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '产品标签管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-label-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加产品标签') ? Html::a('添加产品标签', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        //['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '排序',
            'format' => 'raw',
            'value'  => function ($model) {
                $url     = '/coffee-label/changeSort?id=' . $model->id;
                $options = [
                    'onclick' => '$(this).parent().find("input").removeAttr("disabled");;return false;',
                ];
                return '<input onBlur="changeSort(this,' . $model->id . ')" style="width:25px" disabled="disabled" type="text" value="' . $model->sort . '"> ' . Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
            },
        ],
        [
            'label'  => '名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->label_name;
            },
        ],
        [
            'label'  => '类型',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->access_status == 1 ? '默认' : '非默认';
            },
        ],
        [
            'label'  => '图标(选中前)',
            'format' => 'html',
            'value'  => function ($model) {
                return Html::img(Yii::$app->params['fcoffeeUrl'] . $model->desk_img_url, ['alt' => '', 'width' => '50']);
            },
        ],
        [
            'label'  => '图标(选中后)',
            'format' => 'html',
            'value'  => function ($model) {
                return Html::img(Yii::$app->params['fcoffeeUrl'] . $model->desk_selected_img_url, ['alt' => '', 'width' => '50']);
            },
        ],
        [
            'label'  => '标签',
            'format' => 'html',
            'value'  => function ($model) {
                return Html::img(Yii::$app->params['fcoffeeUrl'] . $model->label_img_url, ['alt' => '', 'width' => '50']);
            },
        ],
        [
            'label'  => '状态',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->online_status == 1 ? '上线' : '下线';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{online} {view} {update} {del}',
            'buttons'  => [
                'online' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '上线',
                        'onclick' => 'if(confirm("确定上线吗？")){$.get(\'/coffee-label/online?id=' . $model->id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'上线失败\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return (!\Yii::$app->user->can('上线产品标签') || $model->online_status == 1) ? '' : Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', $url, $options, []);
                },
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看产品标签') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '查看']);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑产品标签') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑']);
                },
                'del'    => function ($url, $model, $key) {
                    $options = [
                        'title'   => '删除',
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/coffee-label/del?id=' . $model->id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'删除失败\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('删除产品标签') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>
</div>
<script type="text/javascript">
    //监控input框情况
    function changeSort(e,id){
        var data = {};
        data.sort = $(e).val();
        var urls = 'change-sort?id='+id;
        //获取值发送ajax请求
        $.ajax({
            type:'get',
            url:urls,
            data:data,
            dataType:'json',
            success:function(result){
                if(result['status'] == 'success'){
                    //刷新
                    location.reload();
                }else{
                    alert('修改失败!');
                }
            },
            error:function(){
                alert('请求失败!');
            }
        });
    }
</script>
