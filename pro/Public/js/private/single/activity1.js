var project = {}
var objs = new obje()
project.init = function () {
	//获取交易排行
	this.getPh()
}
project.getPh = function() {
	$.ajax({
		url:'/Home/single/volumeSort',
		type:'get',
		success:function(res){
			var txt = '';
			if(res.data==undefined){
				txt ='<ul class="no-data">'+
						'<li>'+$('#mess').attr('data-nodata')+'</li>'+
					'</ul>'
			}else{
				for(var i=0;i<res.data.length;i++){
					txt += '<ul class="ph-name">'+
						 '<li>'+(i+1)+'</li>'+
						 '<li>'+res.data[i].nick+'</li> '+
						 '<li>'+res.data[i].money+' BTC</li>'+
					 '</ul>'
				}
			}
			$('.svn').append(txt)
		}
	})
}
project.init()
