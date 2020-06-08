<?php
$this->title = '剩余物料管理';
?>
<style type="text/css">
    .border{
        border:1px solid #ccc;
        padding:2% 3% 5% ;
        margin-bottom:10% ;
        border-radius: 10px
    }
    h5{
        text-align: center;
        font-size:16px ;
        font-weight: bold;
    }
</style>
<div id="app"></div>
<div>
    <div class="border">
        <div><h5>物料</h5></div>
        <?php echo backend\models\ScmUserSurplusMaterial::getMaterialByAuthor($author); ?>
        <div><h5>散料</h5></div>
        <?php echo backend\models\ScmUserSurplusMaterialGram::getMaterialGramByAuthor($author); ?>
    </div>
</div>
