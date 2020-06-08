<?php

use common\models\Wxdepartment;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WxDepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '部门管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
    $('.del-confirm').click(function(){
        var url = $(this).attr('href');
        $('#confirm-content').text('确认要删除吗？');
            cart_id = $(this).next().val();
            $('#confirm').modal();
            $('#confirm-sure').click(function (){
                location.href=url;
            })
        return false;
    })
");
?>
<div class="wx-department-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->can('添加部门')) {?>
            <?=Html::a('新建部门', ['create'], ['class' => 'btn btn-success'])?>
        <?php }if (Yii::$app->user->can('同步部门')) {?>
            <?=Html::a('从微信同步部门', ['sync-department'], ['class' => 'btn btn-success'])?>
        <?php }?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'name',
        [
            'attribute' => 'parentid',
            'value'     => function ($model) {
                return isset($model->parentid) ? Wxdepartment::getDepartName($model->parentid) : '';
            },
        ],
        [
            'attribute' => 'headquarter',
            'value'     => function ($model) {
                return $model->headquarter ? Wxdepartment::$headquarter[$model->headquarter] : '';
            },
        ],
        [
            'label' => '所属分公司',
            'value' => function ($model) use($searchModel) {
                return isset($searchModel->orgArr[$model->org_id]) ? $searchModel->orgArr[$model->org_id] : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return Yii::$app->user->can('编辑部门') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                },
                'delete' => function ($url, $model, $key) {
                    return Yii::$app->user->can('删除部门') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash del-confirm', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
<?=$this->render('/layouts/confirm');?>
