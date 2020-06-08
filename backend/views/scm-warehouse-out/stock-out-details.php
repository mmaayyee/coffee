<?php

use backend\models\ScmStock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<link rel="stylesheet" href="/css/stock-out-details.css">
<div class="estimate-single">
    <div class="block-a">
        <div class="title">
            <p>出库单详情</p>
            <p>任务日期：2018.3.18</p>
        </div>
        <table class="table table-bordered">
            <tr>
                <td>物料种类</td>
                <td>名称</td>
                <td>数量</td>
            </tr>
            <tr>
                <td>咖啡豆</td>
                <td>泰谷-100g/包</td>
                <td>10 包</td>
            </tr>
            <tr>
                <td>香草</td>
                <td>雀巢--200g/包</td>
                <td>雀巢--200g/包</td>
            </tr>
        </table>
    </div>
    <div class="block-a">
        <h4>预估单详情</h4>
        <table class="table table-bordered">
            <tr>
                <td>物料种类</td>
                <td>名称</td>
                <td>数量</td>
            </tr>
            <tr>
                <td>咖啡豆</td>
                <td>泰谷-100g/包</td>
                <td>10 包</td>
            </tr>
            <tr>
                <td>香草</td>
                <td>雀巢--200g/包</td>
                <td>雀巢--200g/包</td>
            </tr>
        </table>
    </div>
    <div class="block-a">
        <h4>差异在</h4>
        <table class="table table-bordered">
            <tr>
                <td>物料种类</td>
                <td>名称</td>
                <td>数量</td>
            </tr>
            <tr>
                <td>咖啡豆</td>
                <td>泰谷-100g/包</td>
                <td>10 包</td>
            </tr>
            <tr>
                <td>香草</td>
                <td>雀巢--200g/包</td>
                <td>雀巢--200g/包</td>
            </tr>
        </table>
    </div>
    <div class="block-a">
        <h4>运维专员领料详情</h4>
        <table class="table table-bordered">
            <tr>
                <td>物料种类</td>
                <td>名称</td>
                <td>数量</td>
            </tr>
            <tr>
                <td>咖啡豆</td>
                <td>泰谷-100g/包</td>
                <td>10 包</td>
            </tr>
            <tr>
                <td>香草</td>
                <td>雀巢--200g/包</td>
                <td>雀巢--200g/包</td>
            </tr>
        </table>
    </div>
    <div class="block-b">
         <button type="button" class="btn btn-default">取消</button><button type="submit" class="btn btn-primary">确认</button>
    </div>
</div>
<script type="text/javascript" src="/js/third-party/jquery-1.10.2.min.js"></script>