<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use backend\models\LightBeltScenario;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LightBeltScenarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '灯带场景管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-scenario-index">

    <?php echo $this->render('_search', ['model' => $model]); ?>

    <div id="error" style="color: red; display: none; margin-bottom: 6px;">删除失败，请检测是否使用</div>

    <?php if(Yii::$app->user->can('添加灯带场景')){ ?>
    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    <table class="table table-bordered">
        <tr>
            <td>序号</td>
            <td>场景名称</td>
            <td>设备场景</td>
            <td>饮品组</td>
            <td>策略</td>
            <td>开始时间</td>
            <td>结束时间</td>
            <td>操作</td>
        </tr>
        <?php if(isset($scenarioList) && $scenarioList){ ?>
        <?php foreach ($scenarioList as $key => $scenario) {  ?>
            <tr>
                <td><?php echo ($page-1)*$pageSize + $key + 1 ?></td>
                <td><?php echo $scenario['scenario_name'] ?></td>
                <td><?php echo $scenario['equip_scenario_name'] ?></td>
                <td><?php echo $scenario['product_group_name'] ?></td>
                <td><?php echo $scenario['strategy_name'] ?></td>
                <td><?php echo $scenario['start_time'] ?></td>
                <td><?php echo $scenario['end_time'] ?></td>
                <td>
                    <?php  if(Yii::$app->user->can("查看灯带场景")){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-eye-open" title="查看"></span>',
                            Url::to(['light-belt-scenario/view', 'id' => $scenario['id']])
                        );
                    } ?>
                    <?php if(Yii::$app->user->can('编辑灯带场景')){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-pencil" title="编辑"></span>',

                            Url::to(['light-belt-scenario/update', 'id' => $scenario['id']])
                        );
                    }?>

                    <?php if($scenario['isUse']){
                        echo Html::a(
                            '<span style="color:red;" class="glyphicon glyphicon-comment" id='.$scenario['id'].' title="使用中"></span>',
                            Url::to(['light-belt-scenario/use-scenario-view', 'id' => $scenario['id']])
                        );
                    }?>

                    <?php if(Yii::$app->user->can('删除灯带场景') && !$scenario['isUse']){
                        echo Html::a(
                            '<span class="glyphicon glyphicon-trash del_scenario" id='.$scenario["id"].' title="删除"></span>',
                            "#"
                        );
                    }?>
                </td>
            </tr>
        <?php }} ?>
    </table>
    <?php if(!isset($scenarioList) || !$scenarioList){ ?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php } ?>
    <?=
        LinkPager::widget([
          'pagination' => $pages,
        ]);
    ?>

</div>
<?php
$url = Url::to(["light-belt-scenario/delete"]);
$this->registerJs('
    $(".del_scenario").click(function(){
        if(!confirm("确认要删除？")){
            return false;
        }
        else
        {
            var scenarioId = $(this).attr("id");
            $.post("' . $url . '",{id: scenarioId},function(data){
                console.log(data);
                if(data == "false"){
                    $("#error").show();
                }
                else{
                    window.location.reload();
                }
            });
        }
    })

');

?>
