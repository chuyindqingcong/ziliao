var project = {};
project.init=function(){
	var objs = new obje()
	//登录
	this.login()
	//回车登录
	this.enter()
}
project.enter = function () {
	$('#pwd').on('keydown',function(e){
		if(e.which==13){
			var datas = $('#signupForm').serialize();
			$.ajax({
				type:'post',
				url:'/Home/Login/checkLog',
				dataType:'json',
				data:datas,
				success:function(req){
					if(req.login_type==1){
				   		window.location.href = "/"
					   }else if (req.login_type==2){
					   		window.location.href = '/Home/single/google.html'
					   }else {
							alert(req.info)
					}
				}
			})
		}
	})
}
project.login = function (){
	$('#login').on('click',function(){
		var datas = $('#signupForm').serialize();
		$.ajax({
			type:'post',
			url:'/Home/Login/checkLog',
			dataType:'json',
			data:datas,
			success:function(req){
				if(req.login_type==1){
				   		window.location.href = "/"
				   }else if (req.login_type==2){
				   		window.location.href = '/Home/single/google.html'
				   }else {
						alert(req.info)
				}
			}
		})
	})
}
project.init();
