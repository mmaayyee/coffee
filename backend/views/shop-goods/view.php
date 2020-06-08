<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/18
 * Time: 下午2:11
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = $goodsDetail['goods_name'];
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-view">

    <h1><?=Html::encode($this->title);?></h1>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'goods_id',
            'value'     => $goodsDetail['goods_id'],
        ],
        [
            'attribute' => 'goods_name',
            'value'     => $goodsDetail['goods_name'],
        ],
        [
            'attribute' => 'image',
            'format'    => 'html',
            'value'     => $model->getImage($goodsDetail['image']),
        ],
        [
            'attribute' => 'specification',
            'value'     => $model->specification,
        ],
        [
            'attribute' => 'suttle',
            'value'     => $model->suttle,
        ],
        [
            'attribute' => 'expiration',
            'value'     => $model->expiration,
        ],
        [
            'attribute' => 'producter',
            'value'     => $model->producter,
        ],
        [
            'attribute' => 'status',
            'value'     => $model->getStatus($goodsDetail['status']),
        ],
        [
            'attribute' => 'check_fail_reason',
            'value'     => $model->check_fail_reason ? $model->check_fail_reason : '',
        ],
        [
            'attribute' => 'create_time',
            'value'     => $goodsDetail['create_time'] > 0 ? date('Y-m-d H:i:s', $goodsDetail['create_time']) : '',
        ],
        [
            'attribute' => 'content',
            'format'    => 'html',
            'value'     => $goodsDetail['content'],
        ],
    ],
]);?>
  <h2>商品属性详情</h2>
    <div class="bs-example" data-example-id="bordered-table">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>商品价格</th>
                <th><?php echo $data[0]['attribute']; ?></th>
                <?php if (isset($data[0]['attribute1'])): ?>
                <th><?php echo $data[0]['attribute1']; ?></th>
                <?php else: ?>
                    <?php endif;?>
                <th>商品库存</th>
            </tr>
            </thead>
    <tbody>
                <?php foreach ($data as $key => $val): ?>
                    <tr>
                        <th scope="row"><?php echo $val['price']; ?></th>
                        <?php if ($val['col1']): ?>
                            <td><?php echo $val['col1']; ?></td>
                        <?php else: ?>
                        <?php endif;?>

                          <?php if (isset($val['col2'])): ?>
                        <td><?php echo $val['col2']; ?></td>
                         <?php else: ?>
                         <?php endif;?>

                        <td><?php echo $val['stock']; ?></td>
                    </tr>
                <?php endforeach;?>
         </tbody>
        </table>
    </div>
</div>
