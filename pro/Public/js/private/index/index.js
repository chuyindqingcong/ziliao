var project = {};
var objs = new obje()

project.init=function(){
	objs.yesNum()
//	初始化滚动条
	$("#abut").nanoScroller();
	$("#abut4").nanoScroller();
	//初始化页数
	this.ids = $('#dhi').attr('data-id') //房间号
	this.coinCount=1 //币的当前记录
	this.record=1 	 //成交记录
	this.userCord=1  //用户成交记录
	this.m = 1 //买单当前记录
	this.mm=1 //卖单当前记录
	this.coin = $('#mess').attr('data-cname'); //币种名称data-cname
	this.id = $('#mess').attr('data-cid')	//币种ID
	this.ago = 'BTC'	//类型
	this.CordLengh = 0; //成交记录条数
	this.uCordLength= 0; //个人成交记录条数
	this.mLength = 0; //买单条数
	this.mmLength= 0 ;//卖单条数
	project.socket=null;
//  设置切换效果
	this.adNotClick();
//	单击搜索发生改变
	this.sousuo();
	//单击切换效果2 <600状态
	this.mmClick()
	//版块可以进行伸缩
	this.upHeight()
	//获取币种信息
	this.getCoins()
	//切换币种
	this.selectCoins()
	//初始化页面数据
	this.initData()
	//滚动事件
	this.scrooBot()
	//轮训更新
	this.setInter()
	//买入和fee
	this.buy()
	//卖出和fee
	this.sell()
	//单击填充数据
	this.copyli()
	//删除订单
	this.delOrder()
	//Copy 充值地址
	this.copys()	
	//获取充值地址
	this.getCoinAdd()
	//发送地址下拉sel
	this.selectLi()
	//切换充值地址
	this.qhSendAdd()
	//获取公告
	this.getAD()
	//取消二次确认框
	this.hide()
	//放大聊天
	this.userMes()
	//切换聊天
	this.lang()
	//提现币种
	this.sendCo()
	//图表
	// this.highch()
	//余额填入
	this.moy()
	//图表2
	this.charst()
	//登录用户获取数据
	if($('#hidden').val()==1){
		//我的订单
		this.meOrders()
		//我的成交记录
		this.meCount()
		//聊天
		this.qq()
		//获取余额
		this.getmon()
	}else{
		//未登录聊天
		this.qqto()
	}
}
project.charst = function () {
		TradingView.onready(function()
			{
				var widget = window.tvWidget = new TradingView.widget({
					height:'334px',
					width:'100%',
					symbol: project.id,
					interval: "5",
					container_id: "charts",
					//	BEWARE: no trailing slash is expected in feed URL
					datafeed: new Datafeeds.UDFCompatibleDatafeed("https://www.openiex.com/Home/orders"),
					library_path: "/Public/js/lib/charting_library/",
					locale:localStorage.getItem('lang')?localStorage.getItem('lang'):'en',
					//	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
					drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
					disabled_features: ["use_localstorage_for_settings",'timeframes_toolbar',],
					enabled_features: ["keep_left_toolbar_visible_on_small_screens"],
					timezone:'Asia/Shanghai',
					overrides:{
						'scalesProperties.lineColor' : "#e8f0f4",		
						"paneProperties.vertGridProperties.color": "#f2f7f9",
	                    "paneProperties.horzGridProperties.color": "#f2f7f9",
	                    'mainSeriesProperties.candleStyle.upColor': "#00a63f",		
	                    'mainSeriesProperties.candleStyle.borderColor': "#378658",
						'mainSeriesProperties.candleStyle.borderUpColor': "#225437",
						'mainSeriesProperties.candleStyle.borderDownColor': "#5b1a13",
	                  	
					},
					favorites: {
						intervals: ["1","5","30","60","1D"],
						chartTypes: ["Area", "Line"]
					}
				
				
				});
				var widget1 = window.tvWidget = new TradingView.widget({
					height:'334px',
					width:'100%',
					symbol: project.id,
					container_id: "charts1",
					interval: "5",
					//	BEWARE: no trailing slash is expected in feed URL
					datafeed: new Datafeeds.UDFCompatibleDatafeed("https://www.openiex.com/Home/orders"),
					library_path: "/Public/js/lib/charting_library/",
					locale:localStorage.getItem('lang')?localStorage.getItem('lang'):'en',
					//	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
					drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
					disabled_features: ["use_localstorage_for_settings",'timeframes_toolbar'],
					enabled_features: ["keep_left_toolbar_visible_on_small_screens"],
					timezone:'Asia/Shanghai',
					overrides: {
							"paneProperties.background": "#14384b",
	                        "paneProperties.vertGridProperties.color": "#264a60",
	                        "paneProperties.horzGridProperties.color": "#264a60",
							"symbolWatermarkProperties.transparency": 90,
							"scalesProperties.textColor" : "#A1B5C1",
							'scalesProperties.lineColor':'#507691',
							
						},	
					favorites: {
						intervals: ["1","5","30",'60',"1D"],
						chartTypes: ["Area", "Line"]
					}
		
				});
				widget1.onChartReady(function(){
						var ifrm = $('#charts1>iframe').attr('id');
					 	$("#"+ifrm).contents().find(".header-chart-panel").css('background','#14384b');
	                    $("#"+ifrm).contents().find(".layout__area--center").css('background','#14384b');
	                    $("#"+ifrm).contents().find(".apply-common-tooltip").css('background','#14384b');
	                    $("#"+ifrm).contents().find(".tv-side-toolbar").css('background','#14384b');
	                    $("#"+ifrm).contents().find(".main").css('background','#14384b');
	                    $("#"+ifrm).contents().find(".apply-common-tooltip").css('color','#fff');
	                    $('#'+ifrm).contents().find(".selected").css('background','#2c3b59 !important');
					
				})
				

			});
	if(localStorage.getItem('ofs')==undefined){
		$('#charts').css('display','block')
		$('#charts1').css('display','none')
	}else{
		if(localStorage.getItem('ofs')=='1'){
			$('#charts1').css('display','block')
			$('#charts').css('display','none')
		}else{
			$('#charts').css('display','block')
			$('#charts1').css('display','none')
		}
	}

	// TradingView.onready(function()
	// 		{
	// 			var widget =window.tvWidget new TradingView.widget({
	// 				height:'267px',
	// 				width:'100%',
	// 				symbol: project.id,
	// 				interval: [1,5,15,30],
	// 				container_id: "tv_chart_container",
	// 				//	BEWARE: no trailing slash is expected in feed URL
	// 				datafeed: new Datafeeds.UDFCompatibleDatafeed("https://demo_feed.tradingview.com"),
	// 				library_path: "/Public/js/lib/charting_library/",
	// 				locale: getParameterByName('lang') || "cn",
	// 				//	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
	// 				drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
	// 				disabled_features: ["use_localstorage_for_settings"],
	// 				enabled_features: ["study_templates"],

	// 				// interval: [1,5,15,30],
	// 				// container_id: "charts",
	// 				// //	BEWARE: no trailing slash is expected in feed URL
	// 				// datafeed: new Datafeeds.UDFCompatibleDatafeed("http://192.168.1.20/Home/orders"),
	// 				// library_path: "/Public/js/lib/charting_library/",
	// 				// locale: "en",
	// 				// //	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
	// 				// drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
	// 				// disabled_features: ["use_localstorage_for_settings"],
	// 				// toolbar_bg: "#f1f3f6",
	// 				// timezone:'Asia/Shanghai',
			
	// 			});
	// 		})
}


project.getmon = function (res) {
		$.ajax({
				url:'/Home/trade/getBalance',
				type:'get',
				data:{
					currency_id:project.id
				},
				success:function(req){
					$('.ozlo').eq(0).html(req.user_btc+' BTC');
					$('.ozlo').eq(1).html(req.user_money+' '+project.coin)
					$('.ozlo').eq(2).html($('#mess').attr('data-ye')+'<b class="bclicks">'+req.user_btc+'</b> BTC')
					$('.ozlo').eq(3).html($('#mess').attr('data-ye')+'<b class="bclicks">'+req.user_money+'</b> '+project.coin)
					$('.ozlo2').eq(0).html(req.user_btc);
					$('.ozlo2').eq(1).html(req.user_money)
					$('.ozlo3').eq(0).html(req.user_btc);
					$('.ozlo3').eq(1).html(req.user_money)
				}
			})	
}
project.moy = function () {
	$('.shouczs1 .ozlo').on('click',function(){
		$('#buy1num').val(parseFloat($(this).html()));
		if(parseFloat($('#buy1pri').val())>0){
			$('#buy1sum').val(objs.fds($('#buy1pri').val(),$("#buy1num").val()))
		}
	})
	$('.shouczs2 .ozlo').on('click',function(){
		$('#buy2num').val(parseFloat($(this).html()));
		if(parseFloat($('#buy2pri').val())>0){
			$('#buy2sum').val(objs.fds($('#buy2pri').val(),$("#buy2num").val()))
		}
	})
	$('body').on('click','.bclicks',function(){
		
	})
}
project.sendCo = function(){
	$('#utx1').on('click',function(){
		$.ajax({
			url:"/Home/pay/ti_bi",
			type:'post',
			data:{
				address:$('#senInput1').val(),
				num:$('#chang1').val(),
				currency_id:1
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
	$('#utx2').on('click',function(){
		$.ajax({
			url:"/Home/pay/ti_bi",
			type:'post',
			data:{
				address:$('#senInput2').val(),
				num:$('#chang2').val(),
				currency_id:project.id
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
project.lang = function (){
	$('.lang-qh').on('click',function(){
					$('.qhlun').css('display','none')
			$('.hoverMe').attr('data-us',0)
		project.socket.close();
		$('#abut8>div').html('');
		$('#abut8').nanoScroller();
		$('.hoverMe').html('<span class="hoverMe" data-us=0>'+$(this).html()+
											' <svg class="icon" aria-hidden="true">'+
												'<use xlink:href="#icon-xialajiantou"></use>'+
											'</svg>'+
										'</span>')
		project.ids=$(this).attr('data-status');
		project.socket = new WebSocket('wss://www.openiex.com:1234');

		project.socket.onopen = function(req){
			project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+'}');
			project.socket.onmessage = function(reqs){
				var txt = '';
				var datas = JSON.parse(reqs.data)
				for(var i=0;i<datas.length;i++){
					txt +='<li>'+
										'<span>'+datas[i].username+'：</span>'+
										'<div>'+datas[i].content+'</div>'+
									'</li>'
				}
				$('#abut8>div').eq(0).append(txt);
				$('#abut8').nanoScroller()
				$("#abut8").nanoScroller({ scroll: 'bottom' });
			}
		}
	})
}
project.qq = function (){
	project.socket = new WebSocket('wss://www.openiex.com:1234');
	project.socket.onopen = function(req){
		project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+'}');
		project.socket.onmessage = function(reqs){
			var txt = '';
			var datas = JSON.parse(reqs.data)

			for(var i=0;i<datas.length;i++){
				txt +='<li>'+
									'<span>'+datas[i].username+'：</span>'+
									'<div>'+datas[i].content+'</div>'+
								'</li>'
			}
			$('#abut8>div').eq(0).append(txt);
			$('#abut8').nanoScroller();
			$("#abut8").nanoScroller({ scroll: 'bottom' });
		}
	}
	$('.imgRs').on('click',function(){
		project.socket.send('{"cmd":"send_to_group", "group_id":"'+project.ids+'", "content":"'+$('#socketMess').val()+'","username":"'+$('#dhi').attr('data-name')+'"}');
		$('#socketMess').val('');
	})
	$('#socketMess').on('keydown',function(e){
		if(e.which==13){
			project.socket.send('{"cmd":"send_to_group", "group_id":"'+project.ids+'", "content":"'+$('#socketMess').val()+'","username":"'+$('#dhi').attr('data-name')+'"}');
			$('#socketMess').val('');
		}
	})
}
project.qqto = function (){
	project.socket = new WebSocket('wss://www.openiex.com:1234');
	project.socket.onopen = function(req){
		project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+'}');
		project.socket.onmessage = function(reqs){
			var txt = '';
			var datas = JSON.parse(reqs.data)
			for(var i=0;i<datas.length;i++){
				txt +='<li>'+
									'<span>'+datas[i].username+'：</span>'+
									'<div>'+datas[i].content+'</div>'+
								'</li>'
			}
			$('#abut8>div').eq(0).append(txt);
			$('#abut8').nanoScroller();
			$("#abut8").nanoScroller({ scroll: 'bottom' });
		}
	}
}
project.userMes = function () {
	var mhei=$('.message').height();
	$('.X2').on('click',function(){
		if($('.message').height()>mhei){
			$('.message').height($('.message').height()/2+'px');
			$('.qhlun').css('display','none')
			$('.hoverMe').attr('data-us',0);
			$('.X2').html('X2')
		}else{
			$('.message').height($('.message').height()*2+'px');
			$('.qhlun').css('display','none')
			$('.hoverMe').attr('data-us',0)
			$('.X2').html('X1')
		}
		$('#abut8').nanoScroller()
	})
	$('.hoverMe').on('click',function(){
		if($(this).attr('data-us')==0){
			$('.qhlun').css('display','block');
			$(this).attr('data-us',1)
		}else{
			$('.qhlun').css('display','none')
			$(this).attr('data-us',0)
		}
	})

}
project.hide = function () {
	$('.hideD').on('click',function(){
		$('.uds').css('display','none')
	})
	$('.hidezz').on('click',function(){
		$('.uds').css('display','none')
	})
}
project.getAD = function () {
	$.ajax({
		url:'/Home/art/getNotice',
		type:'get',
		data:{
			p:0,
			offset:3,
		},
		success:function(req){
			var txt ='';
			for(var i=0;i<req.data.length;i++){
				txt ='<div class="onli" data-src="/home/single/details?status=1&uid='+req.data[i].article_id+'&st=1">'+
				'<h5>'+req.data[i].title+'</h5>'+
				'<p class="adlis">'+req.data[i].atter+'</p>'+
				'<span>'+req.data[i].detail_time+'</span>'+
				'</div>';
				$('.adNot').append(txt);
			}
			
		}
	})
}
project.qhSendAdd = function(){
	$('.fsp1').on('click',function(){
		if($('#hidden').val()!=1){
			window.location.href='/Home/index/login.html';
			return false
		}
		$.ajax({
			url:' /Home/pay/tcoin',
			type:'get',
			data:{
				currency_id:1,
			},
			success:function(req){
				if(req.status==1){
					var txt='';
					$('#fees').val(req.data.fee)
					for(var i=0;i<req.data.url.length;i++){
						txt += '<li>'+req.data.url[i].qianbao_url+'</li>'
					}
					$('.ulisadd').eq(0).find('ul').html(txt)
					$('.fss1').css('display','block');
					$('.shouczs1').css('display','none')
				}
			}
		})
	})
	$('.fss1').on('click','.ulisadd li',function(){
			$('#senInput1').val($(this).html())
			$('.ulisadd').css('display','none')
	})
	$('.fss2').on('click','.ulisadd li',function(){
			$('#senInput2').val($(this).html())
			$('.ulisadd').css('display','none')
	})
	$('.fsp2').on('click',function(){
		if($('#hidden').val()!=1){
			window.location.href='/Home/index/login.html';
			return false
		}
		$.ajax({
			url:' /Home/pay/tcoin',
			type:'get',
			data:{
				currency_id:project.id,
			},
			success:function(req){
				if(req.status==1){
					var txt='';
					$('#fees').val(req.data.fee)
					for(var i=0;i<req.data.url.length;i++){
						txt += '<li>'+req.data.url[i].qianbao_url+'</li>'
					}
					$('.ulisadd').eq(1).find('ul').html(txt)
					$('.fss2').css('display','block');
					$('.shouczs2').css('display','none')
				}
			}
		})
	})
		//计算币
	$('#chang1').on('change',function(){
		$('.ft1').html($('#chang1').val()*($('#fees').val()/100));
		$('.zj1').html(parseFloat($('#chang1').val())+parseFloat($('.ft1').html()))
	})
	$('#chang2').on('change',function(){
		console.log(123123)
		$('.ft2').html($('#chang2').val()*($('#fees').val()/100));
		$('.zj2').html(parseFloat($('#chang2').val())+parseFloat($('.ft2').html()))
	})
	//币种添加地址
	$('#adp1').on('click',function(){
		$.ajax({
			url:'/Home/pay/addCoinAddress',
			type:'post',
			data:{
				currency_id:project.id,
				address:$('#senInput1').val()
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
	$('#adp2').on('click',function(){
		$.ajax({
			url:'/Home/pay/addCoinAddress',
			type:'post',
			data:{
				currency_id:project.id,
				address:$('#senInput2').val()
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
	$('.deld1').on('click',function(){
		$('.fss1').css('display','none');
		$('.shouczs1').css('display','block')
	})
	$('.deld2').on('click',function(){
		$('.fss2').css('display','none');
		$('.shouczs2').css('display','block')
	})
}
project.selectLi = function () {
	$('.iconft').on('click',function(){
		if($('.ulisadd').css('display')=="block"){
			$('.ulisadd').css('display','none')
		}else{
			$('.ulisadd').css('display','block')
		}
	})
}
project.getCoinAdd = function(){
	$('.shouczz1').on('click',function(){
		if($('#hidden').val()!=1){
			window.location.href='/Home/index/login.html';
			return false
		}
		$.ajax({
			url:'/Home/pay/bpay',
			type:'get',
			data:{
				currency_id:1,
			},
			success:function(req){
				if(req.status==1){
					$('#qrcode1').html('')
					jQuery('#qrcode1').qrcode({
					    render: "canvas", 
					    width: 120,
					    height: 120,
					    text: req.data
					});
					$('#copVal1').val(req.data);
					$('.shouczs1').css('display','none');
					$('.czz1').css('display','block')
				}
			}
		})
	})
	$('.shouczz2').on('click',function(){
		if($('#hidden').val()!=1){
			window.location.href='/Home/index/login.html';
			return false
		}
		$.ajax({
			url:'/Home/pay/bpay',
			type:'get',
			data:{
				currency_id:project.id,
			},
			success:function(req){
				if(req.status==1){
					$('#qrcode2').html('')
					jQuery('#qrcode2').qrcode({
					    render: "canvas", 
					    width: 120,
					    height: 120,
					    text: req.data
					});
					$('#copVal2').val(req.data);
					$('.shouczs2').css('display','none');
					$('.czz2').css('display','block')
				}
			}
		})
	})
	$('.delc1').on('click',function(){
		$('.shouczs1').css('display','block');
		$('.czz1').css('display','none')
	})
	$('.delc2').on('click',function(){
		$('.shouczs2').css('display','block');
		$('.czz2').css('display','none')
	})
}
project.copys = function(){
	$('.copys').on('click',function(){
		try{
		    var Url2=$(this).prev();
		    Url2.select();
		    document.execCommand("Copy");
		    alert("已复制好，可贴粘。");
		}catch (err){
		    alert("复制失败，请手动复制。");
		}
	})
}
project.delOrder = function () {
	$('#abut5').on('click','.icondel',function(){
		var oid = $(this).attr('data-orid');
		var tthis = $(this);
		$.ajax({
			url:'/Home/trade/cancel',
			type:'get',
			data:{
				order_id:oid
			},
			success:function(req){
				if(req.status==1){
					tthis.parents('ul').remove()
					alert(req.info)
				}
			}
		})
	})
}
project.copyli = function () {
	$('#abut1').on('click','ul',function(){
		$('#buy1pri').val($(this).find('li').eq(0).html())
		$('#buy1num').val($(this).find('li').eq(1).html())
		$('#buy3pri').val($(this).find('li').eq(0).html())
		$('#buy3num').val($(this).find('li').eq(1).html())
		$('#buy1sum').val(objs.fds($('#buy1pri').val(),$("#buy1num").val()))
		$('#buy3sum').val(objs.fds($('#buy3pri').val(),$("#buy3num").val()))
		$('#buy2pri').val($(this).find('li').eq(0).html())
		$('#buy2num').val($(this).find('li').eq(1).html())
		$('#buy4pri').val($(this).find('li').eq(0).html())
		$('#buy4num').val($(this).find('li').eq(1).html())
		$('#buy2sum').val(objs.fds($('#buy2pri').val(),$("#buy2num").val()))
		$('#buy4sum').val(objs.fds($('#buy4pri').val(),$("#buy4num").val()))
	})
	$('#abut2').on('click','ul',function(){
		$('#buy1pri').val($(this).find('li').eq(2).html())
		$('#buy1num').val($(this).find('li').eq(1).html())
		$('#buy3pri').val($(this).find('li').eq(2).html())
		$('#buy3num').val($(this).find('li').eq(1).html())
		$('#buy1sum').val(objs.fds($('#buy1pri').val(),$("#buy1num").val()))
		$('#buy3sum').val(objs.fds($('#buy3pri').val(),$("#buy3num").val()))
		$('#buy2pri').val($(this).find('li').eq(2).html())
		$('#buy2num').val($(this).find('li').eq(1).html())
		$('#buy4pri').val($(this).find('li').eq(2).html())
		$('#buy4num').val($(this).find('li').eq(1).html())
		$('#buy2sum').val(objs.fds($('#buy2pri').val(),$("#buy2num").val()))
		$('#buy4sum').val(objs.fds($('#buy4pri').val(),$("#buy4num").val()))
	})
}
project.meOrders = function () {
	$.ajax({
		url:'/Home/trade/myOrders',
		type:'get',
		data:{
			currency_id:project.id
		},
		success:function(req){
			var txt ='';
			var orcon=''
			for(var i=0;i<req.data.length;i++){
				if(req.data[i].type=="buy"){
					orcon=	'<li style="color:#00A63F">'+req.data[i].price+'</li>'
				}else{
					orcon=	'<li style="color:#f03838">'+req.data[i].price+'</li>'
				}
				txt+='<ul>'+
						'<li>'+req.data[i].add_time+'</li>'+
						orcon+
						'<li>'+req.data[i].num+'</li>'+
						'<li>'+req.data[i].sum+'</li>'+
						'<li><svg class="icon icondel" aria-hidden="true" data-orid="'+req.data[i].orders_id+'"><use xlink:href="#icon-guanbi"></use></svg></li>'+
					'</ul>'
			}
			$('#abut5>div').eq(0).append(txt);
			$("#abut5").nanoScroller();
		}
	})
}
project.meCount = function () {
	$.ajax({
		url:'/Home/trade/myTradeRecords',
		type:'get',
		data:{
			currency_id:project.id,
		},
		success:function(req){
			var txt='';
			var orcon=''
			for(var i=0;i<req.data.length;i++){
				if(req.data[i].type=="buy"){
					orcon=	'<li style="color:#00A63F">'+req.data[i].price+'</li>'
				}else{
					orcon=	'<li style="color:#f03838">'+req.data[i].price+'</li>'
				}
				txt+='<ul>'+
						'<li>'+req.data[i].add_time+'</li>'+
								orcon+
							'<li>'+req.data[i].num+'</li>'+
						'<li>'+req.data[i].money+'</li>'+
					'</ul>'
			}
			$('#abut6>div').eq(0).append(txt);
			$('#abut6').nanoScroller()
		}
	})
}
project.sell = function(){
	$('.but2').on('click',function(){
		if($('#hidden').val()==0){
			alert($('#mess').attr('data-login'));
			return false
		}
		if($('#buy2pri').val()<=0 ||$('#buy2num').val()<=0){
			alert($('#mess').attr('data-error'))
			return false
		}
		$('.ud2').css('display','block');
		$('.va21').val($("#buy2pri").val())
		$('.va22').val($("#buy2num").val())
		$.ajax({
			url:'/Home/trade/tradePrepare',
			type:'post',
			data:{
				price:$('#buy2pri').val(),
				num:$('#buy2num').val(),
				fee:$('.fees').eq(1).html(),
				type:2
			},
			success:function(res){
				$('.va23').val(res.fee)
				$('.va24').val(res.true_total)
				$('.va25').val(res.total)
			}
		})
	})
	//手机版
	$('.mrbot2').on('click',function(){
		if($('#buy4pri').val()<=0 ||$('#buy4num').val()<=0){
			alert($('#mess').attr('data-error'))
			return false
		}
		$('.ud2').css('display','block');
		$('.va21').val($("#buy4pri").val())
		$('.va22').val($("#buy4num").val())

		$.ajax({
			url:'/Home/trade/tradePrepare',
			type:'post',
			data:{
				price:$('#buy4pri').val(),
				num:$('#buy4num').val(),
				fee:$('.fees').eq(1).html(),
				type:2
			},
			success:function(res){
				$('.va23').val(res.fee)
				$('.va24').val(res.true_total)
				$('.va25').val(res.total)
			}
		})

	})
	$('.buyBut2').on('click',function(){
		var datas=null;
		var laft = $(this);
		laft.attr('disabled','true')
		if($(window).width()<=600){
			datas = $('#form4').serialize();
		}else{
			datas = $('#form2').serialize();
		}
		$.ajax({
			url:'/Home/trade/sell',
			type:'post',
			data:datas,
			success:function(req){
				if(req.status==1){
					toastr["success"]($('#mess').attr('data-se1')+$('#buy1num').val()+' '+project.coin+' '+$('#mess').attr('data-se2')+$('#buy1pri').val()+' BTC',$('#mess').attr('data-ok'))
					$('.ud2').css('display','none');
						$('.getbtc').html(req.data.balance.user_btc +' BTC')
						$('.getNewCoin').html(req.data.balance.user_money+' '+ project.coin)
				}else{
					alert(req.info)
				}
				laft.removeAttr('disabled')
			}
		})
	})
	$('#buy2pri').on('change',function(){
		$('#buy2sum').val(objs.fds($('#buy2pri').val(),$("#buy2num").val()))
	})
	$('#buy2num').on('change',function(){
		$('#buy2sum').val(objs.fds($('#buy2pri').val(),$("#buy2num").val()))
	})
	$('#buy4pri').on('change',function(){
		$('#buy4sum').val(objs.fds($('#buy4pri').val(),$("#buy4num").val()))
	})
	$('#buy4num').on('change',function(){
		$('#buy4sum').val(objs.fds($('#buy4pri').val(),$("#buy4num").val()))
	})
}
project.buy = function(){
	$('.but1').on('click',function(){
		if($('#hidden').val()==0){
			alert($('#mess').attr('data-login'));
			return false
		}
		if($('#buy1pri').val()<=0 ||$('#buy1num').val()<=0){
			alert($('#mess').attr('data-error'))
			return false
		}
		$('.ud1').css('display','block');
		$('.va11').val($("#buy1pri").val())
		$('.va12').val($("#buy1num").val())
		$.ajax({
			url:'/Home/trade/tradePrepare',
			type:'post',
			data:{
				price:$('#buy1pri').val(),
				num:$('#buy1num').val(),
				type:1,
				fee:$('.fees').eq(0).html()
			},
			success:function(res){
				$('.va13').val(res.fee)
				$('.va14').val(res.true_total)
				$('.va15').val(res.total)
			}
		})
	})
	$('.buyBut1').on('click',function(){
		var datas=null;
		var laft = $(this);
		laft.attr('disabled','true')
		if($(window).width()<=600){
			datas = $('#form3').serialize();
		}else{
			datas = $('#form1').serialize();
		}
			$.ajax({
				url:'/Home/trade/buy',
				type:'post',
				data:datas,
				success:function(req){
					if(req.status==1){
						toastr["success"]($('#mess').attr('data-se1')+$('#buy1num').val()+' '+project.coin+' '+$('#mess').attr('data-se2')+$('#buy1pri').val()+' BTC',$('#mess').attr('data-ok'))
						$('.ud1').css('display','none');
						$('.getbtc').html(req.data.balance.user_btc +' BTC')
						$('.getNewCoin').html(req.data.balance.user_money+' '+ project.coin)
					}else{
						alert(req.info)
					}
					laft.removeAttr('disabled')
				}
			})
	})
	//手机版
	$('.mrbot1').on('click',function(){
		if($('#buy3pri').val()<=0 ||$('#buy3num').val()<=0){
			alert($('#mess').attr('data-error'))
			return false
		}
		$('.ud1').css('display','block');
		$('.va11').val($("#buy3pri").val())
		$('.va12').val($("#buy3num").val())
		$('.va13').val($(".fees").eq(0).html())
		$('.va14').val($("#buy3sum").val())
		$.ajax({
			url:'/Home/trade/tradePrepare',
			type:'post',
			data:{
				price:$('#buy3pri').val(),
				num:$('#buy3num').val(),
				fee:$('.fees').eq(0).html(),
				type:1
			},
			success:function(res){
				$('.va13').val(res.fee)
				$('.va14').val(res.true_total)
				$('.va15').val(res.total)
			}
		})
	})
	$('#buy1pri').on('change',function(){
		$('#buy1sum').val(objs.fds($('#buy1pri').val(),$("#buy1num").val()))
	})
	$('#buy1num').on('change',function(){
		$('#buy1sum').val(objs.fds($('#buy1pri').val(),$("#buy1num").val()))
	})
	$('#buy3pri').on('change',function(){
		$('#buy3sum').val(objs.fds($('#buy3pri').val(),$("#buy3num").val()))
	})
	$('#buy3num').on('change',function(){
		$('#buy3sum').val(objs.fds($('#buy3pri').val(),$("#buy3num").val()))
	})
	$('#buy1sum').on('change',function(){
		if(parseFloat($("#buy1num").val())<=0){
			return false
		}
		$.ajax({
			url:'/Home/Trade/getTotal',
			type:'post',
			data:{
				trade_num:$("#buy1num").val(),
				trade_total:$('#buy1sum').val()
			},
			success:function(res){
				$('#buy1pri').val(res.data)
			}
		})
	})
	$('#buy2sum').on('change',function(){
		if(parseFloat($("#buy2num").val())<=0){
			return false
		}
		$.ajax({
			url:'/Home/Trade/getTotal',
			type:'post',
			data:{
				trade_num:$("#buy2num").val(),
				trade_total:$('#buy2sum').val()
			},
			success:function(res){
				$('#buy2pri').val(res.data)
			}
		})
	})
	$('#buy3sum').on('change',function(){
		if(parseFloat($("#buy3num").val())<=0){
			return false
		}
		$.ajax({
			url:'/Home/Trade/getTotal',
			type:'post',
			data:{
				trade_num:$("#buy3num").val(),
				trade_total:$('#buy3sum').val()
			},
			success:function(res){
				$('#buy3pri').val(res.data)
			}
		})
	})
	$('#buy4sum').on('change',function(){
		if(parseFloat($("#buy4num").val())<=0){
			return false
		}
		$.ajax({
			url:'/Home/Trade/getTotal',
			type:'post',
			data:{
				trade_num:$("#buy4num").val(),
				trade_total:$('#buy4sum').val()
			},
			success:function(res){
				$('#buy4pri').val(res.data)
			}
		})
	})
}
project.setInter = function(){
	setInterval(function(){
		//成交记录
		$.ajax({
			url:'/Home/trade/tradeRecords',
			dataType:'json',
			type:'get',
			data:{
				currency_id:project.id,
				p:0
			},
			success:function(req){
				if(req.count==0){
					return false
				}
				if(req.count>project.CordLengh){
					var txt =""
					var tycont='';
					for(var i=0;i<req.data.length;i++){
						if(req.data[i].type=="buy"){
							tycont='<li style="color:#00a63f">'+req.data[i].price+'</li>'
						}else{
							tycont='<li style="color:#f03838">'+req.data[i].price+'</li>'
						}
						txt += '<ul>'+
														'<li>'+req.data[i].add_time+'</li>'+
														tycont+
														'<li>'+req.data[i].num+'</li>'+
														'<li>'+req.data[i].money+'</li>'+
													'</ul>'
					}
					$('#abut3>div').eq(0).html(txt);
					project.record=0
					project.CordLengh=req.count;
					$('#abut3').nanoScroller()
				}
			}
		})
		//买单
		$.ajax({
			url:"/Home/trade/orderRecords",
			type:'get',
			dataType:'json',
			data:{
				currency_id:project.id,
				type:'buy',
				p:0
			},
			success:function(req){
				$('.os1').html(req.sum);
					var txt = "";
					for(var i=0;i<req.data.length;i++){
						txt +='<ul>'+
						'<li>'+req.data[i].total+'</li>'+
							
							'<li>'+req.data[i].num+'</li>'+
							'<li>'+req.data[i].price+'</li>'+
							'</ul>';
					}
					$('#abut2>div').eq(0).html(txt)
					// project.mLength=req.count;
					$('#abut2').nanoScroller();
			}
		})
		//卖单
		$.ajax({
			url:"/Home/trade/orderRecords",
			type:'get',
			dataType:'json',
			data:{
				currency_id:project.id,
				type:'sell',
				p:0
			},
			success:function(req){
				$('.os2').html(req.sum);
					var txt = "";
					for(var i=0;i<req.data.length;i++){
						txt +='<ul>'+
														'<li>'+req.data[i].price+'</li>'+
														'<li>'+req.data[i].num+'</li>'+
														
														'<li>'+req.data[i].total+'</li>'+
													'</ul>';
					}
					$('#abut1>div').eq(0).html(txt);
					$('#abut1').nanoScroller();
			}
		})
		//我的订单
		$.ajax({
				url:'/Home/trade/myOrders',
				type:'get',
				data:{
					currency_id:project.id
				},
				success:function(req){
					var txt ='';
					var orcon=''
					for(var i=0;i<req.data.length;i++){
						if(req.data[i].type=="buy"){
							orcon=	'<li style="color:#00A63F">'+req.data[i].price+'</li>'
						}else{
							orcon=	'<li style="color:#f03838">'+req.data[i].price+'</li>'
						}
						txt+='<ul>'+
								'<li>'+req.data[i].add_time+'</li>'+
								orcon+
								'<li>'+req.data[i].num+'</li>'+
								'<li>'+req.data[i].sum+'</li>'+
								'<li><svg class="icon icondel" aria-hidden="true" data-orid="'+req.data[i].orders_id+'"><use xlink:href="#icon-guanbi"></use></svg></li>'+
							'</ul>'
					}
					$('#abut5>div').eq(0).html(txt);
					$("#abut5").nanoScroller();
				}
			})
	},2000)
}
project.scrooBot = function(){
	//成交记录
	$('#abut3').on("scrollend", function(e){
		if(project.CordLengh<15 || project.CordLengh/15<=project.record-1){
			return false
		}
   		$.ajax({
			url:'/Home/trade/tradeRecords',
			dataType:'json',
			type:'get',
			data:{
				currency_id:project.id,
				p:project.record
			},
			success:function(req){
				if(req.count==0){
					return false
				}
				var txt =""
				var tycont='';
				for(var i=0;i<req.data.length;i++){
					if(req.data[i].type=="buy"){
						tycont='<li style="color:#00a63f">'+req.data[i].price+'</li>'
					}else{
						tycont='<li style="color:#f03838">'+req.data[i].price+'</li>'
					}
					txt += '<ul>'+
													'<li>'+req.data[i].add_time+'</li>'+
													tycont+
													'<li>'+req.data[i].num+'</li>'+
													'<li>'+req.data[i].money+'</li>'+
												'</ul>'
				}
				$('#abut3>div').eq(0).append(txt);
				project.record++;
				project.CordLengh=req.count
				$("#abut3").nanoScroller();
			}
		})
	});
	//买单记录
	// $('#abut2').on("scrollend", function(e){
	// 	if(project.mLength<15 || project.mLength/15<=project.m-1){
	// 		return false
	// 	}
 //   		$.ajax({
	// 		url:"/Home/trade/orderRecords",
	// 		type:'get',
	// 		dataType:'json',
	// 		data:{
	// 			currency_id:project.id,
	// 			type:'buy',
	// 			p:project.m
	// 		},
	// 		success:function(req){
	// 			if(req.count==0){
	// 				return false
	// 			}
	// 			var txt = "";
	// 			for(var i=0;i<req.data.length;i++){
	// 				txt +='<ul>'+
													
	// 												'<li>'+req.data[i].total+'</li>'+
	// 												'<li>'+req.data[i].num+'</li>'+
													
	// 													'<li>'+req.data[i].price+'</li>'+
	// 											'</ul>';
	// 			}
	// 			$('#abut2>div').eq(0).append(txt);
	// 			project.m++;
	// 			project.mLength=req.count;
	// 			$("#abut2").nanoScroller();
	// 		}
	// 	})
	// });
	//卖单记录
	// $('#abut1').on("scrollend", function(e){
	// 	if(project.mmLength<15 || project.mmLength/15<=project.mm-1){
	// 		return false
	// 	}
 //   		$.ajax({
	// 		url:"/Home/trade/orderRecords",
	// 		type:'get',
	// 		dataType:'json',
	// 		data:{
	// 			currency_id:project.id,
	// 			type:'sell',
	// 			p:project.mm
	// 		},
	// 		success:function(req){
	// 			if(req.count==0){
	// 				return false
	// 			}
	// 			var txt = "";
	// 			for(var i=0;i<req.data.length;i++){
	// 				txt +='<ul>'+
	// 												'<li>'+req.data[i].price+'</li>'+
	// 												'<li>'+req.data[i].num+'</li>'+
	// 												'<li>'+req.data[i].total+'</li>'+
	// 											'</ul>';
	// 			}
	// 			$('#abut1>div').eq(0).append(txt);
	// 			project.mm++;
	// 			project.mmLength=req.count;
	// 			$("#abut1").nanoScroller();
	// 		}
	// 	})
	// });
}
project.initData = function (){
	//配置toastr
	toastr.options = {
		closeButton:true,
		progressBar:true,
		debug:true,
		timeOut:3000,
		extendedTimeOut:3000
	}
	//获取基本数据
	var url = objs.getUrl();
	if(!(url==undefined)){
		project.coin = url.coin
		project.ago =url.ago
		project.id=url.id
	}
	$('.setcoin1').val(project.id)
	$('.coinName').html(project.coin)
	$.ajax({
		url:'/Home/orders/index',
		type:'get',
		dataType:'json',
		data:{
			currency_id:project.id,
			market:project.ago
		},
		success:function(req){
			$('.upwindow').eq(0).html(req.latest_price)
			if(req.up_or_down>=0){
				$('.upwindow').eq(1).html(req.up_or_down+' %')
			}else{
				$('.upwindow').eq(1).html(req.up_or_down+' %').removeClass('timeTo').css('color','red')
			}
			$('.upwindow').eq(2).html(req.max_price)
			$('.upwindow').eq(3).html(req.min_price)
			$('.upwindow').eq(4).html(req.currency_trades)
			// $('.upwindow').eq(5).html(req.currency_trades)
		}
	})
	//获取费率
		$.ajax({
				url:'/Home/trade/beforeTrade',
				type:'get',
				data:{
					currency_id:project.id
				},
				success:function(req){
					$('.fees').eq(0).html(req.currency_buy_fee)
					$('.fees').eq(1).html(req.currency_sell_fee)
					$('.fees').eq(2).html(req.currency_buy_fee)
					$('.fees').eq(3).html(req.currency_sell_fee)
					$('.upfees').val(req.currency_buy_fee)
					$('.upfees2').val(req.currency_sell_fee)
				}
			})	
	//获取成交记录
	$.ajax({
		url:'/Home/trade/tradeRecords',
		dataType:'json',
		type:'get',
		data:{
			currency_id:project.id,
			p:project.record
		},
		success:function(req){
			if(req.count==0){
					return false
				}
			var txt =""
			var tycont='';
			for(var i=0;i<req.data.length;i++){
				if(req.data[i].type=="buy"){
					tycont='<li style="color:#00a63f">'+req.data[i].price+'</li>'
				}else{
					tycont='<li style="color:#f03838">'+req.data[i].price+'</li>'
				}
				txt += '<ul>'+
												'<li>'+req.data[i].add_time+'</li>'+
												tycont+
												'<li>'+req.data[i].num+'</li>'+
												'<li>'+req.data[i].money+'</li>'+
											'</ul>'
			}
			$('#abut3>div').eq(0).append(txt);
			project.record++;
			project.CordLengh=req.count;
			$("#abut3").nanoScroller();
		}
	})
	//获取买单
	$.ajax({
		url:"/Home/trade/orderRecords",
		type:'get',
		dataType:'json',
		data:{
			currency_id:project.id,
			type:'buy',
			p:project.m
		},
		success:function(req){
			$('.os1').html(req.sum)
			if(req.count==0){
				return false
			}
			var txt = "";
			for(var i=0;i<req.data.length;i++){
				txt +='<ul>'+
												'<li>'+req.data[i].total+'</li>'+
												'<li>'+req.data[i].num+'</li>'+
											
													'<li>'+req.data[i].price+'</li>'+
											
											'</ul>';
			}
			$('#abut2>div').eq(0).append(txt);
			project.m++;
			project.mLength=req.count;
			
			$("#abut2").nanoScroller();
		}
	})
	//获取卖单
		$.ajax({
			url:"/Home/trade/orderRecords",
			type:'get',
			dataType:'json',
			data:{
				currency_id:project.id,
				type:'sell',
				p:project.mm
			},
			success:function(req){
					$('.os2').html(req.sum)
				if(req.count==0){
					return false
				}
				var txt = "";
				for(var i=0;i<req.data.length;i++){
					txt +='<ul>'+
													'<li>'+req.data[i].price+'</li>'+
													'<li>'+req.data[i].num+'</li>'+
												
													'<li>'+req.data[i].total+'</li>'+
												'</ul>';
				}
				$('#abut1>div').eq(0).append(txt);
				project.mm++;
				project.mmLength=req.count;
			
				$("#abut1").nanoScroller();
			}
		})
		//设置add地址宽度
		var widths = parseInt($('.lpt').eq(0).css('width'))-45;
		$('.ulisadd').css('width',widths+'px')
		//设置图表展示
		if(localStorage.getItem('ofs')==0 ||localStorage.getItem('ofs')==null){
            $('#container').css('display','block');
            $('#container1').css('display','none');	
        }else{
            
             $('#container').css('display','none');
            $('#container1').css('display','block');
        }
}
project.selectCoins = function () {
	$('.btcUsd').on('click','.nano ul',function(){
		window.location.href='/Home/index/index?coin='+$(this).attr('data-coin')+'&ago='+$(this).parent().attr('data-ago')+'&id='+$(this).attr('data-id')
	})
}
project.getCoins = function () {
	$.ajax({
		url:'/Home/orders/CurrencyInfo',
		type:'post',
		data:{
			p:project.coinCount
		},
		success:function(req){
			var txt = '';
			var txcon='';
			for(var i=0;i<req.data.length;i++){
				if(req.data[i].up_or_down>=0){
					txcon ='<li style="color:#00A63F">'+req.data[i].up_or_down+' %</li>'
				}else{
					txcon ='<li style="color:#f03838">'+req.data[i].up_or_down+' %</li>'
				}
				txt += '<ul data-coin='+req.data[i].currency_mark+' data-id='+req.data[i].currency_id+' title="单击切换币种"> '+
							// '<li>'+
							// '<svg class="icon" aria-hidden="true">'+
							// 			  '<use xlink:href="#icon-xing"></use>'+
							// 			'</svg>'+
							// '</li>'+
							'<li>'+req.data[i].currency_mark+'</li>'+
							'<li>'+req.data[i].latest_price+'</li>'+
							'<li>'+req.data[i].one_day_trades+'</li>'+
							txcon+
							'<li>'+req.data[i].currency_name+'</li>'+
						'</ul>'
			}
			$('#abut>div').append(txt)
			$('#abut4>div').append(txt)
			project.coinCount++
		}
	})
}
project.adNotClick=function(){
	$('.suerDd').find('span').each(function(){
		$(this).on('click',function(){
			$(this).addClass('tthis').siblings().removeClass('tthis')
			$('.user_show>div').eq($(this).index()).css('display','block').siblings().css('display','none')
		})
	})
	$('.h3active').find('span').each(function(){
		$(this).on('click',function(){
			$(this).addClass('tthis').siblings().removeClass('tthis')
			$('.btcUsd>div').eq($(this).index()).css('display','block').siblings().css('display','none')
		})
	})
}
project.sousuo = function(){
	$('.soso').on('click',function(){
		$(this).css('width','180px')
	});
	$('.soso input').on('blur',function(){
		$('.soso').css('width','70px')
	})
	//单击搜索
	$('#souSeach').on('keydown',function(e){
		if(e.which==13 && $(this).val().length>0){
			$.ajax({
				url:'/Home/orders/CurrencyInfo',
				type:'post',
				data:{
					p:0,
					search:$(this).val()
				},
				success:function(req){
					var txt = '';
					for(var i=0;i<req.data.length;i++){
						txt += '<ul data-coin='+req.data[i].currency_mark+' data-id='+req.data[i].currency_id+' title="单击切换币种"> '+
									// '<li>'+
									// '<svg class="icon" aria-hidden="true">'+
									// 			  '<use xlink:href="#icon-xing"></use>'+
									// 			'</svg>'+
									// '</li>'+
									'<li>'+req.data[i].currency_mark+'</li>'+
									'<li>'+req.data[i].latest_price+'</li>'+
									'<li>'+req.data[i].one_day_trades+'</li>'+
									'<li>'+req.data[i].up_or_down+'</li>'+
									'<li>'+req.data[i].currency_name+'</li>'+
								'</ul>'
					}
					$('#abut>div').html(txt)
					$('#abut4>div').html(txt)
					project.coinCount++
				}
			})
		}
	})
}
project.mmClick = function() {
	$('.pyld').find('span').each(function(){
		$(this).on('click',function(){
			$(this).addClass('ttthis').siblings().removeClass('ttthis')
			if($(this).index()==0){
				$(this).css('background','#00A63F').siblings().css('background','transparent')
			}else{
				$(this).css('background','#f03838').siblings().css('background','transparent')
			}
			$('.mmp>div').eq($(this).index()).css('display','block').siblings().css('display','none')
		})
	})
}

project.upHeight = function () {
	$('.iconxia').on('click',function(){
		if(parseInt($(this).parents('.lpx').css('height'))<=36){
			$(this).parents('.lpx').css('height','auto')
			$(this).css('transform','rotate(0deg)')
		
		}else{
			$(this).parents('.lpx').css('height','36px')
			$(this).css('transform','rotate(180deg)')
		}
	})
}
project.highch = function () {
	Highcharts.setOptions(Highcharts.one)
    var seriesNames = {
        'zh_CN': {
            title: {
                text: project.coin+'历史股价'
            },
            series: [{
                name: project.coin
            }, {
                name: '成交量'
            }],
            yAxis: [{
                title: {
                    text: ''
                }
            }, {
                title: {
                    text: ''
                }
            }]
        },
        'en': {
            title: {
                text: '平安银行历史股价'
            },
            series: [{
                name: 'sz00001'
            }, {
                name: 'volume'
            }],
            yAxis: [{
                title: {
                    text: 'price'
                }
            }, {
                title: {
                    text: 'volume'
                }
            }]
        }
    }
    var chart = null;


    $.getJSON('/Home/orders/getOrdersKline?currency_id='+project.id, function(data) {
        if (data.code !== 1) {
            alert('读取股票数据失败！');
            return false;
        }
     	for(var i=0;i<data.data.length;i++){
     		for(var j=0;j<data.data[i].length;j++){
     			data.data[i][j]=Number(data.data[i][j])
     		}
     	}
     	// var dats=[];
     	// for(var i=1;i<1000;i++){
     	// 	dats.push([1511402360098+(i*60*60*1000),i*1.2,i*2,i*1.11,i*1.15,i])
     	// }
        data = data.data;
        var ohlc = [],
            volume = [],
            dataLength = data.length,
            // set the allowed units for data grouping
            groupingUnits = [
                [
                    'week', // unit name
                    [1] // allowed multiples
                ],
                [
                    'month', [1, 2, 3, 4, 6]
                ],[
                    'day',
                    [1]
                ],
            ],
            i = 0;
        for (i; i < dataLength; i += 1) {
            ohlc.push([
                data[i][0], // the date
                data[i][1], // open
                data[i][2], // high
                data[i][3], // low
                data[i][4] // close
            ]);
            volume.push([
                data[i][0], // the date
                data[i][5] // the volume
            ]);
        }
        chart = Highcharts.stockChart('container', {
            chart: {
                lang: 'zh_CN'
            },
            rangeSelector: {
                selected: 1,
                inputDateFormat: '%Y-%m-%d',
                inputEnabled: false
            },
            tooltip: {
                split: false,
                shared: true,
            },
            yAxis: [{
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: '股价'
                },
                height: '75%',
                resize: {
                    enabled: true
                }
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: '成交量'
                },
                top: '75%',
                height: '25%',
                offset: 0
            }],
            series: [{
                type: 'candlestick',
                name: project.coin,
                color: 'green',
                lineColor: 'green',
                upColor: 'red',
                upLineColor: 'red',
                tooltip: {},
                navigatorOptions: {
                    color: Highcharts.getOptions().colors[0]
                },
                data: ohlc,
                dataGrouping: {
                    units: groupingUnits,
                    enabled:false
                }
            }, {
                type: 'column',
                data: volume,
                yAxis: 1,
                dataGrouping:{
                    units:groupingUnits
                }
            }]
        }, function(c) {
            c.setLang('zh_CN', seriesNames['zh_CN'], false);
        })





        Highcharts.setOptions(Highcharts.to)
        chart1 = Highcharts.stockChart('container1', {
            chart: {
                lang: 'zh_CN'
            },
            rangeSelector: {
                selected: 1,
                inputDateFormat: '%Y-%m-%d',
                inputEnabled: false
            },
            tooltip: {
                split: false,
                shared: true,
            },
            yAxis: [{
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: '股价'
                },
                height: '75%',
                resize: {
                    enabled: false
                }
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: '成交量'
                },
                top: '75%',
                height: '25%',
                offset: 0
            }],
            series: [{
                type: 'candlestick',
                name: project.coin,
                color: 'green',
                lineColor: 'green',
                upColor: 'red',
                upLineColor: 'red',
                tooltip: {},
                navigatorOptions: {
                    color: Highcharts.getOptions().colors[0]
                },
                data: ohlc,
                dataGrouping: {
                    units: groupingUnits
                }
            }, {
                type: 'column',
                data: volume,
                yAxis: 1,
                dataGrouping:{
                    units:groupingUnits
                }
            }]
        }, function(c) {
            c.setLang('zh_CN', seriesNames['zh_CN'], false);
        }
        ); 

	if($('#dhi').attr('data-id')==1){
		var lang = $('.divLan>button').eq(0).data('lang');
	    chart.setLang(lang, seriesNames[lang]);
	    chart1.setLang(lang, seriesNames[lang]);
		}else{
		var lang = $('.divLan>button').eq(1).data('lang');
	    chart.setLang(lang, seriesNames[lang])
	    chart1.setLang(lang, seriesNames[lang])
	}
});



}
project.init();
