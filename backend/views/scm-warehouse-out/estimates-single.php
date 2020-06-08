<?php

use backend\models\ScmStock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="estimate-single">
    <div class="block-a">
        <p><span>运维任务日期:</span>2018.3.18</p>
        <p><span>制作人：</span>王永才</p>
    </div>
    <div class="block-b">
        <form action="">
            <table class="table table-bordered">
                <tr>
                    <td>物料总和</td>
                    <td> 
                        <p><span>泡沫奶-润泽祥和贸易-20克/包 </span><span class="txt-style" id="text1">15包</span></p>
                        <p><span>咖啡豆-王天乐城商贸-20克/包 </span><span class="txt-style" id="text2">15包</span></p>
                    </td>
                </tr>
                <tr>
                    <td>姓名1 <input type="hidden" name="" value=""/></td>
                    <td>
                        <p class="form-group"><span>泡沫奶-润泽祥和贸易-20克/包 </span><input type="text" name="text1" value="3" check-type="nonnegativeInteger" maxlength="3" /><span class="help-block" id="valierr"></span></p>
                        <p class="form-group"><span>咖啡豆-王天乐城商贸-20克/包</span><input type="text" name="text2" value="3" check-type="nonnegativeInteger" maxlength="3"/><span class="help-block" id="valierr"></span></p>
                    </td>
                </tr>
                <tr>
                    <td>姓名2 <input type="hidden" name="" value=""/></td>
                    <td>
                        <p class="form-group"><span>泡沫奶-润泽祥和贸易-20克/包 </span><input type="text" name="text1" value="3" check-type="nonnegativeInteger" maxlength="3"/><span class="help-block" id="valierr"></span></p>
                        <p class="form-group"><span>咖啡豆-王天乐城商贸-20克/包</span><input type="text" name="text2" value="3" check-type="nonnegativeInteger" maxlength="3"/><span class="help-block" id="valierr"></span></p>
                    </td>
                </tr>
                <tr>
                    <td>姓名3 <input type="hidden" name="" value=""/></td>
                    <td>
                        <p class="form-group"><span>泡沫奶-润泽祥和贸易-20克/包 </span><input type="text" name="text1" value="3" check-type="nonnegativeInteger" maxlength="3"/><span class="help-block" id="valierr"></span></p>
                        <p class="form-group"><span>咖啡豆-王天乐城商贸-20克/包</span><input type="text" name="text2" value="3" check-type="nonnegativeInteger" maxlength="3"/><span class="help-block" id="valierr"></span></p>
                    </td>
                </tr>
            </table>
            <button type="button" class="btn btn-default">返回</button><button type="submit" class="btn btn-primary">修改</button>
        </form>
    </div>
</div>
<link rel="stylesheet" href="/css/estimates-single.css">
<script type="text/javascript" src="/js/third-party/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/js/bootstrap3-validation.js"></script>
<script type="text/javascript" src="/js/regular_verification.js"></script>
<script type="text/javascript" src="/js/estimates-single.js"></script>