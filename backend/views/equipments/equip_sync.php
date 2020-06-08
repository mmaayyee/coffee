<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EquipmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '设备数据统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipments-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_equip_sync_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('返回上一页', '/equipments/index', ['class' => 'btn btn-success']) ?>
    </p>
    <table class="table table-bordered">
      <thead>
         <tr>
            <th>公司名称</th>
            <th>运营状态</th>
            <th>该状态总台数</th>
            <th>设备类型</th>
            <th>设备台数</th>
         </tr>
      </thead>
      <tbody>
         <?php if ($list) { foreach ($list as $key => $value): $i=0;?>
            <?php if ($value['data']) { foreach ($value['data'] as $ke=>$val): $i++;?>
               <?php if ($val['data']) { $j=0; foreach ($val['data'] as $k => $v): $j++;?>
                  <tr>
                     <?php if($j == 1) { if ($i == 1) {?>
                     <td rowspan="<?php echo $value['rows']; ?>"><?php echo $key; ?></td>
                     <?php } ?>
                     <td rowspan="<?php echo count($val['data']); ?>"><?php echo $ke; ?></td>
                     <td rowspan="<?php echo count($val['data']); ?>"><?php echo $val['num']." 台"; ?></td>
                     <?php } ?>
                     <td><?php echo $k; ?></td>
                     <td><?php echo $v." 台"; ?></td>
                  </tr>
               <?php endforeach; } ?>
            <?php endforeach; } ?>
         <?php endforeach; }?>
      </tbody>
   </table>
</div>