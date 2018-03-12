var project = {}
var objs = new obje()
var arrColor=['#F7931A','#3EC8C8','#154BE9','#51B0DD','#52c2e2','#52c2e2'];
var arrBGColor = ['#FBE9D3','#A6E4E3','#9AB1F2','#AED9EC','#d1eef8','#d1eef8'];
var arrOPTAColor = ["#FBF3E8","#C8ECE9","#D8DEF2","#D1E7EF","#D1E7EF","#D1E7EF"];
project.init=function(){
        //获取数据
    this.getdata()
    // this.ech()
    //发送注册请求
    this.send()
    //获取最新公告
    this.ggs()
    //获取推荐币种
    this.getCOin()
    //设置币种走势图
    this.getImg =[
                    'https://files.coinmarketcap.com/generated/sparklines/2306.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2027.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2243.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2034.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2263.png',
                    'https://files.coinmarketcap.com/generated/sparklines/1954.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2297.png',
                    'https://files.coinmarketcap.com/generated/sparklines/2273.png',
                    // 'https://files.coinmarketcap.com/generated/sparklines/2297.png',
                    // 'https://files.coinmarketcap.com/generated/sparklines/2273.png',
                ]
    //滚动公告
    // this.roll()
}
project.roll = function () {
    var txt = $('.gulis').html()
    $('.gulis').append(txt);
    var time=null;
    var ss = 3000;
    var anima = 500
    var count = 1;
    time = setInterval(function(){
        $('.gulis').animate({top:(-(count*36))+'px'},anima);
        if(count==3){
            $('.gulis').css('top',0);
             $('.gulis').animate({top:'0px'},1);
            count=1
        }else{
            count++;
        }
    },ss)
}
project.getCOin = function () {
    $.ajax({
        url:'/Home/index/getCoinSort',
        type:'get', 
        success:function(res){
            var txt = '';
            cor =null;
            for(var i=0;i<res.data.length;i++){
                cor = res.data[i].changePercent>0?"#00a63f":'red';
                txt += '<ul class="u24">'+
                    '<li><div style="display:inline-block;width:40px;text-align:left"><img src="'+res.data[i].logo+'"  style="width:28px"></div><div style="display:inline-block;width:60px;text-align:left">'+res.data[i].MarketName+'</div></li>'+
                    '<li>'+res.data[i].Last+'</li>'+
                    '<li>'+res.data[i].High+'</li>'+
                    '<li>'+res.data[i].Low+'</li>'+
                    '<li>'+parseFloat(res.data[i].Volume)+'</li>'+
                    '<li style="color:'+cor+'">'+res.data[i].changePercent+'%</li>'+
                    '<li><div id="ec'+i+'"><img src="'+project.getImg[i]+'"></div></li>'+
                    '<li>'+(i+1)+'</li>'+
                '</ul>';
            }
            $('.uls').append(txt);
        }
    })
}
project.ggs = function() {
    $.ajax({
        url:"/Home/art/getNotice",
        type:"get",
        data:{
            offset:3,
        },
        success:function(req){
            for(var i=0;i<req.data.length;i++){
               $('.ggs').eq(i).html(req.data[i].title);
               var index = req.data[i].position_id==102?0:1;
               $('.ggs').eq(i).attr('data-src','/home/single/details.html?status=1&uid='+req.data[i].article_id+'&st='+index)
            }
            project.roll()
        }
    })
}
project.getdata = function (){
    $('.swiper-slide span').css('width',$(window).width()+'px');
      var swiper = new Swiper('#lb1', {
      pagination: {
        el: '#lb1 .swiper-pagination',
         clickable: true,
      },
          loop : true,
          slidesPerView: 1,
          autoplay:true
        });
    $.ajax({
        url:'/Home/index/website',
        type:'post',
        dataType:'json',
        success:function(req){
            var arr1=[]
            var arr2=[]
            for(var i=0;i<req.data.length;i++){
                var txt = '<li>'+
                ' <img src="'+req.data[i].currency_logo+'"><p>'+req.data[i].currency_mark+'</p>'+
                '<div class="pricete">'+
                '   <span>'+req.data[i].latest_price+' BTC </span>'+
                '   <span class="jggreen">'+parseFloat(req.data[i].up_or_down)+'%</span>'+
                '  </div><div class="echw"><div  id="echw'+i+'"></div>'+
                ' </li>';
                // var txts = ' <div class="swiper-slide">'+
                // '<p>'+req.data[i].currency_mark+'/USDT ('+parseFloat(req.data[i].latest_price)+')</p>'+
                // ' <div>'+
                // '  <span>24小时量:'+parseInt(req.data[i].one_day_trades)+'</span>'+
                // '   <span class="jggreen">('+parseFloat(req.data[i].up_or_down)+'%)</span>'+
                // ' </div>'
                    // $('#swipreList').append(txts)
                    $('#swipreList>div').eq(0).addClass('selectLi');
                    $('#iconlists').append(txt)

                    var arrs1=[]
                    var arrs2=[]
                for(var j=0;j<req.data[i].every_hour_trades.length;j++){
                    arrs1.push(req.data[i].every_hour_trades[j].hour)
                    arrs2.push(req.data[i].every_hour_trades[j].price)
                }
                if(arr1.length<24){
                    for(var b = arrs1.length;b<24;b++){
                        // arrs1.unshift((Math.random()+1)*10)
                        // arrs2.unshift((Math.random()+1)*10)
                        arrs1.unshift(0)
                        arrs2.unshift(0)
                    }
                }
                arr1.push(arrs1)
                arr2.push(arrs2)
            }
            $('#iconlists>li').eq(0).addClass('selectLi');
            //设施图表隐藏
            project.ech(arr1,arr2);
                  // var swiper = new Swiper('#lb2', {
                  //       slidesPerView: 1,
                  //       spaceBetween: 0,
                  //       freeMode: true,
                  //       pagination: {
                  //         el: '#lb2 .swiper-pagination',
                  //         clickable: true,
                  //     },
                  //   });
            $('.echs>div').eq(1).css('display','none')
            $('.echs>div').eq(2).css('display','none')
        }
    })
}
project.send = function() {
    $('#sendEmail').on('click',function(){
      $('#sendEmail').attr("disabled",true);
      var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/; 
      if(!reg.test($('#email').val())){
        alert($('#mess').attr('data-error'))
        return false
    }
    $.ajax({
        url:'/Home/Reg/sendEmail',
        type:'post',
        data:{
            email:$('#email').val()
        },
        success:function(req){
            if(req.status==1){
                alert(req.info)
            }else{
             $('#sendEmail').attr("disabled",false);
             alert(req.info)
         }
     }
 })
})
}

project.ech = function(arr1,arr2){
    for(var i=0;i<arr1.length;i++){
    var intId= i;
    var myChart = echarts.init(document.getElementById('echw'+intId));
    $("#echw"+intId).css( 'width', $("#echw"+intId).width() );
         var option = {
                color : [ arrColor[intId] ],
                calculable : false,
                grid : {
                    left : '0%',
                    right : '0%',
                    bottom : '0%',
                    containLabel : false
                },
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'line'        // 默认为直线，可选为：'line' | 'shadow'
                    },
                    formatter:function(e){
                        return e[0].data
                    }
                },
                xAxis :
                    {
                        show:false,
                        type : 'category',
                        boundaryGap : false,
                        axisLine : {
                            show : false,
                            onZero : false
                        },
                        data :arr2[intId]
                    }
                ,
                yAxis :{
                    show:false,
                    type : 'value',
                    axisLine : {
                        show : false,
                        onZero : false
                    },
                },
                series : [
                    {
                        name:'成交',
                        type:'line',
                        smooth:true,
                        symbol:'pin',
                        symbolOffset:[0,'2px'],
                        itemStyle: {normal: {areaStyle: {type: 'default'}}},
                        symbolSize : 3,
                        areaStyle : {
                            normal: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: arrBGColor[intId]
                                }, {
                                    offset: 1,
                                    color: arrOPTAColor[intId]
                                }])
                            }
                        },
                        data:arr1[intId]
                    }
                ]
            };
            myChart.setOption(option);
            myChart.on('click',function(eee){

            })
    }
}

project.init()