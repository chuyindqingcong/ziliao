<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class OrdersController extends CommonController{
    public function _initialize(){
        parent::_initialize();
        $this->assign('head_status','2');
    }
    //交易页面显示
    public function index(){
        if(empty($_GET['currency'])){
            $this->display('Public:b_stop');
            return;
        }        
        $currency_id=I('get.currency');
        $currency=M('Currency')->where("currency_mark='$currency_id' and is_line=1")->find();
        if(empty($currency)){
            $this->display('Public:b_stop');
            return;
        }
        $currency['currency_digit_num']=$currency['currency_digit_num']?$currency['currency_digit_num']:4;//设置限制位数
        //显示委托记录
        $buy_record=$this->getOrdersByType($currency['currency_id'],'buy', 10, 'desc');
        $sell_record=$this->getOrdersByType($currency['currency_id'],'sell', 10, 'asc');
        $this->assign('buy_record',$buy_record);
        $this->assign('sell_record',$sell_record);
        //格式化手续费
        $currency['currency_sell_fee']=floatval($currency['currency_sell_fee']);
        $currency['currency_buy_fee']=floatval($currency['currency_buy_fee']);
        //币种信息
        $currency_message=$this->getCurrencyMessageById($currency['currency_id']);
        $currency_trade=$this->getCurrencynameById($currency['trade_currency_id']);
				
        $this->assign('currency_message',$currency_message);
        $this->assign('currency_trade',$currency_trade);
		
		
		
        //个人账户资产
        if (!empty($_SESSION['USER_KEY_ID'])){
            $user_currency_money['currency']['num']=$this->getUserMoney($currency['currency_id'], 'num');
            $user_currency_money['currency']['forzen_num']=$this->getUserMoney($currency['currency_id'], 'forzen_num');
            $user_currency_money['currency_trade']['num']=$this->getUserMoney($currency['trade_currency_id'], 'num');
            $user_currency_money['currency_trade']['forzen_num']=$this->getUserMoney($currency['trade_currency_id'], 'forzen_num');
            if($currency['trade_currency_id']==0){
                $user_currency_money['currency_trade']['num']=$this->member['rmb'];
                $user_currency_money['currency_trade']['forzen_num']=$this->member['forzen_rmb'];
            }
            $this->assign('user_currency_money',$user_currency_money);
            //个人挂单记录
            //var_dump($this->getOrdersByUser(5,$currency['currency_id']));
            $this->assign('user_orders',$this->getOrdersByUser(10,$currency['currency_id']));
            //最大可买
            if (!empty($sell_record)){
            		  $buy_num=sprintf('%.4f',$user_currency_money['currency_trade']['num']/$sell_record[0]['price']);
            }else {
                $buy_num=0;
            }
            $this->assign('buy_num',$buy_num);
            //最大可卖
            $sell_num=sprintf('%.4f',$user_currency_money['currency']['num']);
            $this->assign('sell_num',$sell_num);


            
        }
        if(session('USER_KEY_ID')==true)
        {
            $member_id=session('USER_KEY_ID');
            $trade_fee=M('trade_fee');
            $diycurr=$trade_fee->where(['member_id'=>$member_id,'currency_id'=>$currency['currency_id']])->find();
            if($diycurr == true)
            {
                //判断是否找到自定义手续费
                $currency['currency_buy_fee']=$diycurr['currency_buy_fee'];
                $currency['currency_sell_fee']=$diycurr['currency_sell_fee'];
            }
        }
        $currencys=$this->getCurrencyMessageById($currency['currency_id']);
        $this->assign('session',$_SESSION['USER_KEY_ID']);
        $this->assign('currency',$currency);
        $this->assign('currency',$currency);
        $this->assign('status',$currencys['new_price_status']);
        //成交记录
        $trade=$this->getOrdersByStatus(2, 30, $currency['currency_id']);
        $lists = M('Trade')->where(['currency_id'=>$currency['currency_id']])->limit(0,30)->order('trade_id desc')->select();
        $this->assign('trade',$trade);
        $this->assign('lists',$lists);
        //全部币种
        $currency_all=$this->currency_10();
        foreach ($currency_all as $k=>$v){
            $list=$this->getCurrencyMessageById($v['currency_id']);
            $currency_all[$k]=array_merge($list,$currency_all[$k]);
            $list['new_price']?$list['new_price']:0;
            $currency_all[$k]['currency_all_money'] = floatval($v['currency_all_num'])*$list['new_price'];
        }
        $this->assign('currency_all',$currency_all);
       
        if(session('jypwd')>time())
        {
             $this->assign('jypwd',1);
            
        }
        else
        {
          $this->assign('jypwd',0); 
         
        }

       

        $this->display();
    }
    //交易大厅
    public function currency_trade(){
        $count = M('Currency')->where('is_line=1')->count();//根据分类查找数据数量
        $page = new \Think\Page($count,10);//实例化分页类，传入总记录数和每页显示数
        $show = $page->show();//分页显示输出性
        $currency = M('Currency')->where('is_line=1')->order('sort')->limit($page->firstRow.','.$page->listRows)->select();//时间降序排列，越接近当前时间越高
		$c = M('Currency')->where('is_line=1')->order('sort')->limit($page->firstRow.','.$page->listRows)->select();
		
		foreach ($currency as $k=>$v){
            $list=$this->getCurrencyMessageById($v['currency_id']);
            $currency[$k]=array_merge($list,$currency[$k]);
            $list['new_price']?$list['new_price']:0;
            $currency[$k]['currency_all_money'] = floatval($v['currency_all_num'])*$list['new_price'];
        }
		
		//幻灯
        $flash=M('Flash')->where('type=1')->order('sort')->limit(6)->select();
        $this->assign('flash',$flash);
	
		
		$line=$this->getThreeData($c);
		
		$len=count($line);
		$this->assign('len',$len);
		$this->assign('line',str_replace('"','',json_encode($line)));
        $this->assign('page',$show);
        $this->assign('currency',$currency);
        $this->assign('accountMoney',$this->getAccountCount());
        $this->display();
    }
    private function getAccountCount()
    {
        $where['member_id']=$_SESSION['USER_KEY_ID'];
        $currency_user=M('Currency_user')
            ->join("left join ".C('DB_PREFIX')."currency on ".C('DB_PREFIX')."currency.currency_id=".C('DB_PREFIX')."currency_user.currency_id")
            ->field(''.C('DB_PREFIX').'currency_user.*,('.C('DB_PREFIX').'currency_user.num+'.C('DB_PREFIX').'currency_user.forzen_num) as count,'.C('DB_PREFIX').'currency.currency_name,'.C('DB_PREFIX').'currency.currency_mark,'.C('DB_PREFIX').'currency.currency_logo')
            ->where($where)->order('sort')->select();
        $allcount=null;
        foreach ($currency_user as $k=>$v){
            $Currency_message=$this->getCurrencyMessageById($v['currency_id']);
            $currency_user[$k]['new_price']=$Currency_message['new_price'];
            $currency_user[$k]['cmoney']=self::listCountMoney($currency_user[$k]);
            $allcount+=$currency_user[$k]['cmoney'];
        }
        $member_rmb=$this->member;
        $allcount+=$member_rmb['rmb']+$member_rmb['forzen_rmb'];
        return $allcount;
    }
    static private function listCountMoney($arr)
    {
        $info=$arr;
        $res=($info['num']+$info['forzen_num'])*$info['new_price'];
        return $res;
    }
    //交易大厅刷新
    public function currency_trade_shuaxin(){
        $count = M('Currency')->where('is_line=1')->count();//根据分类查找数据数量

        $currency = M('Currency')->where('is_line=1')->order('sort')->select();//时间降序排列，越接近当前时间越高
        $c = M('Currency')->where('is_line=1')->order('sort')->select();
        
        foreach ($currency as $k=>$v){
            $list=$this->getCurrencyMessageById($v['currency_id']);
            $currency[$k]=array_merge($list,$currency[$k]);
            $list['new_price']?$list['new_price']:0;
            $currency[$k]['currency_all_money'] = floatval($v['currency_all_num'])*$list['new_price'];
        }
        
       
        
        $this->ajaxReturn($currency);
    }


	//3日涨势图数据
	public function getThreeData($currency)
	{
		
		$time=time();
		
		$t=$time-60*60*24*3;//3日前时间
		
		$default=array(
		
			array(1991,0.5),
			array(1996,0.5),
			array(1998,0.5)
		
		);
		$data=array();
		foreach($currency as $key=>$value)
		{
			$data[$key]['currency_id']=$value['currency_id'];
			$where['currency_id']=array('eq',$value['currency_id']);
			$where['add_time']=array('between',array($t,$time));
			$re=M('trade')->where($where)->order('add_time asc')->field('add_time as x,price as y')->limit(0,20)->select();
			if($re)
			{
				// $data[$key]['data']=$re;
                foreach ($re as $k => $v) {
                    $data[$key]['data'][$k][0]=$v['x'];
                    $data[$key]['data'][$k][1]=$v['y'];
                }
			}
			else
			{
				$data[$key]['data']=$default;
			}
		}
		/*
			print_r("<pre>");
			print_r($data);
			print_r("<pre>");
		
		*/
		
		return $data;
		
		
	}
	
	
	
    
    //获取挂单记录
    public function getOrders(){
        switch (I('post.type')){
          case 'buy':  $this->ajaxReturn($this->getOrdersByType(I('post.currency_id'),'buy', 10, 'desc'));
          break;
          case 'sell':$this->ajaxReturn($this->getOrdersByType(I('post.currency_id'),'sell', 10, 'asc'));
          break;
        }
    }
    //获取k线
    public function getOrdersKline(){
        if(empty($_GET['currency'])){
            return;
        }
        $currency_id=I('get.currency');
        //K线
        $char=!empty($_GET['time'])?I('get.time'):'kline_1h';
        switch ($char){
            case 'kline_1m':$time=1;break;
            case 'kline_3m':$time=3;break;
            case 'kline_5m':$time=5;break;
            case 'kline_15m':$time=15;break;
            case 'kline_30m':$time=30;break;
            case 'kline_1h':$time=60;break;
            case 'kline_8h':$time=480;break;
            case 'kline_1d':$time=24*60;break;
            case 'kline_7d':$time=168*60;break;
            case 'kline_12h':$time=12*60;break;
            default:$time=60;
        }
        $data[$char]=$this->getKline($time,$currency_id);
        $this->ajaxReturn($data);
    }
    //获取K线
    private function getKline($base_time,$currency_id){
            $time=time()-$base_time*60*120;
            for ($i=0;$i<120;$i++){
             $start= $time+$base_time*60*$i;
             $end=$start+$base_time*60;
            //时间
            // $item[$i][]=$start*1000+8*3600*1000;
            $item[$i][]=date('Y-m-d H:i:s',$start);
            $where['currency_id']=$currency_id;
            $where['type']='buy';
            $where['add_time']=array('between',array($start,$end));
     
            //交易量
          $num=M('Trade')->where($where)->sum('num');
          $item[$i][]=!empty($num)?floatval($num):0;
            //开盘
            $where_price['currency_id']=$currency_id;
            $where_price['type']='buy';
            $where_price['add_time']=array('elt',$end);
          
            $order_k=M('Trade')->field('price')->where($where_price)->order('add_time desc')->find();
            $item[$i][]=!empty($order_k['price'])?floatval($order_k['price']):0;
            //最高
           $max=M('Trade')->where($where)->max('price');
           $max=!empty($max)?floatval($max):$order_k['price'];
           $max=!empty($max)?$max:0;
           $item[$i][]=$max;
            //最低
            $min=M('Trade')->where($where)->min('price');
            $min=!empty($min)?floatval($min):$order_k['price'];
            $item[$i][]=!empty($min)?$min:0;
            //收盘
            $order_s=M('Trade')->field('price')->where($where)->order('add_time asc')->find();
            $order_s=!empty($order_s['price'])?floatval($order_s['price']):$order_k['price'];
            $item[$i][]=!empty($order_s)?$order_s:0;
        }
       // $item=json_encode($item,true);
       // $item=json_encode($item,true);
        return $this->average($item);//获取均线
//        return $item;
    }
    private function average($data)
    {

        //平均线
        $kdata=$data;
        $tmp=null;
        $summember=count($kdata);
        $arr=array();
        foreach ($kdata as $key=>$value)
        {
            $tmp=null;
            for($i=0;$i<5;$i++)
            {
                if($key+5>=$summember)
                {
                    $tmp+=$kdata[$key-$i][5];
                }else{
                    $tmp+=$kdata[$key+$i][5];
                }
            }
            $kdata[$key][6]=($tmp/5);
        }
        return $kdata;
    }
    public function kline($currency)
    {
        $this->display();
    }
}