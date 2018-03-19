var project={}

project.init = function(){
	//初始化设置高度
	this.setHei();
		//倒计时
	this.djs()
    //改写alert
    this.alert()
    //登录
    this.login()
    //隐藏
    this.hide()
    //初始化函数
    this.main()
    //注册
    this.sig()
    //忘记密码
    this.resetPas()
    //搜集邮箱
    this.em()
    //跳出计划
    this.one()
}
project.one = function () {
    $('.sneds').on('click',function () {
        $('.shou-ps>div').eq(0).css('display','block').siblings().css('display','none');
        $('.shouS').css('display','block');
    })
    $('.rcion').on('click',function () {
        if($(this).attr('data-status')==0){
            $(this).attr('src')
        }
    })
}
project.em = function () {
    $('.Join').on('click',function () {
        $.ajax({
            url:'/email/join',
            type:'post',
            data:{
                email:$('#em1').val()
            },
            success:function (res) {
                alert(res.msg)
            }
        })
    })
}
project.resetPas =  function () {
    $('#reset').on('click',function () {
        var datas = $('#forget').serialize();
        $.ajax({
            url:'/user/forget',
            type:'post',
            data:datas,
            success:function (res) {
                if (res.success==false){
                    alert(res.msg)
                }else{
                    alert(res.msg)
                    $('.shou-wrap>form').eq(0).css('display','block').siblings().css('display','none');
                    $('.shouD').css('display','block')
                }
            }
        })
    })
    $('.noCode').on('click',function(){
        if($('#email2').val().length<=0){
            alert($('#mess').attr('data-erEmial'))
            return false
        }else{
            $.ajax({
                url:'/user/verify',
                type:'get',
                data:{
                    type:1,
                    email:$('#email2').val()
                },
                success:function (res) {
                    alert(res.msg)
                }
            })
        }
    })
}
project.alert = function () {
    window.alert=function (val) {
        if(window.alertTime){
            clearTimeout(window.alertTime)
        }else{
            window.alertTime=null
        }
        var div=null
        if(document.getElementById('alert1')==null ||document.getElementById('alert1')=='undefined'){
            div = document.createElement('div');
            div.setAttribute('style',"height:30px;line-height:30px;background:rgba(0,0,0,.5);position:fixed;top:0px;left:0;color:#fff;padding:10px;opacity:1;border-radius:5px;z-index:999999999999");
            div.setAttribute('id','alert1');
            div.innerHTML=val;
            document.body.appendChild(div);
            var widht=0;
            div=document.getElementById('alert1');
            if(div.currentStyle)
            {
                width=div.currentStyle['width'];
            }
            else
            {
                width=getComputedStyle(div, false)['width'];
            }
            width=parseInt(width)/2;
            div.style.left='50%';
            div.style.top='50%';
            div.style.marginTop="-20px";
            div.style.marginLeft='-'+width+'px';

            window.alertTime = setTimeout(function () {
                div=document.getElementById('alert1');
                div.parentNode.removeChild(div);
            },2000)
        }else{
            div=document.getElementById('alert1');
            div.parentNode.removeChild(div);
            div = document.createElement('div');
            div.setAttribute('style',"height:30px;line-height:30px;background:rgba(0,0,0,.5);position:fixed;top:0px;left:0;color:#fff;padding:10px;opacity:1;border-radius:5px");
            div.setAttribute('id','alert1');
            div.innerHTML=val;
            document.body.appendChild(div);
            var widht=0;
            div=document.getElementById('alert1');
            if(div.currentStyle)
            {
                widht=div.currentStyle['width'];
            }
            else
            {
                width=getComputedStyle(div, false)['width'];
            }
            width=parseInt(width)/2;
            div.style.left='50%';
            div.style.top='50%';
            div.style.marginTop="-20px";
            div.style.marginLeft='-'+width+'px';

            window.alertTime= setTimeout(function () {
                div=document.getElementById('alert1');
                div.parentNode.removeChild(div);

            },2000)
        }
    }
}
project.sig = function () {
    $('.but2').on('click',function(){
        var datas = $('#sig').serialize();
        $.ajax({
            url:'/user/register',
            type:'post',
            data:datas,
            success:function (res) {
                if (res.success==false){
                    alert(res.msg)
                }else{
                    alert(res.msg)
                    $('.shou-wrap>form').eq(0).css('display','block').siblings().css('display','none');
                    $('.shouD').css('display','block')
                }
            }
        })
    })
    $('.getCode').on('click',function(){

        if($('#email1').val().length<=0){
            alert($('#mess').attr('data-erEmial'))
            return false
        }else{
            $.ajax({
                url:'/user/verify',
                type:'get',
                data:{
                    type:0,
                    email:$('#email1').val()
                },
                success:function (res) {
                    alert(res.msg)
                }
            })
        }
    })
}
project.main= function(){
    project.t1()
}
project.t1 = function(){
    $('.t1').on('click',function(){
        $('.shouD .shou-wrap>form').eq(1).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
    $('.t2').on('click',function(){
        $('.shouD .shou-wrap>form').eq(2).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
    $('.t3').on('click',function(){
        $('.shouD .shou-wrap>form').eq(0).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
    $('.t4').on('click',function(){
        $('.shouD .shou-wrap>form').eq(0).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
}
project.hide = function () {
    $('.zz').on('click',function(){
        $('.shouD').css('display','none')
    })
}
project.login = function () {
    $('.regi').on('click',function(){
        $('.shou-wrap>form').eq(1).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
    $('.logi').on('click',function(){
        $('.shou-wrap>form').eq(0).css('display','block').siblings().css('display','none');
        $('.shouD').css('display','block')
    })
    $('.but1').on('click',function () {
        var datas = $('#login').serialize();
        $.ajax({
            url:'/user/login',
            type:'post',
            data:datas,
            success:function (res) {
                if (res.success==false){
                    alert(res.msg)
                }else{
                    window.location.href='/index'
                }
            }
        })
    })
}
project.djs = function () {
	var sj = 108000;
		var d =parseInt(sj/3600/24);
		var sj1 = sj-d*3600*24;
		var h = parseInt(sj1/3600);
		var sj2 = sj1-h*3600;
		var m = parseInt(sj2/60);
		sj--
		$('.time>li').eq(0).html(project.timer(d));
		$('.time>li').eq(2).html(project.timer(h));
		$('.time>li').eq(4).html(project.timer(m));
	setInterval(function(){
		var d =parseInt(sj/3600/24);
		var sj1 = sj-d*3600*24;
		var h = parseInt(sj1/3600);
		var sj2 = sj1-h*3600;
		var m = parseInt(sj2/60);
		sj--
		$('.time>li').eq(0).html(project.timer(d));
		$('.time>li').eq(2).html(project.timer(h));
		$('.time>li').eq(4).html(project.timer(m));
	},1000)
}
project.timer = function (data) {
    return parseInt(data/10)>0?data:'0'+data;
}
project.setHei = function(){
	$('.one').height($(window).height()+'px');
}
project.init()