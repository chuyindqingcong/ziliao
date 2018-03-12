	var obj = new obje();
	var project = {};

	project.init=function(){
		var objs = new obje()
		//提交表单信息
		this.send();
		//勾选按钮
		this.reImg()
		//切换
		this.bantab()
		//设置最小高度
		this.setHeight()
		//复制功能
		this.copy()
		//上传头像
		this.upust()
		//修改密码
		this.upDataPas()
		//提交谷歌验证
		this.sendGoogle()
		//解绑谷歌验证
		this.ungoogle()
		//邀请好友
		this.invitation()
		//生成TXT下载
		this.createTxt()
		//密码切换
		this.lbPass()
		//生成qr
		this.google()
	}
	project.google = function () {
		$('#qrImg').html('')
					jQuery('#qrImg').qrcode({
					    render: "canvas", 
					    width: 200,
					    height: 200,
					    text: $('#mess').attr('data-qrcode')
					});
	}
	project.lbPass = function () {
		$('#pwd2').on('change',function () {
	   		$('#shou1').val( $('#pwd2').val())
		})
		$('#shImg1').on('click',function () {
		    if($(this).attr('data-status')==1){
		        $(this).attr('data-status',2)
		        $('#shou1').css('display','block');
		        $('#pwd2').css('display','none')
		        $(this).attr('src','/Public/img/wht1.png')
		    }else{
		        $(this).attr('data-status',1)
		        $('#shou1').css('display','none');
		        $('#pwd2').val($('#shou1').val())
		         $('#pwd2').css('display','block')
		           $(this).attr('src','/Public/img/wht2.png')
		    }
		})
		$('#repwd2').on('change',function () {
		    $('#shou2').val( $('#repwd2').val())
		})
		$('#shImg2').on('click',function () {
		     if($(this).attr('data-status')==1){
		        $(this).attr('data-status',2)
		        $('#shou2').css('display','block');
		        $('#repwd2').css('display','none')
		          $(this).attr('src','/Public/img/wht1.png')
		    }else{
	  $(this).attr('src','/Public/img/wht2.png')
		        $(this).attr('data-status',1)
		        $('#shou2').css('display','none');
		        $('#repwd2').val($('#shou2').val())
		         $('#repwd2').css('display','block')
		    }
		})
	}
	project.createTxt = function () {
		var content = $("#qrspan").html();
	    var blob = new Blob([content], { type: "text/plain;charset=utf-8" });
	    $('#qrspan').on('click',function(){
	    	 saveAs(blob, "google秘钥.txt");   
	    })
	}
	project.invitation = function(){
		$('#Invitation').on('click',function(){
			$.ajax({
				url:'/Home/user/invite',
				type:'post',
				data:{
					email:$("#getEmail").val()
				},
				success:function(req){
					if(req.status==1){
						alert(req.info)
					}else{
						alert(req.info)
					}
				}
			})
		})
	}
	project.ungoogle = function () {
		$('#unGool').on('click',function(){
			$.ajax({
				url:'/Home/user/unbind',
				type:'post',
				data:{
					code:$('#uncode').val()
				},
				success:function(req){
					if(req.status==1){
						alert(req.info);
						setTimeout(function(){
							window.location.href=window.location.href
						},2000)
					}else{
						alert(req.info)
					}
				}
			})
		})
	}
	project.sendGoogle = function() {
		$('#sendGool').on('click',function(){
			if($('#pwd1').val().length<=0 || $('#code1').val().length<=0){
				alert($('#mess').attr('data-Norequire'));
				return false
			}
			if($('.yy').attr('data-status')==0){
				alert($('#mess').attr('data-errch'))
				return false
			}
			$.ajax({
				url:'/Home/user/verifyGoogleAuth',
				type:'post',
				data:{
					pwd:$('#pwd1').val(),
					code:$('#code1').val(),
					type:1
				},
				success:function(req){
					if(req.status==1){
						alert(req.info)
						setTimeout(function(){
							window.location.href=window.location.href
						},2000)
					}else{
						alert(req.info)
					}
				}
			})
		})
	}

	project.upDataPas = function() {
		$('#upDataPas').on('click',function(){
			$.ajax({
				url:'/Home/user/updatePassword',
				type:'post',
				data:{
					oldpwd:$('#oldpwd2').val(),
					pwd:$('#pwd2').val(),
					repwd:$('#repwd2').val()
				},
				success:function(req){
					if(req.status==1){
						alert(req.info);
						setTimeout(function(){
							window.location.href='/Home/Index/login.html'
						},2000)
					}else{
						alert(req.info)
					}
				}
			})
		})
	}
	project.upust = function(){
		$('#usd1').on('click',function(){
	    	$('#file5').click();
	    
		})
		$('#usd2').on('click',function(){
	    	$('#file6').click();
		})
		$('#file5').on('change',function () {
				var file = document.getElementById('file5').files[0]
	    		if(file.size>1024*1024*2){
	    			alert($('#mess').attr('data-ppd'))
	    			return false
	    		}
			$('#form5').ajaxSubmit({
		        type:'post',
		        url:'/Home/user/upload',
		        data:{
		        	type:1
		        },
		        dataType:'json',
		        success:function (req) {
		            if(req.status ==1) {
		                $('#usd1').html($('#mess').attr('data-upOK'))
		              	$('#pic1').val(req.data)
		            }
		        }
		    })
		})
		$('#file6').on('change',function () {
			var file = document.getElementById('file6').files[0]
	    		if(file.size>1024*1024*2){
	    			alert($('#mess').attr('data-ppd'))
	    			return false
	    		}
		    $('#form6').ajaxSubmit({
		        type:'post',
		        url:'/Home/user/upload',
		        dataType:'json',
		        data:{
		        	type:2
		        },
		        success:function (req) {
		            if(req.status ==1) {
		                $('#usd2').html($('#mess').attr('data-upOK'))
		                $('#pic2').val(req.data)
		            }
		        }
		    })
		})
	}
	project.copy = function () {
		$('#copy').on('click',function(){
			 try{
		        var Url2=document.getElementById("aa");
		        Url2.select();
		        document.execCommand("Copy");
		        alert($('#mess').attr('data-copy'));
		    }catch (err){
		        alert($('#mess').attr('data-noCopy'));
		    }
		})
	}
	project.setHeight = function () {
		$('.Tabcon').css('min-height',$(window).height()-208+ "px")
	}
	project.bantab = function () {
		$('.userTab li').on('click',function(){
			$(this).addClass('userTabClass').siblings().removeClass('userTabClass');
			$('.Tabcon>div').eq($(this).index()).css('display','block').siblings().css('display','none')
		})
	}
	project.send = function () {
		$('#rsend').on('click',function(){
			if(($('#pic1').val().length>=0 && $('#pic2').val().length==0)||($('#pic1').val().length==0 && $('#pic2').val().length>=0)){
				alert($('#mess').attr('data-nosend'))
				return false
			}
			var datas = $('#formsrzi').serialize();
			$.ajax({
				url:"/Home/user/setMember",
				type:'post',
				data:datas,
				success:function(req){
					alert(req.info)
				}
			})
		})
	}
	project.reImg = function () {
		$('.yy').on('click',function() {
			if($(this).attr('data-status')==0){
				$(this).attr('data-status',1);
				$(this).attr('src','/Public/img/yy2.png');
				$('#check1').click()
			}else{
				$(this).attr('data-status',0);
				$(this).attr('src','/Public/img/yy1.png');
				$('#check1').click()
			}
		})
	}
	project.init()