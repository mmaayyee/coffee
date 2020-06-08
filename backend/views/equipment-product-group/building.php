<p>
    <a class="btn btn-success" href="/equipment-product-group/index">返回上一页</a>
</p>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>楼宇Id</th>
            <th>楼宇名称</th>
            <th>设备类型</th>
            <th>设备编号</th>
            <th>机构名称</th>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($building as $buildingInfo): ?>
                <tr data-key="">
                <td><?=$buildingInfo['build_id']?></td>
                <td><?=$buildingInfo['name']?></td>
                <td><?=$buildingInfo['equipment_name']?></td>
                <td><?=$buildingInfo['equipment_code']?></td>
                <td><?=$buildingInfo['org_name']?></td>
            </tr>
            <?php endforeach;?>

        </tbody>
</table>