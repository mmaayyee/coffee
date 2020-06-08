<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '城市优惠策略';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-type-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['cityName' => $cityName]); ?>
    <?php if (Yii::$app->user->can('添加城市优惠策略')) {?>
        <p>
            <?=Html::a('添加城市优惠策略', ['create'], ['class' => 'btn btn-success'])?>
        </p>
    <?php }?>
    <div id="w1" class="grid-view">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>城市名称</th>
                    <th>优惠策略名称</th>
                    <th class="action-column">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($strategyList as $key => $strategy): ?>
                <tr data-key="<?php echo $key + 1; ?>">
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $strategy['city_name'] ?></td>
                    <td><?php echo $strategy['coupon_group_name'] ? $strategy['coupon_group_name'] : '无' ?></td>
                    <td>
                        <?php if (Yii::$app->user->can('编辑城市优惠策略')): ?>

                        <a href="/index.php/city-preferential-strategy/update?id=<?php echo $strategy['id'] ?>" title="更新" aria-label="更新">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                        <?php endif?>
                        <?php if (Yii::$app->user->can('删除城市优惠策略')): ?>
                        <a onclick="return confirm('确定要删除吗？');" href="/index.php/city-preferential-strategy/delete?id=<?php echo $strategy['id'] ?>" title="删除" aria-label="删除">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                        <?php endif?>
                    </td>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
    </div>
</div>
