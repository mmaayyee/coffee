<?php

use backend\models\DistributionUser;
use common\models\Building;
use yii\grid\GridView;
use yii\helpers\Html;
use kartik\select2\Select2;
use backend\models\Manager;
use backend\models\Organization;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '人员管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/management/css/app.5667cd9a135208915873f57aed3131a5.css');
$this->registerJSFile('/management/js/manifest.7070c2ca46b43318b12a.js');
$this->registerJSFile('/management/js/vendor.e1671ebbaeb5ce27bbdc.js');
$this->registerJSFile('/management/js/app.7a1e572d30288e088fde.js');
?>
<?php
    $orgId = Manager::getManagerBranchID();
    if($orgId == 1){
        $companyList = Organization::getBranchArray(2);
        $companyArray = [];
        foreach($companyList as $orgId => $company){
            if($orgId != 1){
                $companyArray[$orgId] = $company;
            }
        }
?>
    <form action="/distribution-user/management" method="get">
        <div class="form-inline">
            <div class="form-group">
            <label>运维员</label>
            <div class="select2-search">
                <?php
                echo Select2::widget([
                    'name' => 'org_id',
                    'value' => $org_id,
                    'data' => $companyArray,
                    'options' => ['multiple' => false, 'placeholder' => '请选择分公司']
                ]);
                ?>
            </div>
        </div>
            <div class="form-group">
                <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </form>
<?php }?>
<script>
	var rootErpUrl = '';
    var rootInitData = <?php echo $model; ?>;
    var rootOrgId = '<?php echo $org_id; ?>';
</script>
<div id=app></div>