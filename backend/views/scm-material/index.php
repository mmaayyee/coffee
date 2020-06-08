<?php

use backend\models\ScmMaterialType;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmmaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '物料信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-material-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->can('添加物料')) {?>
        <?=Html::a('添加物料', ['create'], ['class' => 'btn btn-success'])?>
        <?php }?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'supplier_id',
            'value'     => function ($model) {
                if (!empty($model->supplier)) {
                    return $model->supplier->name;
                }
            },
        ],
        'name',
        [
            'attribute' => 'weight',
            'value'     => function ($model) {
                return $model->weight ? $model->weight.' '.ScmMaterialType::getMaterialTypeDetail('*', ['id'=>$model->material_type])['spec_unit'] : '';
            },
        ],
        [
            'attribute' => 'material_type',
            'value'     => function ($model) {
                return $model->material_type ? ScmMaterialType::getIdNameArr()[$model->material_type] : '';
            },
        ],
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
        [
            'attribute' => 'is_operation',
            'value'     => function ($model) {
                return $model->is_operation == 1?'是':'否';
            },
        ],

        [

            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看供应商') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑供应商') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
            ],
        ],
    ],
]);?>

</div>
<?php
$url = Url::to(["scm-material/batch-delete"]);
$this->registerJs('
        $(".gridview").on("click", function () {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            if (keys.length == 0) {
                alert("请选择想要删除的内容");
            }else{
                console.log(keys);
                $.ajax({
                    type: "POST",
                    url:  "' . $url . '",
                    data: {keys: keys},
                    dataType: "json",
                    success: function(data){
                        if (data == true) {
                            window.location.reload();
                        }else{
                            alert("删除失败");
                        }
                    },
                    error: function(data){
                        alert("删除失败");
                    }
                });
            }

        });
    ');
?>
