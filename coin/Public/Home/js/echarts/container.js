// var myChart = echarts.init(document.getElementById('container'));
// function splitData(rawData) {
//     var categoryData = [];
//     var values = [];
//     var volumns = [];
//     for (var i = 0; i < rawData.length; i++) {
//         categoryData.push(rawData[i].splice(0, 1)[0]);
//         values.push(rawData[i]);
//         volumns.push(rawData[i][4]);
//     }
//     return {
//         categoryData: categoryData,
//         values: values,
//         volumns: volumns
//     };
// }
// function calculateMA(dayCount, data) {
//     var result = [];
//     for (var i = 0, len = data.values.length; i < len; i++) {
//         if (i < dayCount) {
//             result.push('-');
//             continue;
//         }
//         var sum = 0;
//         for (var j = 0; j < dayCount; j++) {
//             sum += data.values[i - j][1];
//         }
//         result.push(+(sum / dayCount).toFixed(3));
//     }
//     return result;
// }
//
// $.get('/stock-DJI.json', function (rawData) {
//     var data = splitData(rawData);
//     myChart.setOption(option = {
//         backgroundColor: '#eee',
//         animation: false,
//         legend: {
//             bottom: 10,
//             left: 'center',
//             data: ['参考数据', 'MA10', 'MA20', 'MA30','MA5'],
//             selected: {
//                 'MA10':false,
//                 '参考数据':true,
//                 'MA20':false,
//                 'MA30':false,
//                 'MA5':false
//             }
//             },
//         tooltip: {
//             trigger: 'axis',
//             axisPointer: {
//                 type: 'cross'
//             },
//             backgroundColor: 'rgba(245, 245, 245, 0.8)',
//             borderWidth: 1,
//             borderColor: '#ccc',
//             padding: 10,
//             textStyle: {
//                 color: '#000'
//             },
//             position: function (pos, params, el, elRect, size) {
//                 var obj = {top: 10};
//                 obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 30;
//                 return obj;
//             },
//             extraCssText: 'width: 170px'
//         },
//         axisPointer: {
//             link: {xAxisIndex: 'all'},
//             label: {
//                 backgroundColor: '#777'
//             }
//         },
//         toolbox: {
//             feature: {
//                 dataZoom: {
//                     yAxisIndex: false
//                 },
//                 brush: {
//                     type: ['lineX', 'clear']
//                 }
//             }
//         },
//         brush: {
//             xAxisIndex: 'all',
//             brushLink: 'all',
//             outOfBrush: {
//                 colorAlpha: 0.1
//             }
//         },
//         grid: [
//             {
//                 left: '5%',
//                 right: '5%',
//                 height: '50%'
//             },
//             {
//                 left: '5%',
//                 right: '5%',
//                 bottom: '20%',
//                 height: '15%'
//             }
//         ],
//         xAxis: [
//             {
//                 type: 'category',
//                 data: data.categoryData,
//                 scale: true,
//                 boundaryGap : false,
//                 axisLine: {onZero: false},
//                 splitLine: {show: false},
//                 splitNumber: 20,
//                 min: 'dataMin',
//                 max: 'dataMax',
//                 axisPointer: {
//                     z: 100
//                 }
//             },
//             {
//                 type: 'category',
//                 gridIndex: 1,
//                 data: data.categoryData,
//                 scale: true,
//                 boundaryGap : false,
//                 axisLine: {onZero: false},
//                 axisTick: {show: false},
//                 splitLine: {show: false},
//                 axisLabel: {show: false},
//                 splitNumber: 20,
//                 min: 'dataMin',
//                 max: 'dataMax',
//                 axisPointer: {
//                     label: {
//                         formatter: function (params) {
//                             var seriesValue = (params.seriesData[0] || {}).value;
//                             return params.value
//                                 + (seriesValue != null
//                                         ? '\n' + echarts.format.addCommas(seriesValue)
//                                         : ''
//                                 );
//                         }
//                     }
//                 }
//             }
//         ],
//         yAxis: [
//             {
//                 scale: true,
//                 splitArea: {
//                     show: true
//                 }
//             },
//             {
//                 scale: true,
//                 gridIndex: 1,
//                 splitNumber: 2,
//                 axisLabel: {show: false},
//                 axisLine: {show: false},
//                 axisTick: {show: false},
//                 splitLine: {show: false}
//             }
//         ],
//         dataZoom: [
//             {
//                 type: 'inside',
//                 xAxisIndex: [0, 1],
//                 start: 0,
//                 end: 100
//             },
//             {
//                 show: true,
//                 xAxisIndex: [0, 1],
//                 type: 'slider',
//                 top: '85%',
//                 start: 0,
//                 end: 100
//             }
//         ],
//         series: [
//             {
//                 name: '参考数据',
//                 type: 'candlestick',
//                 data: data.values,
//                 dimensions:['','开盘','收盘','最低','最高'],
//                 itemStyle: {
//                     normal: {
//                         color: '#06B800',
//                         color0: '#FA0000',
//                         borderColor: null,
//                         borderColor0: null
//                     }
//                 },
//                 tooltip: {
//                     formatter: function (param) {
//                         param = param[0];
//                         return [
//                             'Date: ' + param.name + '<hr size=1 style="margin: 3px 0">',
//                             'Open: ' + param.data[0] + '<br/>',
//                             'Close: ' + param.data[1] + '<br/>',
//                             'Lowest: ' + param.data[2] + '<br/>',
//                             'Highest: ' + param.data[3] + '<br/>'
//                         ].join('');
//                     }
//                 }
//             },
//             {
//                 name: 'MA5',
//                 type: 'line',
//                 data: calculateMA(5, data),
//                 smooth: true,
//                 lineStyle: {
//                     normal: {opacity: 0.5}
//                 },
//                 //             markLine: {
//                 //               data: {1:
//                 //         {
//                 //             type: 'max',
//                 //             name: '平均值',
//                 //             symbol: 'diamond'
//                 //         },
//                 // }
//                 //             }
//             },
//             {
//                 name: 'MA10',
//                 type: 'line',
//                 data: calculateMA(10, data),
//                 smooth: true,
//                 lineStyle: {
//                     normal: {opacity: 0.5}
//                 }
//             },
//             {
//                 name: 'MA20',
//                 type: 'line',
//                 data: calculateMA(5, data),
//                 smooth: true,
//                 lineStyle: {
//                     normal: {opacity: 0.5}
//                 }
//             },
//             {
//                 name: 'MA30',
//                 type: 'line',
//                 data: calculateMA(30, data),
//                 smooth: true,
//                 lineStyle: {
//                     normal: {opacity: 0.5}
//                 }
//             },
//
//             {
//                 name: '交易量',
//                 type: 'bar',
//                 xAxisIndex: 1,
//                 yAxisIndex: 1,
//                 data: data.volumns
//             }
//         ]
//     }, true);
// });
// myChart.showLoading();
// function up() {
//     // var newmoney=17832+Math.ceil(Math.random()*100);//最新价格
//     // var data0=option.series[0].data;
//     // // var data1=option.series[1].data;
//     // var data5=option.series[5].data;
//     // // data1[data1.length-2]=0;//加个归0
//     // // data1.shift();//删除第一个数组元素
//     // // data1.push(newmoney);
//     // data5.shift();
//     // data5.push([89440000+Math.random()*100]);
//     // data0.shift();
//     // data0.push([newmoney,17780+Math.ceil(Math.random()*100),17770+Math.ceil(Math.random()*10),17920+Math.ceil(Math.random()*100),894440000+Math.ceil(Math.random()*100)]);
//     var volume = [];
//     $.get("/Home/Orders/getOrdersKline",{"currency":$("#currency_id").val(),"time":"kline_1h"},function(orders){
//         // option.series[0].data=orders['kline_1h'];
//         var timedata=[];
//         var kdata=[];
//         var coinmember=[];//交易量
//         for(var i=0;i<orders['kline_1h'].length;i++)
//         {
//             timedata[i]=orders['kline_1h'][i][0];
//             kdata[i]=[orders['kline_1h'][i][2],orders['kline_1h'][i][3],orders['kline_1h'][i][4],orders['kline_1h'][i][5]];
//             coinmember[i]=orders['kline_1h'][i][1];
//         }
//         option.xAxis[1].data=timedata;
//         option.xAxis[0].data=timedata;
//         option.series[0].data=kdata;
//         option.series[5].data=coinmember;
//         myChart.setOption(option);
//         myChart.hideLoading();
//     });
// }
// setTimeout(up,4000);
// $("#chart-control > button").click(function(){
//     myChart.showLoading();
//     $(this).addClass("btn-success").siblings().removeClass("btn-success");
//     var time = $(this).attr('data-time');
//     $.get("/Home/Orders/getOrdersKline",{"currency":$("#currency_id").val(),"time":time},function(orders){
//         var volume = [];
//             // option.series[0].data=orders['kline_1h'];
//         var index=null;
//         for(var item in orders){
//             index=item;//获取json下标
//         }
//             var timedata=[];
//             var kdata=[];
//             var coinmember=[];//交易量
//             for(var i=0;i<orders[index].length;i++)
//             {
//                 timedata[i]=orders[index][i][0];
//                 kdata[i]=[orders[index][i][2],orders[index][i][3],orders[index][i][4],orders[index][i][5]];
//                 coinmember[i]=orders[index][i][1];
//             }
//             option.xAxis[1].data=timedata;
//             option.xAxis[0].data=timedata;
//             option.series[0].data=kdata;
//             option.series[5].data=coinmember;
//             myChart.setOption(option);
//             myChart.hideLoading();
//     });
// });