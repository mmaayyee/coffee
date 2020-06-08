<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CoffeeLanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '咖语管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
     var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
</script>
<div class="coffee-language-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', [
    'model'            => $searchModel,
    'productNameList'  => $productNameList,
    'onlineStaticList' => $onlineStaticList,
    'languageTypeList' => $languageTypeList,
]); ?>

    <p>
        <?=Html::a('新建咖语', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'attribute' => '咖语类型',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_type) ? $model->language_type : '';},
        ],
        [
            'attribute' => '咖语名称',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_name) ? $model->language_name : '';},
        ],
        [
            'attribute' => '对应饮品',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_product) ? $model->language_product : '';},
        ],
        [
            'attribute' => '咖语状态',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_static) ? $model->language_static : '';},
        ],
        [
            'attribute' => '支持设备',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_equipment) ? $model->language_equipment : '';},
        ],
        [
            'attribute' => '添加时间',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_time) ? $model->language_time : '';},
        ],
        [
            'attribute' => '咖语顺序',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->language_sort) ? $model->language_sort : '';},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{online} {view} {update} {del}',
            'buttons'  => [
                'online' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '上线',
                        'onclick' => 'if(confirm("确定上线吗？")){$.get(url+\'coffee-language-api/coffee-language-online.html?coffee_language_id=' . $model->id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["code"] == "200"){alert(datas["message"]);location.reload()}'
                        . 'else{alert(datas["message"])}})'
                        . '};'
                        . 'return false;',
                    ];
                    return (!\Yii::$app->user->can('编辑咖语信息') || $model->language_static == '上线') ? '' : Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', $url, $options, []);
                },
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看咖语详细信息') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '查看']);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑咖语信息') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑']);
                },
                'del'    => function ($url, $model, $key) {
                    $options = [
                        'title'   => '删除',
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(url+\'coffee-language-api/delete-coffee-language-info.html?coffee_language_id=' . $model->id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["code"] == "200"){alert(datas["message"]);location.reload()}'
                        . 'else{alert(\'删除失败\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('删除指定咖语信息') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>
</div>
