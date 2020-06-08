$(function(){
	$('.getBuildingInfo').click(function(){
		var id = $(this).attr('buildId')
		$('#exampleModal').modal('show')
		$.get(
            '/materiel-consum/get-building-info',
            {'id':id},
            function(data){
                if(data){
                	$('#buildName').html(data.buildName)
                	$('#time').html("上传时间:"+data.time)
                	str=""
                	$.each(data.list,function(k,v){
						switch (k) {
							case 'water':
								str+='<div><span>水:</span>'+'<span>'+v+'</span>'+'</div>'
								break;
							case 'coins':
								if(v.materielName) {
									str += '<div><span>' + v.materielName + ':</span>' + '<span>' + v.count + '个</span>' + '</div>'
								}
								break;
							case 'cups':
								if(v.materielName) {
									str += '<div><span>' + v.materielName + ':</span>' + '<span>' + v.count + '个</span>' + '</div>'
								}
								break;
							default:
								if(v.materielName){
									str+='<div><span>'+v.materielName+':</span>'+'<span>'+v.count+'g</span>'+'</div>'
								}
								break;
						}
                	})
                	$('#materielTypeName').html(str)
                }
            },
            'json'
        )
	})
})