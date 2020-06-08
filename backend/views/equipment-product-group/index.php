<?php

use backend\models\EquipmentProductGroup;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipmentProductGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '产品组管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-product-group-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('添加产品组', ['create'], ['class' => 'btn btn-success'])?>
        <button class="btn btn-success" onclick="getIsPublicAll()">批量发布</button>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name'  => 'id',
        ],
        [
            'label' => '分组名称',
            'value' => function ($model) {
                return $model->group_name;
            },
        ],

        [
            'label' => '分组描述',
            'value' => function ($model) {
                return $model->group_desc;
            },
        ],

        [
            'label'  => '产品组料仓信息',
            'format' => 'html',
            'value'  => function ($model) use ($grouStoIdNameList) {
                return isset($grouStoIdNameList[$model->pro_group_stock_info_id]) ? Html::a($grouStoIdNameList[$model->pro_group_stock_info_id], ['/product-group-stock-info/view?id=' . $model->pro_group_stock_info_id]) : '';
            },
        ],

        [
            'label'  => '设备类型',
            'format' => 'html',
            'value'  => function ($model) use ($grouStoIdEtypeNameList) {
                return isset($grouStoIdEtypeNameList[$model->pro_group_stock_info_id]['name']) ? Html::a($grouStoIdEtypeNameList[$model->pro_group_stock_info_id]['name'], ['/scm-equip-type/view?id=' . $grouStoIdEtypeNameList[$model->pro_group_stock_info_id]['id']]) : '';
            },
        ],
        [
            'label' => '是否显示领取咖啡',
            'value' => function ($model) {return $model->setup_get_coffee ? '否' : "是";},
        ],

        [
            'label' => '发布状态',
            'value' => function ($model) {return $model->getReleaseStatus($model->release_status);},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {product} {building} {version} {delete} {copy}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view'     => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看产品组') ? '' : Html::a('查看', $url);
                },
                'update'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑产品组') ? '' : Html::a('编辑', $url);
                },
                'product'  => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看产品组单品') ? '' : Html::a('查看产品', $url);
                },
                'building' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看产品组楼宇') ? '' : Html::a('查看楼宇', $url);
                },
                'version'  => function ($url, $model, $key) {
                    $options = [
                        'onclick' => "return getIsPublic($model->product_group_id)",
                    ];
                    return (!\Yii::$app->user->can('产品组发布') || $model->release_status == $model::RELEASE_YES || ($model->is_update_product == $model::UPDATE_PRODUCT_NO && $model->is_update_recipe == $model::UPDATE_RECIPE_NO && $model->is_update_progress == $model::UPDATE_PROGRESS_NO)) ? '' : Html::a('发布', '#', $options);
                },
                'delete'   => function ($url, $model) {
                    $options = [
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/equipment-product-group/delete?id=' . $model->product_group_id . '&groupName=' . $model->group_name . '\','
                        . 'function(data){'
                        . 'if(data == 1){location.reload()}'
                        . 'else{alert(\'删除失败，请检查该策略是否绑定楼宇\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return Yii::$app->user->can('删除产品组') && EquipmentProductGroup::RELEASE_NO == $model->release_status ? Html::a('<span class="glyphicon"><b>删除</b></span>', '/equipment-product-group/delete?id=' . $model->product_group_id . '&groupName=' . $model->group_name, $options) : '';
                },
                'copy'     => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑产品组') ? '' : Html::a('复制产品组', '/equipment-product-group/update?id=' . $model->product_group_id . '&isCopy=1', ['title' => '复制产品组']);
                },
            ],
        ],
    ],
]);?>
</div>
<?=$this->render('/layouts/confirm');?>

<script type="text/javascript">
    //    单个发布
    function getIsPublic(ID)
    {
        $.ajax({
            type : "get",
            url : "/equipment-product-group/is-public",
            data : {id:ID},
            dataType : 'json',
            async : false,
            success : function(data){
                $("#confirm").modal("show");
                if (data == 1) {
                    modalSure(ID);
                } else {
                    $str = "<table class='table table-striped'>";
                    for(i in data) {
                        $str += "<tr><td>" + data[i]+ "</td></tr>";
                    }
                    $str += "</table>";
                    $("#confirm-content").html($str);
                    $("#confirm-sure").click(function () {
                        $("#confirm").modal("hide");
                    })
                }
            }
        });
        return false;
    }

    function getIsPublicAll() {
        var ids = $("tbody").find("input:checkbox:checked").map(function(index,elem) {
            return $(elem).val();
        }).get().join(",");
        if (ids.length == 0) {
            $("#confirm-content").html("至少选择一个分组名称");
            $("#confirm").modal("show");
            $("#confirm-sure").click(function () {
                $("#confirm").modal("hide");
            });
            return false;
        };
        $.ajax({
            type : "post",
            url : "/equipment-product-group/is-public-all",
            data : {ids:ids},
            dataType : 'json',
            async : false,
            success : function(even){
                var data = even.data;
                $("#confirm").modal("show");
                if (even.code == 1) {
                    var length = data.length;
                    if(length == 0){
                        modalSure(ids);
                        return false;
                    }
                    $str = "<table class='table table-striped'>";
                    for(i in data) {
                        $str += "<tr><td>" + i+ "</td>";
                        $str += "<td>" + data[i] +"<td></tr>";
                    }
                    $str += "</table>";
                    $("#confirm-content").html($str);
                    $("#confirm-sure").click(function () {
                        if (Object.keys(data).length != ids.split(",").length) {
                            modalSure(ids);
                        } else {
                            $("#confirm-sure").click(function () {
                                $("#confirm").modal("hide");
                            })
                        }
                    });
                } else {
                    $str = "<table class='table table-striped'>";
                    for(i in data) {
                        $str += "<tr><td>" + i+ "</td><td>";
                        $str += "<table class='table table-striped'>";
                        for(n in data[i]) {
                            $str += "<tr><td>" + data[i][n].replace(/[\'\"\\\/\b\f\n\r\t]/g, '') + "</td></tr>";
                        }
                        $str += "</table>";
                        $str += "<td></tr>";
                    }
                    $str += "</table>";
                    $("#confirm-content").html($str);
                    $("#confirm-sure").click(function () {
                        $("#confirm").modal("hide");
                    })
                }
            }
        });
    }

    function modalClick(event)
    {
        $("#confirm").modal("hide");
        window.location.href="<?php echo Yii::$app->params['erpapi'] ?>equipment-product-group/version?id="+event.currentTarget.getAttribute("groupid");
    }

    function modalSure(ID){
        $("#confirm-content").html("你确定要发布吗？");
        $("#confirm-sure").attr('groupid',ID);
        $("#confirm-sure").on("click",modalClick);
        $('#confirm').on('hidden.bs.modal', function () {
            $("#confirm-sure").off("click");
        })
    }
</script>