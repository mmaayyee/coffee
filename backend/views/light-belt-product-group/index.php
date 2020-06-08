<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LightBeltProductGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '灯带饮品组管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-product-group-index">


<div class="advert-index">
    <?php echo $this->render('_search', ['model' => $model]); ?>
    <div id="error" style="color: red; display: none;">删除失败，请检测是否使用</div>

    <?php if(Yii::$app->user->can('添加饮品组')){ ?>
    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>

    <table class="table table-bordered">
        <tr>
            <td>序号</td>
            <td>饮品名称</td>
            <td>所选饮品组</td>
            <td>操作</td>
        </tr>
        <?php if(isset($productGroupList) && $productGroupList){ ?>
        <?php foreach ($productGroupList as $key => $productGroup) { ?>
            <tr>
                <td><?php echo ($page-1)*$pageSize + $key + 1 ?></td>
                <td><?php echo $productGroup['product_group_name'] ?></td>
                <td><?php echo $productGroup['choose_product'] ?></td>
                <td>
                    <?php if(Yii::$app->user->can('编辑饮品组')){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-pencil" title="编辑"></span>',

                            Url::to(['light-belt-product-group/update', 'id' => $productGroup['id']])
                        );
                    }?>

                    <?php if(Yii::$app->user->can('饮品组使用详情') && $productGroup['isUse']){
                        echo Html::a(
                            '<span style="color:red;" class="glyphicon glyphicon-comment" id='.$productGroup['id'].' title="使用中"></span>',
                            Url::to(['light-belt-product-group/use-progroup-view', 'id' => $productGroup['id']])
                        );
                    }?>

                    <?php if(Yii::$app->user->can('删除饮品组') && !$productGroup['isUse']){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-trash del_group" id='.$productGroup['id'].' title="删除"></span>',
                            '#'
                        );
                    }?>

                </td>
            </tr>
        <?php  } }?>
    </table>
    <?php if(!isset($productGroupList) || !$productGroupList){ ?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php } ?>
    <?=
        LinkPager::widget([
          'pagination' => $pages,
        ]);
    ?>
</div>
<?php
$url = Url::to(["light-belt-product-group/delete"]);
$this->registerJs('
    $(".del_group").click(function(){
        if(!confirm("确认要删除？")){
            return false;
        }
        else
        {
            var proGroupId = $(this).attr("id");
            $.post("' . $url . '",{id: proGroupId},function(data){
                if(data == "false"){
                    $("#error").show();
                }else{
                    window.location.reload();
                }
            });
        }
    })

');

?>
