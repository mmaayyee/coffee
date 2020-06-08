<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EquipmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '设备信息管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/jquery-1.9.1.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/js/echarts.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/equipments-charts/equipments-charts.js', ['position' => View::POS_END]);
?>
<style>
.charts {
  width: 1060px;
  height: 500px;
}
.charts-pie {
  width: 800px;
  height: 650px;
  margin-top: 5px;
}
.chart-txt {
    width: 800px;
    margin: 0;
    padding: 0;
    height:30px;
    line-height:30px;
    text-align:center;
}
</style>
<script type="text/javascript">
    var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
<div class="equipments-index">
    <h1><?=Html::encode($this->title);?></h1>
    <?php if (\Yii::$app->user->can('查看饼状图')): ?>
        <p class="chart-txt">&nbsp;&nbsp;<strong>选择日期: </strong><input type="text" id="datepicker" value="" readonly style="width:90px;height:25px;"/></p>
        <div id="pieChart" class="charts-pie"></div>
        <div id="pieChartTxt" class="chart-txt" style="display: none">分公司无数据，请选择其他日期</div>
        <div id="pieChart2" class="charts-pie"></div>
        <div id="pieChartTxt2" class="chart-txt" style="display: none">代理商公司无数据，请选择其他日期</div>
    <?php endif?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加设备信息') ? Html::a('添加设备批次', ['create'], ['class' => 'btn btn-success']) : '';?>

        <?=Yii::$app->user->can('查看设备信息') ? Html::a('设备地图模式', ['map'], ['class' => 'btn btn-success']) : '';?>

        <?=Yii::$app->user->can('设备数据统计') ? Html::a('设备数据统计', ['equip-sync'], ['class' => 'btn btn-success']) : '';?>

        <?=Yii::$app->user->can('导出设备信息Excel') ? Html::a('Excel导出', ['/equipments/excel-export', 'param' => isset($param) ? $param : ""], ['class' => 'btn btn-success btn-right-param']) : '';?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'equip_code',
        [
            'attribute' => 'build_id',
            'format'    => 'html',
            'value'     => function ($model) {
                return isset($model->build) ? Html::a($model->build->name, ['/building/view?id=' . $model->build_id]) : '';
            },
        ],
        [
            'attribute' => 'equip_type_id',
            'format'    => 'html',
            'value'     => function ($model) {
                if ($model->equipTypeModel) {
                    return Html::a($model->equipTypeModel->model, ['/scm-equip-type/view?id=' . $model->equip_type_id]);
                }
            },

        ],
        [
            'attribute' => 'pro_group_id',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->pro_group_id > 0 ? Html::a($model->proGroupList($model->pro_group_id), ['/equipment-product-group/view?id=' . $model->pro_group_id]) : '';
            },
        ],
        [
            'attribute' => 'org_id',
            'format'    => 'text',
            'value'     => function ($model) use ($searchModel) {return isset($searchModel->orgArr[$model->org_id]) ? $searchModel->orgArr[$model->org_id] : '';},
        ],
        [
            'label' => '机构类型',
            'value' => function ($model) use ($orgList) {
                return $orgList[$model->org_id] ?? '';
            },
        ],

        [
            'attribute' => 'equipment_status',
            'value'     => function ($model) {
                return $model->getEquipStatus();
            },
        ],
        [
            'attribute' => 'operation_status',
            'value'     => function ($model) {
                return $model->getOperationStatus();
            },
        ],
        [
            'attribute' => 'is_lock',
            'value'     => function ($model) {
                return $model->is_lock ? $model::$lock[$model->is_lock] : '';
            },
        ],
        'last_log',
        [
            'attribute' => 'last_update',
            'value'     => function ($model) {
                return $model->last_update ? date('Y-m-d H:i:s', $model->last_update) : '';
            },
        ],
        [
            'attribute' => 'bluetooth_name',
            'value'     => function ($model) {
                return $model->bluetooth_name ? $model->bluetooth_name : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{view} {update} {weight} {unweight}{open}',
            'buttons'  => [
                'update'   => function ($url) {
                    return Yii::$app->user->can('编辑设备信息') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                },

                'weight'   => function ($url, $model, $key) {
                    if (!$model->weight) {
                        return Yii::$app->user->can('置顶设备') ? Html::a('<span title="置顶" class="glyphicon glyphicon-bookmark equip_weight"><input class="" type="hidden" value="' . $model->id . '"/></span>') : '';
                    }
                },

                'unweight' => function ($url, $model, $key) {
                    if ($model->weight) {
                        return Yii::$app->user->can('置顶设备') ? Html::a('<span title="非置顶" style="color:red;" class="glyphicon glyphicon-remove-sign un_equip_weight"><input class="" type="hidden" value="' . $model->id . '"/></span>') : '';
                    }
                },
                'open'     => function ($url, $model, $key) {
                    $options = [
                        'title'      => '开门',
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax'  => '0',
                    ];

                    return !\Yii::$app->user->can('远程开门') ? '' : Html::a('<span class="glyphicon glyphicon glyphicon-bell"></span>', Url::to(["equipments/open", 'id' => $model->id]), $options);
                },
            ],
        ],

        [
            'label'  => '地图',
            'format' => 'raw',
            'value'  => function ($model) {
                if ($model->build_id) {
                    return Html::a('位置', ['equipments/map', 'build_id' => $model->build_id], ['class' => 'btn btn-success']);
                } else {
                    return "暂无";
                }
            },
        ],

    ],
]);?>

</div>

<?php
$equipLockUrl   = Url::to(["equipments/equip-lock-status"]);
$equipUnlockUrl = Url::to(["equipments/equip-unlock-status"]);
$equipWeight    = Url::to(['equipments/equip-weight']);
$unEquipWeight  = Url::to(['equipments/un-equip-weight']);
$this->registerJs('
    //增加权重，添加数据库中的权重信息
    $(".equip_weight").click(function(){
        var equipWeightId  =  $(this).find("input").val();
        $.post("' . $equipWeight . '",{equipWeightId:equipWeightId},function(data){
            if(data="true"){
                window.location.reload();
            }
        });
    })

    //增加权重，添加数据库中的权重信息
    $(".un_equip_weight").click(function(){
        var equipWeightId  =  $(this).find("input").val();
        $.post("' . $unEquipWeight . '",{equipWeightId:equipWeightId},function(data){
            if(data="true"){
                window.location.reload();
            }
        });
    })

');

?>