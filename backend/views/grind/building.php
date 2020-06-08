<div class="block-a">
    <div class="searchResult">
        <h5>搜索结果</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>楼宇名称</th>
                    <th>
                        <button class="btn btn-primary" id="batchAdd" type="button" onclick="batchAddBuilding(this);">批量添加</button>
                    </th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pages"></div>
        <button class="btn btn-info" id="addAll" type="button" onclick="addAllBuilding(this);">全部添加</button>
    </div>
    <div class="text-center no-data">暂无数据</div>
    <div class="addPreview">
        <h5>添加楼宇预览</h5>
        <div class="overflow">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>楼宇名称</th>
                        <th><button type="button" class="btn btn-primary allDelete" disabled="disabled" onclick="allDelete(this)">批量移除</button></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!--楼宇模板-->
<script id="building_add_template" type="text/html">
    {{# $.each(d,function(key,value){ }}
        <tr>
            <td class="SortId" data-text=""></td>
            <td>{{value.name}}</td>
            <td>
                <input type="hidden" value="{{value.id}}" disabled="disabled" />
                <button type="button" class="btn btn-primary btn-sm add" onclick="addBuilding(this)">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </td>
        </tr>
    {{# })}}
</script>
<script id="building_del_template" type="text/html">
    {{# $.each(d,function(key,value){ }}
        <tr>
            <td class="SortId" data-text=""></td>
            <td>{{value.name}}</td>
            <td>
                <input type="hidden"  name="buildingIdArr[]" value="{{value.id}}"/>
                <button type="button" class="btn btn-primary btn-sm add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </td>
        </tr>
    {{# })}}
</script>
