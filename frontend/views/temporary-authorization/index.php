<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 17:24
 */
use yii\helpers\Html;
use backend\models\TemporaryAuthorization;

$this->title = '申请临时开门记录';
?>
<style type="text/css">
    .line-text{
        display: inline-block;
    }
    .confirm{
        margin-top: 5px;
    }
    label{
        width:40%;
    }
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
<?php if (isset($packetArr)){ ?>
    <div style="margin: 20% 0;text-align: center;">
        <div class="glyphicon glyphicon-ok-sign " style="color:#57bb59; font-size:10rem;;margin-bottom: 8%;"></div>
        <p style="font-size: 1.4rem">确认成功，请退出此页！</p>
    </div>
<?php }else if (empty($data)){ ?>
    <div style="margin: 20% 0;text-align: center;">
        <div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
        <p style="font-size: 1.4rem">暂无数据</p>
    </div>
<?php } else { ?>
        <?php if ($data['state'] == 0){ ?>
            <div class="border">
            <div>
                <label >楼宇名称：</label>
                <div class="value line-text"><?php echo $data['build_name']; ?></div>
            </div>
            <div>
                <label >申请人姓名：</label>
                <div class="value line-text"><?php echo $data['wx_member_name']; ?></div>
            </div>
            <div>
                <label >申请时间：</label>
                <div class="value line-text"><?php echo date('Y-m-d H:i:s',$data['application_time']); ?></div>
            </div>
            <div class='confirm'>
                <?php
                    echo Html::a('已授权', 'index?state=1&id='. $data['id'] , ['class' => 'btn btn-block btn-primary']) . ' ';
                    echo Html::a('已拒绝', 'index?state=2&id='. $data['id'] , ['class' => 'btn btn-block btn-primary']) . ' ';
                ?>
            </div>
        </div>
        <?php } else { ?>
        <div style="margin: 20% 0;text-align: center;">
            <div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
            <p style="font-size: 1.4rem">
                <?php
                echo $data['state'] == 1 ? '已授权' : ($data['state'] == 2 ? '已拒绝' : '已失效');
                ?>
            </p>
        </div>
        <?php } ?>
<?php } ?>
