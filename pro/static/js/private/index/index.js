var project={},objs=new obje;project.init=function(){objs.yesNum(),$("#abut").nanoScroller(),$("#abut4").nanoScroller(),this.ids=$("#dhi").attr("data-id"),this.coinCount=1,this.record=1,this.userCord=1,this.m=1,this.mm=1,this.coin=$("#mess").attr("data-cname"),this.id=$("#mess").attr("data-cid"),this.ago="BTC",this.CordLengh=0,this.uCordLength=0,this.mLength=0,this.mmLength=0,project.socket=null,this.adNotClick(),this.sousuo(),this.mmClick(),this.upHeight(),this.getCoins(),this.selectCoins(),this.initData(),this.scrooBot(),this.setInter(),this.buy(),this.sell(),this.copyli(),this.delOrder(),this.copys(),this.getCoinAdd(),this.selectLi(),this.qhSendAdd(),this.getAD(),this.hide(),this.userMes(),this.lang(),this.sendCo(),this.moy(),this.charst(),1==$("#hidden").val()?(this.meOrders(),this.meCount(),this.qq(),this.getmon()):this.qqto()},project.charst=function(){TradingView.onready(function(){window.tvWidget=new TradingView.widget({height:"334px",width:"100%",symbol:project.id,interval:"5",container_id:"charts",datafeed:new Datafeeds.UDFCompatibleDatafeed("https://www.openiex.com/Home/orders"),library_path:"/Public/js/lib/charting_library/",locale:localStorage.getItem("lang")?localStorage.getItem("lang"):"en",drawings_access:{type:"black",tools:[{name:"Regression Trend"}]},disabled_features:["use_localstorage_for_settings","timeframes_toolbar"],enabled_features:["keep_left_toolbar_visible_on_small_screens"],timezone:"Asia/Shanghai",overrides:{"scalesProperties.lineColor":"#e8f0f4","paneProperties.vertGridProperties.color":"#f2f7f9","paneProperties.horzGridProperties.color":"#f2f7f9","mainSeriesProperties.candleStyle.upColor":"#00a63f","mainSeriesProperties.candleStyle.borderColor":"#378658","mainSeriesProperties.candleStyle.borderUpColor":"#225437","mainSeriesProperties.candleStyle.borderDownColor":"#5b1a13"},favorites:{intervals:["1","5","30","60","1D"],chartTypes:["Area","Line"]}});(window.tvWidget=new TradingView.widget({height:"334px",width:"100%",symbol:project.id,container_id:"charts1",interval:"5",datafeed:new Datafeeds.UDFCompatibleDatafeed("https://www.openiex.com/Home/orders"),library_path:"/Public/js/lib/charting_library/",locale:localStorage.getItem("lang")?localStorage.getItem("lang"):"en",drawings_access:{type:"black",tools:[{name:"Regression Trend"}]},disabled_features:["use_localstorage_for_settings","timeframes_toolbar"],enabled_features:["keep_left_toolbar_visible_on_small_screens"],timezone:"Asia/Shanghai",overrides:{"paneProperties.background":"#14384b","paneProperties.vertGridProperties.color":"#264a60","paneProperties.horzGridProperties.color":"#264a60","symbolWatermarkProperties.transparency":90,"scalesProperties.textColor":"#A1B5C1","scalesProperties.lineColor":"#507691"},favorites:{intervals:["1","5","30","60","1D"],chartTypes:["Area","Line"]}})).onChartReady(function(){var t=$("#charts1>iframe").attr("id");$("#"+t).contents().find(".header-chart-panel").css("background","#14384b"),$("#"+t).contents().find(".layout__area--center").css("background","#14384b"),$("#"+t).contents().find(".apply-common-tooltip").css("background","#14384b"),$("#"+t).contents().find(".tv-side-toolbar").css("background","#14384b"),$("#"+t).contents().find(".main").css("background","#14384b"),$("#"+t).contents().find(".apply-common-tooltip").css("color","#fff"),$("#"+t).contents().find(".selected").css("background","#2c3b59 !important")})}),void 0==localStorage.getItem("ofs")?($("#charts").css("display","block"),$("#charts1").css("display","none")):"1"==localStorage.getItem("ofs")?($("#charts1").css("display","block"),$("#charts").css("display","none")):($("#charts").css("display","block"),$("#charts1").css("display","none"))},project.getmon=function(t){$.ajax({url:"/Home/trade/getBalance",type:"get",data:{currency_id:project.id},success:function(t){$(".ozlo").eq(0).html(t.user_btc+" BTC"),$(".ozlo").eq(1).html(t.user_money+" "+project.coin),$(".ozlo").eq(2).html($("#mess").attr("data-ye")+'<b class="bclicks">'+t.user_btc+"</b> BTC"),$(".ozlo").eq(3).html($("#mess").attr("data-ye")+'<b class="bclicks">'+t.user_money+"</b> "+project.coin),$(".ozlo2").eq(0).html(t.user_btc),$(".ozlo2").eq(1).html(t.user_money),$(".ozlo3").eq(0).html(t.user_btc),$(".ozlo3").eq(1).html(t.user_money)}})},project.moy=function(){$(".shouczs1 .ozlo").on("click",function(){$("#buy1num").val(parseFloat($(this).html())),parseFloat($("#buy1pri").val())>0&&$("#buy1sum").val(objs.fds($("#buy1pri").val(),$("#buy1num").val()))}),$(".shouczs2 .ozlo").on("click",function(){$("#buy2num").val(parseFloat($(this).html())),parseFloat($("#buy2pri").val())>0&&$("#buy2sum").val(objs.fds($("#buy2pri").val(),$("#buy2num").val()))}),$("body").on("click",".bclicks",function(){})},project.sendCo=function(){$("#utx1").on("click",function(){$.ajax({url:"/Home/pay/ti_bi",type:"post",data:{address:$("#senInput1").val(),num:$("#chang1").val(),currency_id:1},success:function(t){t.status,alert(t.info)}})}),$("#utx2").on("click",function(){$.ajax({url:"/Home/pay/ti_bi",type:"post",data:{address:$("#senInput2").val(),num:$("#chang2").val(),currency_id:project.id},success:function(t){t.status,alert(t.info)}})})},project.lang=function(){$(".lang-qh").on("click",function(){$(".qhlun").css("display","none"),$(".hoverMe").attr("data-us",0),project.socket.close(),$("#abut8>div").html(""),$("#abut8").nanoScroller(),$(".hoverMe").html('<span class="hoverMe" data-us=0>'+$(this).html()+' <svg class="icon" aria-hidden="true"><use xlink:href="#icon-xialajiantou"></use></svg></span>'),project.ids=$(this).attr("data-status"),project.socket=new WebSocket("wss://www.openiex.com:1234"),project.socket.onopen=function(t){project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+"}"),project.socket.onmessage=function(t){for(var e="",a=JSON.parse(t.data),o=0;o<a.length;o++)e+="<li><span>"+a[o].username+"：</span><div>"+a[o].content+"</div></li>";$("#abut8>div").eq(0).append(e),$("#abut8").nanoScroller(),$("#abut8").nanoScroller({scroll:"bottom"})}}})},project.qq=function(){project.socket=new WebSocket("wss://www.openiex.com:1234"),project.socket.onopen=function(t){project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+"}"),project.socket.onmessage=function(t){for(var e="",a=JSON.parse(t.data),o=0;o<a.length;o++)e+="<li><span>"+a[o].username+"：</span><div>"+a[o].content+"</div></li>";$("#abut8>div").eq(0).append(e),$("#abut8").nanoScroller(),$("#abut8").nanoScroller({scroll:"bottom"})}},$(".imgRs").on("click",function(){project.socket.send('{"cmd":"send_to_group", "group_id":"'+project.ids+'", "content":"'+$("#socketMess").val()+'","username":"'+$("#dhi").attr("data-name")+'"}'),$("#socketMess").val("")}),$("#socketMess").on("keydown",function(t){13==t.which&&(project.socket.send('{"cmd":"send_to_group", "group_id":"'+project.ids+'", "content":"'+$("#socketMess").val()+'","username":"'+$("#dhi").attr("data-name")+'"}'),$("#socketMess").val(""))})},project.qqto=function(){project.socket=new WebSocket("wss://www.openiex.com:1234"),project.socket.onopen=function(t){project.socket.send('{"cmd":"add_group", "group_id":'+project.ids+"}"),project.socket.onmessage=function(t){for(var e="",a=JSON.parse(t.data),o=0;o<a.length;o++)e+="<li><span>"+a[o].username+"：</span><div>"+a[o].content+"</div></li>";$("#abut8>div").eq(0).append(e),$("#abut8").nanoScroller(),$("#abut8").nanoScroller({scroll:"bottom"})}}},project.userMes=function(){var t=$(".message").height();$(".X2").on("click",function(){$(".message").height()>t?($(".message").height($(".message").height()/2+"px"),$(".qhlun").css("display","none"),$(".hoverMe").attr("data-us",0),$(".X2").html("X2")):($(".message").height(2*$(".message").height()+"px"),$(".qhlun").css("display","none"),$(".hoverMe").attr("data-us",0),$(".X2").html("X1")),$("#abut8").nanoScroller()}),$(".hoverMe").on("click",function(){0==$(this).attr("data-us")?($(".qhlun").css("display","block"),$(this).attr("data-us",1)):($(".qhlun").css("display","none"),$(this).attr("data-us",0))})},project.hide=function(){$(".hideD").on("click",function(){$(".uds").css("display","none")}),$(".hidezz").on("click",function(){$(".uds").css("display","none")})},project.getAD=function(){$.ajax({url:"/Home/art/getNotice",type:"get",data:{p:0,offset:3},success:function(t){for(var e="",a=0;a<t.data.length;a++)e='<div class="onli" data-src="/home/single/details?status=1&uid='+t.data[a].article_id+'&st=1"><h5>'+t.data[a].title+'</h5><p class="adlis">'+t.data[a].atter+"</p><span>"+t.data[a].detail_time+"</span></div>",$(".adNot").append(e)}})},project.qhSendAdd=function(){$(".fsp1").on("click",function(){if(1!=$("#hidden").val())return window.location.href="/Home/index/login.html",!1;$.ajax({url:" /Home/pay/tcoin",type:"get",data:{currency_id:1},success:function(t){if(1==t.status){var e="";$("#fees").val(t.data.fee);for(var a=0;a<t.data.url.length;a++)e+="<li>"+t.data.url[a].qianbao_url+"</li>";$(".ulisadd").eq(0).find("ul").html(e),$(".fss1").css("display","block"),$(".shouczs1").css("display","none")}}})}),$(".fss1").on("click",".ulisadd li",function(){$("#senInput1").val($(this).html()),$(".ulisadd").css("display","none")}),$(".fss2").on("click",".ulisadd li",function(){$("#senInput2").val($(this).html()),$(".ulisadd").css("display","none")}),$(".fsp2").on("click",function(){if(1!=$("#hidden").val())return window.location.href="/Home/index/login.html",!1;$.ajax({url:" /Home/pay/tcoin",type:"get",data:{currency_id:project.id},success:function(t){if(1==t.status){var e="";$("#fees").val(t.data.fee);for(var a=0;a<t.data.url.length;a++)e+="<li>"+t.data.url[a].qianbao_url+"</li>";$(".ulisadd").eq(1).find("ul").html(e),$(".fss2").css("display","block"),$(".shouczs2").css("display","none")}}})}),$("#chang1").on("change",function(){$(".ft1").html($("#chang1").val()*($("#fees").val()/100)),$(".zj1").html(parseFloat($("#chang1").val())+parseFloat($(".ft1").html()))}),$("#chang2").on("change",function(){console.log(123123),$(".ft2").html($("#chang2").val()*($("#fees").val()/100)),$(".zj2").html(parseFloat($("#chang2").val())+parseFloat($(".ft2").html()))}),$("#adp1").on("click",function(){$.ajax({url:"/Home/pay/addCoinAddress",type:"post",data:{currency_id:project.id,address:$("#senInput1").val()},success:function(t){t.status,alert(t.info)}})}),$("#adp2").on("click",function(){$.ajax({url:"/Home/pay/addCoinAddress",type:"post",data:{currency_id:project.id,address:$("#senInput2").val()},success:function(t){t.status,alert(t.info)}})}),$(".deld1").on("click",function(){$(".fss1").css("display","none"),$(".shouczs1").css("display","block")}),$(".deld2").on("click",function(){$(".fss2").css("display","none"),$(".shouczs2").css("display","block")})},project.selectLi=function(){$(".iconft").on("click",function(){"block"==$(".ulisadd").css("display")?$(".ulisadd").css("display","none"):$(".ulisadd").css("display","block")})},project.getCoinAdd=function(){$(".shouczz1").on("click",function(){if(1!=$("#hidden").val())return window.location.href="/Home/index/login.html",!1;$.ajax({url:"/Home/pay/bpay",type:"get",data:{currency_id:1},success:function(t){1==t.status&&($("#qrcode1").html(""),jQuery("#qrcode1").qrcode({render:"canvas",width:120,height:120,text:t.data}),$("#copVal1").val(t.data),$(".shouczs1").css("display","none"),$(".czz1").css("display","block"))}})}),$(".shouczz2").on("click",function(){if(1!=$("#hidden").val())return window.location.href="/Home/index/login.html",!1;$.ajax({url:"/Home/pay/bpay",type:"get",data:{currency_id:project.id},success:function(t){1==t.status&&($("#qrcode2").html(""),jQuery("#qrcode2").qrcode({render:"canvas",width:120,height:120,text:t.data}),$("#copVal2").val(t.data),$(".shouczs2").css("display","none"),$(".czz2").css("display","block"))}})}),$(".delc1").on("click",function(){$(".shouczs1").css("display","block"),$(".czz1").css("display","none")}),$(".delc2").on("click",function(){$(".shouczs2").css("display","block"),$(".czz2").css("display","none")})},project.copys=function(){$(".copys").on("click",function(){try{$(this).prev().select(),document.execCommand("Copy"),alert("已复制好，可贴粘。")}catch(t){alert("复制失败，请手动复制。")}})},project.delOrder=function(){$("#abut5").on("click",".icondel",function(){var t=$(this).attr("data-orid"),e=$(this);$.ajax({url:"/Home/trade/cancel",type:"get",data:{order_id:t},success:function(t){1==t.status&&(e.parents("ul").remove(),alert(t.info))}})})},project.copyli=function(){$("#abut1").on("click","ul",function(){$("#buy1pri").val($(this).find("li").eq(0).html()),$("#buy1num").val($(this).find("li").eq(1).html()),$("#buy3pri").val($(this).find("li").eq(0).html()),$("#buy3num").val($(this).find("li").eq(1).html()),$("#buy1sum").val(objs.fds($("#buy1pri").val(),$("#buy1num").val())),$("#buy3sum").val(objs.fds($("#buy3pri").val(),$("#buy3num").val())),$("#buy2pri").val($(this).find("li").eq(0).html()),$("#buy2num").val($(this).find("li").eq(1).html()),$("#buy4pri").val($(this).find("li").eq(0).html()),$("#buy4num").val($(this).find("li").eq(1).html()),$("#buy2sum").val(objs.fds($("#buy2pri").val(),$("#buy2num").val())),$("#buy4sum").val(objs.fds($("#buy4pri").val(),$("#buy4num").val()))}),$("#abut2").on("click","ul",function(){$("#buy1pri").val($(this).find("li").eq(2).html()),$("#buy1num").val($(this).find("li").eq(1).html()),$("#buy3pri").val($(this).find("li").eq(2).html()),$("#buy3num").val($(this).find("li").eq(1).html()),$("#buy1sum").val(objs.fds($("#buy1pri").val(),$("#buy1num").val())),$("#buy3sum").val(objs.fds($("#buy3pri").val(),$("#buy3num").val())),$("#buy2pri").val($(this).find("li").eq(2).html()),$("#buy2num").val($(this).find("li").eq(1).html()),$("#buy4pri").val($(this).find("li").eq(2).html()),$("#buy4num").val($(this).find("li").eq(1).html()),$("#buy2sum").val(objs.fds($("#buy2pri").val(),$("#buy2num").val())),$("#buy4sum").val(objs.fds($("#buy4pri").val(),$("#buy4num").val()))})},project.meOrders=function(){$.ajax({url:"/Home/trade/myOrders",type:"get",data:{currency_id:project.id},success:function(t){for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00A63F">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].sum+'</li><li><svg class="icon icondel" aria-hidden="true" data-orid="'+t.data[o].orders_id+'"><use xlink:href="#icon-guanbi"></use></svg></li></ul>';$("#abut5>div").eq(0).append(e),$("#abut5").nanoScroller()}})},project.meCount=function(){$.ajax({url:"/Home/trade/myTradeRecords",type:"get",data:{currency_id:project.id},success:function(t){for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00A63F">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].money+"</li></ul>";$("#abut6>div").eq(0).append(e),$("#abut6").nanoScroller()}})},project.sell=function(){$(".but2").on("click",function(){return 0==$("#hidden").val()?(alert($("#mess").attr("data-login")),!1):$("#buy2pri").val()<=0||$("#buy2num").val()<=0?(alert($("#mess").attr("data-error")),!1):($(".ud2").css("display","block"),$(".va21").val($("#buy2pri").val()),$(".va22").val($("#buy2num").val()),void $.ajax({url:"/Home/trade/tradePrepare",type:"post",data:{price:$("#buy2pri").val(),num:$("#buy2num").val(),fee:$(".fees").eq(1).html(),type:2},success:function(t){$(".va23").val(t.fee),$(".va24").val(t.true_total),$(".va25").val(t.total)}}))}),$(".mrbot2").on("click",function(){if($("#buy4pri").val()<=0||$("#buy4num").val()<=0)return alert($("#mess").attr("data-error")),!1;$(".ud2").css("display","block"),$(".va21").val($("#buy4pri").val()),$(".va22").val($("#buy4num").val()),$.ajax({url:"/Home/trade/tradePrepare",type:"post",data:{price:$("#buy4pri").val(),num:$("#buy4num").val(),fee:$(".fees").eq(1).html(),type:2},success:function(t){$(".va23").val(t.fee),$(".va24").val(t.true_total),$(".va25").val(t.total)}})}),$(".buyBut2").on("click",function(){var t=null,e=$(this);e.attr("disabled","true"),t=$(window).width()<=600?$("#form4").serialize():$("#form2").serialize(),$.ajax({url:"/Home/trade/sell",type:"post",data:t,success:function(t){1==t.status?(toastr.success($("#mess").attr("data-se1")+$("#buy1num").val()+" "+project.coin+" "+$("#mess").attr("data-se2")+$("#buy1pri").val()+" BTC",$("#mess").attr("data-ok")),$(".ud2").css("display","none"),$(".getbtc").html(t.data.balance.user_btc+" BTC"),$(".getNewCoin").html(t.data.balance.user_money+" "+project.coin)):alert(t.info),e.removeAttr("disabled")}})}),$("#buy2pri").on("change",function(){$("#buy2sum").val(objs.fds($("#buy2pri").val(),$("#buy2num").val()))}),$("#buy2num").on("change",function(){$("#buy2sum").val(objs.fds($("#buy2pri").val(),$("#buy2num").val()))}),$("#buy4pri").on("change",function(){$("#buy4sum").val(objs.fds($("#buy4pri").val(),$("#buy4num").val()))}),$("#buy4num").on("change",function(){$("#buy4sum").val(objs.fds($("#buy4pri").val(),$("#buy4num").val()))})},project.buy=function(){$(".but1").on("click",function(){return 0==$("#hidden").val()?(alert($("#mess").attr("data-login")),!1):$("#buy1pri").val()<=0||$("#buy1num").val()<=0?(alert($("#mess").attr("data-error")),!1):($(".ud1").css("display","block"),$(".va11").val($("#buy1pri").val()),$(".va12").val($("#buy1num").val()),void $.ajax({url:"/Home/trade/tradePrepare",type:"post",data:{price:$("#buy1pri").val(),num:$("#buy1num").val(),type:1,fee:$(".fees").eq(0).html()},success:function(t){$(".va13").val(t.fee),$(".va14").val(t.true_total),$(".va15").val(t.total)}}))}),$(".buyBut1").on("click",function(){var t=null,e=$(this);e.attr("disabled","true"),t=$(window).width()<=600?$("#form3").serialize():$("#form1").serialize(),$.ajax({url:"/Home/trade/buy",type:"post",data:t,success:function(t){1==t.status?(toastr.success($("#mess").attr("data-se1")+$("#buy1num").val()+" "+project.coin+" "+$("#mess").attr("data-se2")+$("#buy1pri").val()+" BTC",$("#mess").attr("data-ok")),$(".ud1").css("display","none"),$(".getbtc").html(t.data.balance.user_btc+" BTC"),$(".getNewCoin").html(t.data.balance.user_money+" "+project.coin)):alert(t.info),e.removeAttr("disabled")}})}),$(".mrbot1").on("click",function(){if($("#buy3pri").val()<=0||$("#buy3num").val()<=0)return alert($("#mess").attr("data-error")),!1;$(".ud1").css("display","block"),$(".va11").val($("#buy3pri").val()),$(".va12").val($("#buy3num").val()),$(".va13").val($(".fees").eq(0).html()),$(".va14").val($("#buy3sum").val()),$.ajax({url:"/Home/trade/tradePrepare",type:"post",data:{price:$("#buy3pri").val(),num:$("#buy3num").val(),fee:$(".fees").eq(0).html(),type:1},success:function(t){$(".va13").val(t.fee),$(".va14").val(t.true_total),$(".va15").val(t.total)}})}),$("#buy1pri").on("change",function(){$("#buy1sum").val(objs.fds($("#buy1pri").val(),$("#buy1num").val()))}),$("#buy1num").on("change",function(){$("#buy1sum").val(objs.fds($("#buy1pri").val(),$("#buy1num").val()))}),$("#buy3pri").on("change",function(){$("#buy3sum").val(objs.fds($("#buy3pri").val(),$("#buy3num").val()))}),$("#buy3num").on("change",function(){$("#buy3sum").val(objs.fds($("#buy3pri").val(),$("#buy3num").val()))}),$("#buy1sum").on("change",function(){if(parseFloat($("#buy1num").val())<=0)return!1;$.ajax({url:"/Home/Trade/getTotal",type:"post",data:{trade_num:$("#buy1num").val(),trade_total:$("#buy1sum").val()},success:function(t){$("#buy1pri").val(t.data)}})}),$("#buy2sum").on("change",function(){if(parseFloat($("#buy2num").val())<=0)return!1;$.ajax({url:"/Home/Trade/getTotal",type:"post",data:{trade_num:$("#buy2num").val(),trade_total:$("#buy2sum").val()},success:function(t){$("#buy2pri").val(t.data)}})}),$("#buy3sum").on("change",function(){if(parseFloat($("#buy3num").val())<=0)return!1;$.ajax({url:"/Home/Trade/getTotal",type:"post",data:{trade_num:$("#buy3num").val(),trade_total:$("#buy3sum").val()},success:function(t){$("#buy3pri").val(t.data)}})}),$("#buy4sum").on("change",function(){if(parseFloat($("#buy4num").val())<=0)return!1;$.ajax({url:"/Home/Trade/getTotal",type:"post",data:{trade_num:$("#buy4num").val(),trade_total:$("#buy4sum").val()},success:function(t){$("#buy4pri").val(t.data)}})})},project.setInter=function(){setInterval(function(){$.ajax({url:"/Home/trade/tradeRecords",dataType:"json",type:"get",data:{currency_id:project.id,p:0},success:function(t){if(0==t.count)return!1;if(t.count>project.CordLengh){for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00a63f">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].money+"</li></ul>";$("#abut3>div").eq(0).html(e),project.record=0,project.CordLengh=t.count,$("#abut3").nanoScroller()}}}),$.ajax({url:"/Home/trade/orderRecords",type:"get",dataType:"json",data:{currency_id:project.id,type:"buy",p:0},success:function(t){$(".os1").html(t.sum);for(var e="",a=0;a<t.data.length;a++)e+="<ul><li>"+t.data[a].total+"</li><li>"+t.data[a].num+"</li><li>"+t.data[a].price+"</li></ul>";$("#abut2>div").eq(0).html(e),$("#abut2").nanoScroller()}}),$.ajax({url:"/Home/trade/orderRecords",type:"get",dataType:"json",data:{currency_id:project.id,type:"sell",p:0},success:function(t){$(".os2").html(t.sum);for(var e="",a=0;a<t.data.length;a++)e+="<ul><li>"+t.data[a].price+"</li><li>"+t.data[a].num+"</li><li>"+t.data[a].total+"</li></ul>";$("#abut1>div").eq(0).html(e),$("#abut1").nanoScroller()}}),$.ajax({url:"/Home/trade/myOrders",type:"get",data:{currency_id:project.id},success:function(t){for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00A63F">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].sum+'</li><li><svg class="icon icondel" aria-hidden="true" data-orid="'+t.data[o].orders_id+'"><use xlink:href="#icon-guanbi"></use></svg></li></ul>';$("#abut5>div").eq(0).html(e),$("#abut5").nanoScroller()}})},2e3)},project.scrooBot=function(){$("#abut3").on("scrollend",function(t){if(project.CordLengh<15||project.CordLengh/15<=project.record-1)return!1;$.ajax({url:"/Home/trade/tradeRecords",dataType:"json",type:"get",data:{currency_id:project.id,p:project.record},success:function(t){if(0==t.count)return!1;for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00a63f">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].money+"</li></ul>";$("#abut3>div").eq(0).append(e),project.record++,project.CordLengh=t.count,$("#abut3").nanoScroller()}})})},project.initData=function(){toastr.options={closeButton:!0,progressBar:!0,debug:!0,timeOut:3e3,extendedTimeOut:3e3};var t=objs.getUrl();void 0!=t&&(project.coin=t.coin,project.ago=t.ago,project.id=t.id),$(".setcoin1").val(project.id),$(".coinName").html(project.coin),$.ajax({url:"/Home/orders/index",type:"get",dataType:"json",data:{currency_id:project.id,market:project.ago},success:function(t){$(".upwindow").eq(0).html(t.latest_price),t.up_or_down>=0?$(".upwindow").eq(1).html(t.up_or_down+" %"):$(".upwindow").eq(1).html(t.up_or_down+" %").removeClass("timeTo").css("color","red"),$(".upwindow").eq(2).html(t.max_price),$(".upwindow").eq(3).html(t.min_price),$(".upwindow").eq(4).html(t.currency_trades)}}),$.ajax({url:"/Home/trade/beforeTrade",type:"get",data:{currency_id:project.id},success:function(t){$(".fees").eq(0).html(t.currency_buy_fee),$(".fees").eq(1).html(t.currency_sell_fee),$(".fees").eq(2).html(t.currency_buy_fee),$(".fees").eq(3).html(t.currency_sell_fee),$(".upfees").val(t.currency_buy_fee),$(".upfees2").val(t.currency_sell_fee)}}),$.ajax({url:"/Home/trade/tradeRecords",dataType:"json",type:"get",data:{currency_id:project.id,p:project.record},success:function(t){if(0==t.count)return!1;for(var e="",a="",o=0;o<t.data.length;o++)a="buy"==t.data[o].type?'<li style="color:#00a63f">'+t.data[o].price+"</li>":'<li style="color:#f03838">'+t.data[o].price+"</li>",e+="<ul><li>"+t.data[o].add_time+"</li>"+a+"<li>"+t.data[o].num+"</li><li>"+t.data[o].money+"</li></ul>";$("#abut3>div").eq(0).append(e),project.record++,project.CordLengh=t.count,$("#abut3").nanoScroller()}}),$.ajax({url:"/Home/trade/orderRecords",type:"get",dataType:"json",data:{currency_id:project.id,type:"buy",p:project.m},success:function(t){if($(".os1").html(t.sum),0==t.count)return!1;for(var e="",a=0;a<t.data.length;a++)e+="<ul><li>"+t.data[a].total+"</li><li>"+t.data[a].num+"</li><li>"+t.data[a].price+"</li></ul>";$("#abut2>div").eq(0).append(e),project.m++,project.mLength=t.count,$("#abut2").nanoScroller()}}),$.ajax({url:"/Home/trade/orderRecords",type:"get",dataType:"json",data:{currency_id:project.id,type:"sell",p:project.mm},success:function(t){if($(".os2").html(t.sum),0==t.count)return!1;for(var e="",a=0;a<t.data.length;a++)e+="<ul><li>"+t.data[a].price+"</li><li>"+t.data[a].num+"</li><li>"+t.data[a].total+"</li></ul>";$("#abut1>div").eq(0).append(e),project.mm++,project.mmLength=t.count,$("#abut1").nanoScroller()}});var e=parseInt($(".lpt").eq(0).css("width"))-45;$(".ulisadd").css("width",e+"px"),0==localStorage.getItem("ofs")||null==localStorage.getItem("ofs")?($("#container").css("display","block"),$("#container1").css("display","none")):($("#container").css("display","none"),$("#container1").css("display","block"))},project.selectCoins=function(){$(".btcUsd").on("click",".nano ul",function(){window.location.href="/Home/index/index?coin="+$(this).attr("data-coin")+"&ago="+$(this).parent().attr("data-ago")+"&id="+$(this).attr("data-id")})},project.getCoins=function(){$.ajax({url:"/Home/orders/CurrencyInfo",type:"post",data:{p:project.coinCount},success:function(t){for(var e="",a="",o=0;o<t.data.length;o++)a=t.data[o].up_or_down>=0?'<li style="color:#00A63F">'+t.data[o].up_or_down+" %</li>":'<li style="color:#f03838">'+t.data[o].up_or_down+" %</li>",e+="<ul data-coin="+t.data[o].currency_mark+" data-id="+t.data[o].currency_id+' title="单击切换币种"> <li>'+t.data[o].currency_mark+"</li><li>"+t.data[o].latest_price+"</li><li>"+t.data[o].one_day_trades+"</li>"+a+"<li>"+t.data[o].currency_name+"</li></ul>";$("#abut>div").append(e),$("#abut4>div").append(e),project.coinCount++}})},project.adNotClick=function(){$(".suerDd").find("span").each(function(){$(this).on("click",function(){$(this).addClass("tthis").siblings().removeClass("tthis"),$(".user_show>div").eq($(this).index()).css("display","block").siblings().css("display","none")})}),$(".h3active").find("span").each(function(){$(this).on("click",function(){$(this).addClass("tthis").siblings().removeClass("tthis"),$(".btcUsd>div").eq($(this).index()).css("display","block").siblings().css("display","none")})})},project.sousuo=function(){$(".soso").on("click",function(){$(this).css("width","180px")}),$(".soso input").on("blur",function(){$(".soso").css("width","70px")}),$("#souSeach").on("keydown",function(t){13==t.which&&$(this).val().length>0&&$.ajax({url:"/Home/orders/CurrencyInfo",type:"post",data:{p:0,search:$(this).val()},success:function(t){for(var e="",a=0;a<t.data.length;a++)e+="<ul data-coin="+t.data[a].currency_mark+" data-id="+t.data[a].currency_id+' title="单击切换币种"> <li>'+t.data[a].currency_mark+"</li><li>"+t.data[a].latest_price+"</li><li>"+t.data[a].one_day_trades+"</li><li>"+t.data[a].up_or_down+"</li><li>"+t.data[a].currency_name+"</li></ul>";$("#abut>div").html(e),$("#abut4>div").html(e),project.coinCount++}})})},project.mmClick=function(){$(".pyld").find("span").each(function(){$(this).on("click",function(){$(this).addClass("ttthis").siblings().removeClass("ttthis"),0==$(this).index()?$(this).css("background","#00A63F").siblings().css("background","transparent"):$(this).css("background","#f03838").siblings().css("background","transparent"),$(".mmp>div").eq($(this).index()).css("display","block").siblings().css("display","none")})})},project.upHeight=function(){$(".iconxia").on("click",function(){parseInt($(this).parents(".lpx").css("height"))<=36?($(this).parents(".lpx").css("height","auto"),$(this).css("transform","rotate(0deg)")):($(this).parents(".lpx").css("height","36px"),$(this).css("transform","rotate(180deg)"))})},project.highch=function(){Highcharts.setOptions(Highcharts.one);var t={zh_CN:{title:{text:project.coin+"历史股价"},series:[{name:project.coin},{name:"成交量"}],yAxis:[{title:{text:""}},{title:{text:""}}]},en:{title:{text:"平安银行历史股价"},series:[{name:"sz00001"},{name:"volume"}],yAxis:[{title:{text:"price"}},{title:{text:"volume"}}]}},e=null;$.getJSON("/Home/orders/getOrdersKline?currency_id="+project.id,function(a){if(1!==a.code)return alert("读取股票数据失败！"),!1;for(var o=0;o<a.data.length;o++)for(var s=0;s<a.data[o].length;s++)a.data[o][s]=Number(a.data[o][s]);a=a.data;var n=[],i=[],l=a.length,r=[["week",[1]],["month",[1,2,3,4,6]],["day",[1]]],o=0;for(o;o<l;o+=1)n.push([a[o][0],a[o][1],a[o][2],a[o][3],a[o][4]]),i.push([a[o][0],a[o][5]]);if(e=Highcharts.stockChart("container",{chart:{lang:"zh_CN"},rangeSelector:{selected:1,inputDateFormat:"%Y-%m-%d",inputEnabled:!1},tooltip:{split:!1,shared:!0},yAxis:[{labels:{align:"right",x:-3},title:{text:"股价"},height:"75%",resize:{enabled:!0}},{labels:{align:"right",x:-3},title:{text:"成交量"},top:"75%",height:"25%",offset:0}],series:[{type:"candlestick",name:project.coin,color:"green",lineColor:"green",upColor:"red",upLineColor:"red",tooltip:{},navigatorOptions:{color:Highcharts.getOptions().colors[0]},data:n,dataGrouping:{units:r,enabled:!1}},{type:"column",data:i,yAxis:1,dataGrouping:{units:r}}]},function(e){e.setLang("zh_CN",t.zh_CN,!1)}),Highcharts.setOptions(Highcharts.to),chart1=Highcharts.stockChart("container1",{chart:{lang:"zh_CN"},rangeSelector:{selected:1,inputDateFormat:"%Y-%m-%d",inputEnabled:!1},tooltip:{split:!1,shared:!0},yAxis:[{labels:{align:"right",x:-3},title:{text:"股价"},height:"75%",resize:{enabled:!1}},{labels:{align:"right",x:-3},title:{text:"成交量"},top:"75%",height:"25%",offset:0}],series:[{type:"candlestick",name:project.coin,color:"green",lineColor:"green",upColor:"red",upLineColor:"red",tooltip:{},navigatorOptions:{color:Highcharts.getOptions().colors[0]},data:n,dataGrouping:{units:r}},{type:"column",data:i,yAxis:1,dataGrouping:{units:r}}]},function(e){e.setLang("zh_CN",t.zh_CN,!1)}),1==$("#dhi").attr("data-id")){var c=$(".divLan>button").eq(0).data("lang");e.setLang(c,t[c]),chart1.setLang(c,t[c])}else{var c=$(".divLan>button").eq(1).data("lang");e.setLang(c,t[c]),chart1.setLang(c,t[c])}})},project.init();