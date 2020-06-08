<?php

use backend\models\DiscountHolicy;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '楼宇支付策略列表';
$this->params['breadcrumbs'][] = ['label' => '楼宇支付策略', 'url' => ['/build-pay-type/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile("/js/laypage.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/discount-building-assoc-index.js?v=1.7", ["depends" => [\yii\web\JqueryAsset::className()]]);
$disModel = new DiscountHolicy();
?>
<div class="discount-building-assoc-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'payTypeList' => $payTypeList, 'buildPayTypeNameList' => $buildPayTypeNameList]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '楼宇支付策略名称',
            'value' => function ($model) {return $model->build_pay_type_name;},
        ],
        [
            'label' => '支付方式名称',
            'value' => function ($model) use ($payTypeList) {return empty($payTypeList[$model->holicy_payment]) ? '' : $payTypeList[$model->holicy_payment];},
        ],
        [
            'label' => '支付方式序号',
            'value' => function ($model) {return $model->weight;},
        ],
        [
            'label' => '策略名称',
            'value' => function ($model) {return $model->holicy_name;},
        ],
        [
            'label'  => '楼宇数量',
            'format' => 'raw',
            'value'  => function ($model) use ($searchModel) {
                return Html::a($model->buildingNumber, '#',
                    [
                        'buildPayTypeId' => $model->build_pay_type_id,
                        'buildName'      => $searchModel->build_name,
                        'class'          => 'select-building',
                        'title'          => '查看楼宇',
                    ]
                );
            },
        ],
        [
            'label'  => '优惠策略',
            'format' => 'raw',
            'value'  => function ($model) use ($disModel) {
                return Html::a($disModel->getHolicyTypeName($model->holicy_type), '#',
                    [
                        'holicyID' => $model->holicy_id,
                        'class'    => 'select-discount-details',
                        'title'    => '查看楼宇',
                    ]
                );
            },
        ],
    ],
]);?>

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

<div class="modal fade" id='select-discount-details'  tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="disDetails">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>