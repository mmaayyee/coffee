<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LightBeltStrategySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '灯带策略管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-strategy-index">

    <?php echo $this->render('_search', ['model' => $model]); ?>
    <div id="error" style="color: red; display: none; margin-bottom: 6px;">删除失败，请检测是否使用</div>
    <?php if(Yii::$app->user->can('添加灯带策略')){ ?>
    <p>
        <?= Html::a('添加策略', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    <table class="table table-bordered">
        <tr>
            <td>序号</td>
            <td>
                灯带策略名称
            </td>
            <td>
                灯带总周期(/ms)
            </td>
            <td>操作</td>
        </tr>
        <?php if(isset($strategyList) && $strategyList){ ?>
        <?php foreach ($strategyList as $key => $strategy) { ?>
           <tr>
                <td><?php echo ($page-1)*$pageSize + $key + 1 ?></td>
                <td><?php echo $strategy['strategy_name']; ?></td>
                <td><?php echo $strategy["total_length_time"]; ?></td>
                <td>
                    <?php  if(Yii::$app->user->can("查看灯带策略")){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-eye-open" title="查看"></span>',

                            Url::to(['light-belt-strategy/view', 'id' => $strategy['id']])
                        );
                    } ?>
                    <?php if(Yii::$app->user->can('编辑灯带策略')){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-pencil" id="'."1".'" build_id="'."1".'" equip_code="'."1".'" product_id="'."1".'" product_name="'."1".'" title="编辑"></span>',

                            Url::to(['light-belt-strategy/update', 'id' => $strategy['id']])
                        );
                    }?>
                    <?php if($strategy['isUse']){
                        echo Html::a(
                            '<span style="color:red;" class="glyphicon glyphicon-comment" id='.$strategy['id'].' title="使用中"></span>',
                            Url::to(['light-belt-strategy/use-strategy-view', 'id' => $strategy['id']])
                        );
                    }?>
                    <?php if(Yii::$app->user->can('删除灯带策略') && !$strategy['isUse']){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-trash del_strategy" id='.$strategy['id'].' title="删除"></span>',
                            '#'
                        );
                    }?>

                </td>
           </tr>
        <?php } }?>
    </table>
    <?php if(!isset($strategyList) || !$strategyList){ ?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php } ?>
    <?=
        LinkPager::widget([
          'pagination' => $pages,
        ]);
    ?>
</div>

<?php
$url = Url::to(["light-belt-strategy/delete"]);
$this->registerJs('
    $(".del_strategy").click(function(){
        if(!confirm("确认要删除？")){
            return false;
        }
        else
        {
            var strategyId = $(this).attr("id");
            $.post("' . $url . '",{id: strategyId},function(data){
                if(data=="false"){
                    $("#error").show();
                }else{
                    window.location.reload();
                }
            });
        }
    })

');

?>
