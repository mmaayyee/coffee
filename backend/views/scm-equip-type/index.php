
<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备类型管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-equip-type-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加设备类型') ? Html::a('添加设备类型', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'model',
        [
            'attribute' => 'supplier_id',
            'format'    => 'text',
            'value'     => function ($model) {
                if ($model->supplier) {
                    return $model->supplier->name;
                }

            },
        ],
        'equip_type_alias',
        [
            'attribute' => 'miscellaneousMaterial',
            'format'    => 'raw',
            'value'     => function ($model) {
                return $model::getMiscellaneousMaterial($model->id);
            },
        ],
        [
            'attribute' => 'readable_attribute',
            'format'    => 'raw',
            'value'     => function ($model) {
                return isset($model->readable_attribute) ? $model::getReadableAttributeValue($model->readable_attribute) : '';
            },
        ],
        [
            'attribute' => 'matstock',
            'format'    => 'raw',
            'value'     => function ($model) {
                return $model->getmaterialStocks($model->id);
            },
        ],
        'stock_num',
        [
            'attribute' => 'empty_box_weight',
            'format'    => 'text',
            'value'     => function ($model) {
                $text = '';
                if (!empty($model->empty_box_weight)) {
                    $emptyBoxWeight = Json::decode($model->empty_box_weight);
                    //空料盒重量显示
                    foreach ($emptyBoxWeight as $stockId => $weight) {
                        if ($stockId == 9) {
                            $text .= 'G号料仓-' . $weight . 'g,';
                        } else {
                            $text .= $stockId . '号料仓-' . $weight . 'g,';
                        }
                    }
                    $text = substr($text, 0, -1);
                }
                return $text;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {config}',
            'buttons'  => [
                'view'   => function ($url) {
                    return Yii::$app->user->can('查看物料分类') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                },

                'update' => function ($url) {
                    return Yii::$app->user->can('编辑物料分类') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                },

                'config' => function ($url) {
                    return Yii::$app->user->can('配置设备分类参数') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-cog', 'title' => '配置分类参数']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
