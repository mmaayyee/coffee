<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CoffeeProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '单品管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-product-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=!\Yii::$app->user->can('添加单品') ? '' : Html::a('添加单品', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => '单品名称',
            'attribute' => 'cf_product_name',
        ],
        [
            'label'     => '单品价格(元)',
            'attribute' => 'cf_product_price',
        ],
        [
            'label'     => '手机端特价',
            'attribute' => 'cf_special_price',
        ],
        [
            'label'     => '口感',
            'attribute' => 'cf_texture',
        ],
        [
            'label'     => '冷热类型',
            'attribute' => 'cf_product_hot',
            'value'     => function ($model) {return $model->getType();},
        ],
        [
            'label'     => '单品状态',
            'attribute' => 'cf_product_status',
            'value'     => function ($model) {return $model->getStatus();},
        ],
        [
            'label'     => '单品类型',
            'attribute' => 'cf_product_type',
            'value'     => function ($model) {return $model->getProductType();},
        ],
        [
            'label'     => '饮品种类',
            'attribute' => 'cf_product_kind',
            'value'     => function ($model) {return $model->getProductKind();},
        ],
        [
            'label'     => 'icon图',
            'attribute' => 'cf_product_thumbnail',
            'format'    => 'raw',
            'value'     => function ($model) {return $model->getCover($model->cf_product_thumbnail);},
        ],
        [
            'label' => '发布状态',
            'value' => function ($model) use ($releaseStatus) {
                if (isset($releaseStatus[$model->cf_product_id]) && $releaseStatus[$model->cf_product_id] == 1) {
                    return '已发布';
                } elseif (isset($releaseStatus[$model->cf_product_id]) && $releaseStatus[$model->cf_product_id] == 0) {
                    return '未发布';
                } else {
                    return '不可发布';
                }
            },
        ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update}  {delete} {list} {release} {copy}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'list'    => function ($url, $model, $key) {
                    $options = [
                        'title'      => '推荐到首页',
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax'  => '0',
                        'class'      => 'menu',
                    ];
                    return !\Yii::$app->user->can('推荐单品') ? '' : Html::a('<span class="glyphicon glyphicon-home"></span>', url::to(['index-recommend/create', 'sid' => $model->cf_product_id]), $options);
                },
                'update'  => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑单品') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delete'  => function ($url, $model, $key) {
                    $options = [
                        'onclick' => 'return confirm("确定删除吗？确认后删除其下相应的配方和进度条。");',
                    ];
                    return !\Yii::$app->user->can('删除单品') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
                'release' => function ($url, $model, $key) use ($releaseStatus) {
                    return !\Yii::$app->user->can('发布单品') || (isset($releaseStatus[$model->cf_product_id]) && $releaseStatus[$model->cf_product_id] == 1) ? '' : Html::a('<span class="glyphicon glyphicon-flag"></span>', $url, ['data-confirm' => '确认发布?']);
                },
                'copy'    => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑单品') ? '' : Html::a('<span class="glyphicon glyphicon-copyright-mark"></span>', '/coffee-product/update?id=' . $model->cf_product_id . '&isCopy=1', ['title' => '复制单品']);
                },
            ],
        ],
    ],
]);?>

</div>
