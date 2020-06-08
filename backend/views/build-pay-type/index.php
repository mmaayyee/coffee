<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildPayTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile("/js/discount-building-assoc-index.js?v=1.7", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->title                   = '楼宇支付策略管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-pay-type-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=!Yii::$app->user->can('添加优惠楼宇') ? '' : Html::a('添加楼宇支付策略', ['/discount-building-assoc/create'], ['class' => 'btn btn-success'])?>
        <?=!Yii::$app->user->can('优惠楼宇查看') ? '' : Html::a('查看全部楼宇策略详情', ['/discount-building-assoc/index'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '楼宇支付策略名称',
            'value' => function ($model) {
                return $model->build_pay_type_name;
            },
        ],
        [
            'label'  => '支付方式数量',
            'format' => 'raw',
            'value'  => function ($model) {
                return Html::a($model->pay_type_number, '#',
                    [
                        'buildPayTypeId' => $model->build_pay_type_id,
                        'class'          => 'select-pay-type',
                        'title'          => '查看支付方式',
                    ]
                );
            },
        ],
        [
            'label'  => '楼宇数量',
            'format' => 'raw',
            'value'  => function ($model) {
                return Html::a($model->build_number, '#',
                    [
                        'buildPayTypeId' => $model->build_pay_type_id,
                        'class'          => 'select-building',
                        'title'          => '查看楼宇',
                    ]
                );
            },
        ],
        [
            'label' => '添加时间',
            'value' => function ($model) {
                return empty($model->create_time) ? '' : date('Y-m-d H:i:s', $model->create_time);
            },
        ],
        [
            'label' => '更新时间',
            'value' => function ($model) {
                return empty($model->update_time) ? '' : date('Y-m-d H:i:s', $model->update_time);
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {delete} {update}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return !Yii::$app->user->can('优惠楼宇查看') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/discount-building-assoc/index?DiscountBuildingAssocSearch[build_pay_type_name]=' . urlencode($model->build_pay_type_name));
                },
                'delete' => function ($url, $model) {
                    return !Yii::$app->user->can('优惠楼宇删除') ? '' : Html::a('', $url, ['onclick' => 'return confirm("确定删除吗？");', 'class' => 'glyphicon glyphicon-trash', 'title' => '删除']);
                },
                'update' => function ($url, $model) {
                    return !Yii::$app->user->can('优惠楼宇修改') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/discount-building-assoc/update?buildPayTypeId=' . $model->build_pay_type_id);
                },
            ],
        ],
    ],
]);?>
</div>

<div class="modal fade bs-example-modal-lg" id="payTypeModal"  aria-labelledby="myLargeModalLabel" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-body" id="payTypeContent">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="myModal"  aria-labelledby="myLargeModalLabel" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <form class="form-inline">
          <div class="form-group">
            <label for="exampleInputName2">楼宇名称:</label>
            <input type="text" name="buildName" class="form-control" id="exampleInputName2" >
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">楼宇类型:</label>
            <select name="buildType" class="form-control">
                <?php foreach ($buildTypeList as $key => $val): ?>
                    <option value=<?=$key?>>
                        <?=$val?>
                    </option>
                <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">设备类型:</label>
            <select name="equipType" class="form-control">
                <?php foreach ($equipTypeList as $key => $val): ?>
                    <option value=<?=$key?>>
                        <?=$val?>
                    </option>
                <?php endforeach;?>
            </select>
          </div>
          <button type="button" class="btn btn-default numberButton">查询</button>
        </form>
      </div>

      <div class="modal-body" id="buildingModal">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
