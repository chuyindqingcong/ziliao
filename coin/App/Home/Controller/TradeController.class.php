<?php
namespace Home\Controller;
use Home\Controller\TradeFatherController;
use Home\Model\InvitModel;
use Think\Page;
class TradeController extends TradeFatherController {
	protected  $currency;
	protected  $entrust;
	protected  $trade;
	public function _initialize(){
		parent::_initialize();

		$this->currency=M('Currency');
		$this->entrust=M('Entrust');
		$this->trade=M('Orders');
        $this->assign('head_status','3');
	}
	//空操作
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
	
    
    //买入
    //$buyprice 购买价格
    public function buy(){
        if(!$this->checkLogin()){
            $data['status']=0;
            $data['info']='请先登录再进行此操作';
            $this->ajaxReturn($data);
        }

        //交易时间段限制
        $time=strtotime(date('Y-m-d'));
        $start_time=$time+$this->config['jiaoyi_start_hour']*3600+$this->config['jiaoyi_start_minute']*60;
        $over_time=$time+$this->config['jiaoyi_over_hour']*3600+$this->config['jiaoyi_over_minute']*60;
   		if(time()<$start_time||time()>$over_time){
			$data['status']=-10;
			$data['info']='交易未开启，请在交易时间内进行交易。';
			$this->ajaxReturn($data);
		}
        $buyprice=floatval(I('post.buyprice'));
        $buynum=floatval(I('post.buynum'));
        $buypwd=I('post.buypwd');
        $buycurrency_id=intval(I('post.currency_id'));
        //获取币的相关信息
        $currency=$this->getCurrencyByCurrencyId($buycurrency_id);
        if ($currency['is_lock']){
            $data['status']=-5;
            $data['info']= '该币种暂时不允许交易';
            $this->ajaxReturn($data);
        }
        
        // if(!is_numeric($buyprice)||!is_numeric($buynum)){
        //     $data['status']=0;
        //     $data['info']='您的挂单价格或数量有误,请修改';
        //     $this->ajaxReturn($data);
        // }


        if (empty($buyprice)||empty($buynum)){
            $msg['status']=-3;
            $msg['info']='买入价格或数量不能为空';
            $this->ajaxReturn($msg);
        }


        //涨停价格限制
        if ($currency['price_down']>0&&$buyprice<$currency['price_down']){
            $msg['status']=-9;
            $msg['info']='交易价格超出了跌停价格限制';
            $this->ajaxReturn($msg);
        }
        
        //涨停价格限制
        if ($currency['price_up']>0&&$buyprice>$currency['price_up']){
            $msg['status']=-7;
            $msg['info']='交易价格超出了涨停价格限制';
            $this->ajaxReturn($msg);
        }
        // if (!is_int($buynum)){
        //     $data['status']=-1;
        //     $data['info']='交易数量必须是整数';
        //     $this->ajaxReturn($data);
        // }

        // $xslen=$this->getfloadLen($buynum);

        // if($xslen>4 && $xslen<0)
        // {
        //     $data['status']=-1;
        //     $data['info']='交易数量不合法,数量精确的小数点后4位';
        //     $this->ajaxReturn($data);
        // }

        if ($buynum<0){
            $data['status']=-2;
            $data['info']='交易数量必须是正数';
            $this->ajaxReturn($data);
        }
        
        if ($buyprice*$buynum<10){
            $data['status']=0;
            $data['info']='不能委托低于10元的订单';
            $this->ajaxReturn($data);
        }
        
        $member=$this->member;
        if(session('jypwd')<time())
        {
           if(md5(I('post.buypwd'))!=$member['pwdtrade']){
            $data['status']=-3;
            $data['info']='请输入正确的交易密码';
            $this->ajaxReturn($data);
             } 
        }
        
        
        if ($this->checkUserMoney($buynum, $buyprice, 'buy', $currency)){
            $data['status']=-4;
            $data['info']='您账户余额不足';
            $this->ajaxReturn($data);
        }
      //  M()->query('lock tables yang_orders write, yang_currency_user write');
        //开启事物
        M()->startTrans();
       
        //计算买入需要的金额
        $trade_money=$buynum*$buyprice;//*(1+($currency['currency_buy_fee']/100));
        // $trade_money+=$trade_money*($currency['currency_buy_fee']/100);
        //操作账户
        //$currency['trade_currency_id'] 可交易币种 等于0 @$trade_money买入金额
        $r[]=$this->setUserMoney($this->member['member_id'],$currency['trade_currency_id'], $trade_money,'dec', 'num');//扣除余额
		$r[]=$this->setUserMoney($this->member['member_id'],$currency['trade_currency_id'], $trade_money, 'inc','forzen_num');//冻结余额
        //挂单流程
       $r[]=$this->guadan($buynum, $buyprice, 'buy', $currency);
       
       
       //交易流程
       $r1[]=$this->trade($currency['currency_id'], 'buy', $buynum, $buyprice,$currency);
       foreach ($r1 as $v){
           $r[]=$v;
       }
 //      M()->query("UNLOCK TABLES");
       if (in_array(false, $r)){
           M()->rollback();
           $msg['status']=-7;
           $msg['info']='操作未成功';
           $this->ajaxReturn($msg);
       }else {
           M()->commit();
           if(session('jypwd')<time()){

               
                session('jypwd',time()+900);
                session('tag',2);
           }
           
           
           $msg['status']=1;
           $msg['info']='操作成功';
           $this->ajaxReturn($msg);
       }
       
    }


    protected function getfloadLen($num)
    {
        $count=0;
        $temp=explode('.',$num);
        if(sizeof($temp)==2)
        {
            $dec=end($temp);
            $count=strlen($dec);

        }
        if(sizeof($temp)>2)
        {
            
            $count=-1;

        }

        return $count;
    }


    public function shuaxin()
    {
        
        
        if(session('tag')==2)
        {


            if(session('jypwd')<time())
            {
                session('tag',1);
                $msg['status']=1;
                $msg['info']='';
                $this->ajaxReturn($msg);
            }
            
        }
        
       
    }

	/*卖出
	 * 
	 * 1.是否登录
	 * 1.5 是否开启交易
	 * 2.准备数据
	 * 3.判断数据
	 * 4.检查账户
	 * 5.操作个人账户
	 * 6.写入数据库
	 * 
	 * 
	 * 
	 */
	
	public function sell(){
		if(!$this->checkLogin()){
			$data['status']=-1;
			$data['info']='请先登录再进行此操作';
			$this->ajaxReturn($data);
		}
		//交易时间段限制
		$time=strtotime(date('Y-m-d'));
		$start_time=$time+$this->config['jiaoyi_start_hour']*3600+$this->config['jiaoyi_start_minute']*60;
		$over_time=$time+$this->config['jiaoyi_over_hour']*3600+$this->config['jiaoyi_over_minute']*60;
		if(time()<$start_time||time()>$over_time){
			$data['status']=-10;
			$data['info']='交易未开启，请在交易时间内进行交易。';
			$this->ajaxReturn($data);
		}
		$sellprice=I('post.sellprice');
		$sellnum=I('post.sellnum');
		$sellpwd=I('post.sellpwd');
		$currency_id=I('post.currency_id');
		//获取币种信息
		$currency=$this->getCurrencyByCurrencyId($currency_id);
		//检查是否开启交易
	   if ($currency['is_lock']){
	       $msg['status']=-2;
	       $msg['info']='该币种暂时不能交易';
	       $this->ajaxReturn($msg);
	   }
	   
		if (empty($sellprice)||empty($sellnum)){
		    $msg['status']=-3;
		    $msg['info']='卖出价格或数量不能为空';
		    $this->ajaxReturn($msg);
		}
		
		if ($sellnum*$sellprice<10){
		    $data['status']=0;
		    $data['info']='不能委托低于10元的订单';
		    $this->ajaxReturn($data);
		}
		
		

        $member=M("Member")->where("member_id='{$_SESSION['USER_KEY_ID']}'")->find();
        if(session('jypwd')<time())
        {
            if ($this->member['pwdtrade']!=md5($sellpwd)){
            $msg['status']=-5;
            $msg['info']='请输入正确的交易密码';
            $this->ajaxReturn($msg);
            }

        }
		
		//涨停价格限制
		if ($currency['price_down']>0&&$sellprice<$currency['price_down']){
		    $msg['status']=-9;
		    $msg['info']='交易价格超出了跌停价格限制';
		    $this->ajaxReturn($msg);
		}
		
		//涨停价格限制
		if ($currency['price_up']>0&&$sellprice>$currency['price_up']){
		    $msg['status']=-7;
		    $msg['info']='交易价格超出了涨停价格限制';
		    $this->ajaxReturn($msg);
		}
		//检查账户是否有钱
		if ($this->checkUserMoney($sellnum, $sellprice, 'sell', $currency)){
		    $msg['status']=-6;
		    $msg['info']='您的账户余额不足';
		    $this->ajaxReturn($msg);
		}
		//减可用钱 加冻结钱
		M()->startTrans();
		$r[]=$this->setUserMoney($this->member['member_id'],$currency['currency_id'], $sellnum, 'dec', 'num');
		$r[]=$this->setUserMoney($this->member['member_id'],$currency['currency_id'], $sellnum,'inc','forzen_num');
        //写入数据库
		$r[]=$this->guadan($sellnum, $sellprice, 'sell', $currency);
		//成交
		$r[]=$this->trade($currency['currency_id'], 'sell', $sellnum, $sellprice,$currency);
		 //设置交易密码状态
       
       
		
		if (in_array(false, $r)){
		    M()->rollback();
		    $msg['status']=-7;
		    $msg['info']='操作未成功';
		    $this->ajaxReturn($msg);
		}else {
		    M()->commit();

            if(session('jypwd')<time()){

               
                session('jypwd',time()+900);
                session('tag',2);
           }

		    $msg['status']=1;
		    $msg['info']='操作成功';
		    $this->ajaxReturn($msg);
		}
		
	} 

    //返利查询
    public function rebate()
    {
        $invit=D('invit');
        $map=array();
        $map['member_id']=session('user.member_id');
        if(I('get.start')==true && I('get.endtime')==true)
        {
            $start=strtotime(I('get.start'));
            $endtime=strtotime(I('get.endtime'));
            $map['ctime'] = array('BETWEEN',array($start,$endtime));
        }else if(I('get.start')==true)
        {
            $start=strtotime(I('get.start'));
            $map['ctime']=array('EGT',$start);
        }else if(I('get.endtime')==true)
        {
            $endtime=I('get.endtime');
            $map['ctime']=array('ELT',$endtime);
        }
        $count      =  $invit->where($map)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list=$invit->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('invit_id desc')->select();
        // $list=$invit->whoere($map)->select();
        $this->assign('list',$list);
         $this->assign('page',$show);
        // dump($list);
        $this->display();
    }

	//我的成交
	public function myDeal(){
	    if (!$this->checkLogin()){
	        $this->redirect(U('Index/index','',false));
	    }

        if(I('post.currency')){
            $this->assign('currencyid',I('post.currency'));
        }
	  //获取主币种
		$currency=$this->getCurrencyByCurrencyId();
		$this->assign('culist',$currency);
		$currencytype = I('currency');

        $mm = I('maimai');

        if(!empty($mm))
        {
            $where['type']=$mm;
        }
	
		if(!empty($currencytype)){
			$where['currency_id'] =$currencytype;
		}
        if(I('currencys') == true)
        {
            $where['currency_id']=I('currencys');
        }
        $starttime =strtotime(I('starttime'));
        $endtime =strtotime(I('endtime'));
        if(!empty($starttime) && !empty($endtime)){
            $where['add_time'] = array(array('EGT',$starttime),array('ELT',$endtime)) ;
        }
        
		$where['member_id'] = $_SESSION['USER_KEY_ID'];

	    $count      = M('Trade')->where($where)->count();// 查询满足要求的总记录数
	    $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
	    foreach($_POST as $key=>$val) {
        $Page->parameter[$key]   =   urlencode($val);
        }
        foreach($_GET as $key=>$val) {
        $Page->parameter[$key]   =   urlencode($val);
        }
	    //给分页传参数
	    setPageParameter($Page, array('currency'=>$currencytype));
	    
	    $show       = $Page->show();// 分页显示输出
	    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	    $list = M('Trade')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('trade_id desc')->select();
	    $this->assign('page',$show);// 赋值分页输出
	    $this->assign('list',$list);
	    $this->display();
	}
	

	
    /**
     *
     * @param int  $num 数量
     * @param float $price 价格
     * @param char $type 买buy 卖sell
     * @param $currency_id 交易币种
     */
        private function  checkUserMoney($num,$price,$type,$currency){
        	
            //获取交易币种信息
            if ($type=='buy'){
                $trade_money=$num*$price*(1+$currency['currency_buy_fee']/100);
                $currency_id=$currency['trade_currency_id'];
            }else {
                $trade_money=$num;
                $currency_id=$currency['currency_id'];
            }
            //和自己的账户做对比 获取账户信息
            $money=$this->getUserMoney($currency_id, 'num');
            if ($money<$trade_money){
                return true;
            }else{
                return false;
            }
        }

    /**
     * 挂单
     * @param int  $num 数量
     * @param float $price 价格
     * @param char $type 买buy 卖sell
     * @param $currency_id 交易币种
     */
    private function guadan($num,$price,$type,$currency){
        //获取交易币种信息
        $trade_fee=M('trade_fee');
        $map['member_id']=$this->member['member_id'];
        $map['currency_id']=$currency['currency_id'];
        $info=$trade_fee->where($map)->find();
        switch ($type){
            case 'buy':
                    // $fee=$currency['currency_buy_fee']/100;
                    if($info == true)
                    {
                        //获取到自定义费率
                        $currency['currency_buy_fee']=$info['currency_buy_fee'];
                    }
                    $fee=$num*($currency['currency_buy_fee']/100);//计算手续费
                    $currency_trade_id=$currency['trade_currency_id'];
                    break;
            case 'sell':
                if($info == true)
                    {
                        //获取到自定义费率
                        $currency['currency_sell_fee']=$info['currency_sell_fee'];
                    }
                    $fee=($num*$price)*$currency['currency_sell_fee']/100;
                    $currency_trade_id=$currency['trade_currency_id'];
                break;
        }
        $data=array(

            'member_id'=>$this->member['member_id'],
            'currency_id'=>$currency['currency_id'],
            'currency_trade_id'=>$currency['trade_currency_id'],
            'price'=>$price,
            'num'=>$num,
            'trade_num'=>0,
            'fee'=>$fee,
            'type'=>$type,
        );
        if (D('Orders')->create($data)){
          $msg=D('Orders')->add();
          
        }else {
            $msg=0;
        }

        return $msg;
        
    }        
    //$num 交易数量
    // private function trade($currencyId,$type,$num,$price,$diycurrency){
    //     if ($type=='buy'){
    //         $trade_type='sell';
    //     }else {
    //         $trade_type='buy';
    //     }
    //     $memberId=$_SESSION['USER_KEY_ID'];
    //     //获取操作人一个订单
    //     $order=$this->getFirstOrdersByMember($memberId,$type ,$currencyId);
    //     //获取对应交易的一个订单
    //     $trade_order=$this->getOneOrders($trade_type, $currencyId,$price);
    //    //如果没有相匹配的订单，直接返回
    //     if (empty($trade_order)){
    //         $r[]=true;
    //         return $r;
    //     }
    //     //如果有就处理订单、
    //     //如果返回 $num 就证明有充足的剩余输量   返回 $trade_order['num']-$trade_order['trade_num']的结果 只扣除本订单的剩余数 
    //     $trade_num=min($num,$trade_order['num']-$trade_order['trade_num']);
    //     //增加本订单的已经交易的数量
    //     $r[]=M('Orders')->where("orders_id={$order['orders_id']}")->setInc('trade_num',$trade_num);
    //     $r[]=M('Orders')->where("orders_id={$order['orders_id']}")->setField('trade_time',time());
    //     //增加trade订单的已经交易的数量
    //     $r[]=M('Orders')->where("orders_id={$trade_order['orders_id']}")->setInc('trade_num',$trade_num);
    //     $r[]=M('Orders')->where("orders_id={$trade_order['orders_id']}")->setField('trade_time',time());

    //     //更新一下订单状态
    //     $r[]=M('Orders')->where("trade_num>0 and status=0")->setField('status',1);
    //     $r[]=M('Orders')->where("num=trade_num")->setField('status',2);
      
    //     $mefee=null;//获取本次订单类型手续费
    //     $coinfee=null;
    //     $partyfee=($trade_num*$price)*($diycurrency['currency_'.$trade_type.'_fee']/100);//获取相反操作的手续费
    //     $trade_fee=M('trade_fee');
    //     $map['member_id']=$order['member_id'];
    //     $map['currency_id']=$order['currency_id'];
    //     $medivfee=$trade_fee->where($map)->find();
    //     if($medivfee==true)
    //     {
    //         $mefee=($trade_num*$price)*($medivfee['currency_'.$type.'_fee']/100);
    //         $coinfee=$trade_num*($pardivfee['currency_'.$type.'_fee']/100);//币数手续费

    //     }else{
    //         $mefee=($trade_num*$price)*($diycurrency['currency_'.$type.'_fee']/100);
    //     }

    //     $map['member_id']=$trade_order['member_id'];
    //     $pardivfee=$trade_fee->where($map)->find();
    //     if($pardivfee==true)
    //     {
    //         $partyfee=($trade_num*$price)*($pardivfee['currency_'.$trade_type.'_fee']/100);//存在自定义的手续费设置
    //     }else{
    //         $partyfee=($trade_num*$price)*($diycurrency['currency_'.$trade_type.'_fee']/100);
    //     }
    //     $metrade=0;
    //     $partrade=0;
    //      $feenumber=null;
    //     //$mefee=null;//买入手续费
    //     //处理资金
    //     switch ($type){

    //         case 'buy':
    //             $parfeermb=null;
    //             if($medivfee==true)
    //             {
    //                 $mefee=$trade_num*($medivfee['currency_'.$type.'_fee']/100);
    //                 $coinfee=$trade_num*($medivfee['currency_'.$type.'_fee']/100);//到账币数
    //             }else{
    //                 $mefee=$trade_num*($diycurrency['currency_'.$type.'_fee']/100);
    //                 $coinfee=$trade_num*($diycurrency['currency_'.$type.'_fee']/100);//到账币数
    //             }
    //             if($pardivfee==true)
    //             {
    //                 $parfeermb=$trade_num*$trade_price*($pardivfee['currency_'.$trade_type.'_fee']/100);//卖出手续费
    //             }else{
                    
    //                 $parfeermb=$trade_num*$trade_price*($diycurrency['currency_'.$trade_type.'_fee']/100);//卖出手续费
    //             }
    //             $trade_price=min($order['price'],$trade_order['price']);
    //             $trade_order_money= $trade_num*$trade_price*(1-($trade_order['fee']/100));
    //             $order_money= $trade_num*$trade_price;//*(1+$order['fee']);
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_id'],($trade_num-$mefee), 'inc', 'num');//增加购买者币种数量
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $order_money, 'dec', 'forzen_num');
    //             // echo ($trade_num*$trade_price)-$partyfee;
    //             $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_id'],  $trade_num, 'dec', 'forzen_num');
    //             $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_trade_id'], ($trade_num*$trade_price)-$partyfee, 'inc', 'num');//对方卖出金额到账

    //             //返还多扣除的部分
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $trade_num*($order['price']-$trade_price), 'inc', 'num');
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $trade_num*($order['price']-$trade_price), 'dec', 'forzen_num');
    //             //手续费   ($num*$price)*($diycurrency['currency_buy_fee']/100)计算出当前买入手续费
    //             $r[]=$this->addFinance($order['member_id'], 11, '交易手续费',$coinfee, 2, $order['currency_id']);
    //             $r[]=$this->addFinance($trade_order['member_id'], 11, '交易手续费',$parfeermb, 2, $order['currency_trade_id']);
    //             break;
    //         case 'sell':
    //             $trade_price=max($order['price'],$trade_order['price']);
    //             $order_money= $trade_num*$trade_price*(1-($order['fee']/100));
    //             $trade_order_money= $trade_num*$trade_price;//*(1+$trade_order['fee']);
    //             if($pardivfee==true)
    //             {
    //                 $feenumber=$trade_num*($pardivfee['currency_buy_fee']/100);
    //             }else{
    //                 $feenumber=$trade_num*($diycurrency['currency_buy_fee']/100);
    //             }
    //             // //执行卖出到账
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_id'], $trade_num, 'dec', 'forzen_num');//本次操作冻结的账户余额扣除 $order
    //             $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], ($trade_num*$price)-($mefee), 'inc', 'num');//卖出币数增加至余额币数
    //             //卖出到账结束
    //             $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_id'], $trade_num-$feenumber, 'inc', 'num');
    //             // dump($trade_num*$price);
    //             // die();
    //             $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_trade_id'],$trade_order_money, 'dec', 'forzen_num');//扣除买入的订单冻结金额
    //             //手续费
    //             //
    //             $r[]=$this->addFinance($order['member_id'], 11, '交易手续费',$mefee, 2, $order['currency_trade_id']);
    //             $r[]=$this->addFinance($trade_order['member_id'], 11, '交易手续费',$partyfee, 2, $order['currency_id']);
    //             break;
    //     }
    //     $coin=null;
    //     //修正最终成交的价格
    //     $r[]=M('Orders')->where("num=trade_num and orders_id={$order['orders_id']}")->setField('price',$trade_price);
    //     $r[]=M('Orders')->where("num=trade_num and orders_id={$trade_order['orders_id']}")->setField('price',$trade_price);
    //     //写入成交表
    //     if($order['type']=='buy')
    //     {
    //         //买入
    //         $tradeid=$this->addTrade($order['member_id'], $order['currency_id'], $order['currency_trade_id'],$trade_price, $trade_num, $order['type'],$mefee);
    //         if($tradeid>0)
    //         {
    //             $res[]=true;
    //         }else{
    //             $res[]=false;
    //         }
    //         $partrade=$this->addTrade($trade_order['member_id'], $trade_order['currency_id'], $trade_order['currency_trade_id'], $trade_price, $trade_num, $trade_order['type'],$partyfee);
    //         if($partrade>0)
    //         {
    //             $res[]=true;
    //         }else{
    //             $res[]=false;
    //         }
    //         $this->meFee($order['member_id'],$mefee,$trade_order['member_id'],$type,$tradeid);//添加我的手续费分利记录
    //         $this->meFee($trade_order['member_id'],$partyfee,$trade_order['member_id'],$trade_type,$partrade);
    //     }else{
            
    //         $coin=null;
    //         //卖出
    //         if($medivfee==true)
    //         {
    //             $coin=($trade_num*$order['price'])*($medivfee['currency_'.$type.'_fee']/100);
    //         }else{
    //             $coin=($trade_num*$order['price'])*($diycurrency['currency_'.$type.'_fee']/100);
    //         }
    //         $tradeid=$this->addTrade($order['member_id'], $order['currency_id'], $order['currency_trade_id'],$trade_price, $trade_num, $order['type'],$coin);

    //         if($tradeid>0)
    //         {
    //             $res[]=true;
    //         }else{
    //             $res[]=false;
    //         }
            
           
    //         $partrade=$this->addTrade($trade_order['member_id'], $trade_order['currency_id'], $trade_order['currency_trade_id'], $trade_price, $trade_num, $trade_order['type'],$feenumber);
    //         if($partrade>0)
    //         {
    //             $res[]=true;
    //         }else{
    //             $res[]=false;
    //         }
    //         if($pardivfee==true)
    //         {
    //             $partyfee=$trade_num*($pardivfee['currency_'.$trade_type.'_fee']/100);//存在自定义的手续费设置
    //         }else{
    //             $partyfee=$trade_num*$price*($diycurrency['currency_'.$trade_type.'_fee']/100);
    //         }
    //         $this->meFee($order['member_id'],$coin,$trade_order['member_id'],$type,$tradeid);//添加我的手续费分利记录
    //         $this->meFee($trade_order['member_id'],$partyfee,$trade_order['member_id'],$trade_type,$partrade);
    //     }
    //     // die;
    //     $num =$num- $trade_num;
    //     if ($num>0){
    //         //递归
    //        $r[]= $this->trade($currencyId, $type, $num, $price,$diycurrency);
    //     }
        

    //     return $r;
        
    // }
     private function trade($currencyId,$type,$num,$price,$diycurrency){
        if ($type=='buy'){
            $trade_type='sell';
        }else {
            $trade_type='buy';
        }
        $memberId=$_SESSION['USER_KEY_ID'];
        //获取操作人一个订单
        $order=$this->getFirstOrdersByMember($memberId,$type ,$currencyId);
        //获取对应交易的一个订单
        $trade_order=$this->getOneOrders($trade_type, $currencyId,$price);
       //如果没有相匹配的订单，直接返回
        if (empty($trade_order)){
            $r[]=true;
            return $r;
        }
        //如果有就处理订单、
        //如果返回 $num 就证明有充足的剩余输量   返回 $trade_order['num']-$trade_order['trade_num']的结果 只扣除本订单的剩余数 
        $trade_num=min($num,$trade_order['num']-$trade_order['trade_num']);
        //增加本订单的已经交易的数量
        $r[]=M('Orders')->where("orders_id={$order['orders_id']}")->setInc('trade_num',$trade_num);
        $r[]=M('Orders')->where("orders_id={$order['orders_id']}")->setField('trade_time',time());
        //增加trade订单的已经交易的数量
        $r[]=M('Orders')->where("orders_id={$trade_order['orders_id']}")->setInc('trade_num',$trade_num);
        $r[]=M('Orders')->where("orders_id={$trade_order['orders_id']}")->setField('trade_time',time());

        //更新一下订单状态
        $r[]=M('Orders')->where("trade_num>0 and status=0")->setField('status',1);
        $r[]=M('Orders')->where("num=trade_num")->setField('status',2);
      
        $mefee=null;//获取本次订单类型手续费
        $coinfee=null;
        $partyfee=($trade_num*$price)*($diycurrency['currency_'.$trade_type.'_fee']/100);//获取相反操作的手续费
        $trade_fee=M('trade_fee');
        $map['member_id']=$order['member_id'];
        $map['currency_id']=$order['currency_id'];
        $medivfee=$trade_fee->where($map)->find();
        if($medivfee==true)
        {
            $mefee=($trade_num*$price)*($medivfee['currency_'.$type.'_fee']/100);
            $coinfee=$trade_num*($pardivfee['currency_'.$type.'_fee']/100);//币数手续费

        }else{
            $mefee=($trade_num*$price)*($diycurrency['currency_'.$type.'_fee']/100);
        }

        $map['member_id']=$trade_order['member_id'];
        $pardivfee=$trade_fee->where($map)->find();
        if($pardivfee==true)
        {
            $partyfee=($trade_num*$price)*($pardivfee['currency_'.$trade_type.'_fee']/100);//存在自定义的手续费设置
        }else{
            $partyfee=($trade_num*$price)*($diycurrency['currency_'.$trade_type.'_fee']/100);
        }
        $metrade=0;
        $partrade=0;
         $feenumber=null;
         $$trade_price=null;
        //$mefee=null;//买入手续费
        //处理资金
        switch ($type){

            case 'buy':
                $trade_price=min($order['price'],$trade_order['price']);
                 if($pardivfee==true)
                {
                    $partyfee=($trade_num*$trade_price)*($pardivfee['currency_'.$trade_type.'_fee']/100);//存在自定义的手续费设置
                }else{
                    $partyfee=($trade_num*$trade_price)*($diycurrency['currency_'.$trade_type.'_fee']/100);
                }
                $parfeermb=null;
                if($medivfee==true)
                {
                    $mefee=$trade_num*($medivfee['currency_'.$type.'_fee']/100);
                    $coinfee=$trade_num*($medivfee['currency_'.$type.'_fee']/100);//到账币数
                }else{
                    $mefee=$trade_num*($diycurrency['currency_'.$type.'_fee']/100);
                    $coinfee=$trade_num*($diycurrency['currency_'.$type.'_fee']/100);//到账币数
                }
                if($pardivfee==true)
                {
                    $parfeermb=$trade_num*$trade_price*($pardivfee['currency_'.$trade_type.'_fee']/100);//卖出手续费
                }else{
                    
                    $parfeermb=$trade_num*$trade_price*($diycurrency['currency_'.$trade_type.'_fee']/100);//卖出手续费
                }
                
                $trade_order_money= $trade_num*$trade_price*(1-($trade_order['fee']/100));
                $order_money= $trade_num*$trade_price;//*(1+$order['fee']);
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_id'],($trade_num-$mefee), 'inc', 'num');//增加购买者币种数量
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $order_money, 'dec', 'forzen_num');
                // echo ($trade_num*$trade_price)-$partyfee;
                $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_id'],  $trade_num, 'dec', 'forzen_num');
                $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_trade_id'], ($trade_num*$trade_price)-$partyfee, 'inc', 'num');//对方卖出金额到账

                //返还多扣除的部分
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $trade_num*($order['price']-$trade_price), 'inc', 'num');
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], $trade_num*($order['price']-$trade_price), 'dec', 'forzen_num');
                //手续费   ($num*$price)*($diycurrency['currency_buy_fee']/100)计算出当前买入手续费
                $r[]=$this->addFinance($order['member_id'], 11, '交易手续费',$coinfee, 2, $order['currency_id']);
                $r[]=$this->addFinance($trade_order['member_id'], 11, '交易手续费',$parfeermb, 2, $order['currency_trade_id']);
                break;
            case 'sell':
                $trade_price=max($order['price'],$trade_order['price']);
                $mefee=($trade_num*$trade_price)*($medivfee['currency_'.$type.'_fee']/100);
                 if($medivfee==true)
                {
                    $mefee=($trade_num*$trade_price)*($medivfee['currency_'.$type.'_fee']/100);

                }else{
                    $mefee=($trade_num*$trade_price)*($diycurrency['currency_'.$type.'_fee']/100);
                }
               
                // $trade_price=min($order['price'],$trade_order['price']);
                // dump($trade_price);

                $order_money= $trade_num*$trade_price*(1-($order['fee']/100));
                $trade_order_money= $trade_num*$trade_price;//*(1+$trade_order['fee']);
                if($pardivfee==true)
                {
                    $feenumber=$trade_num*($pardivfee['currency_buy_fee']/100);
                }else{
                    $feenumber=$trade_num*($diycurrency['currency_buy_fee']/100);
                }
                // //执行卖出到账
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_id'], $trade_num, 'dec', 'forzen_num');//本次操作冻结的账户余额扣除 $order
                $r[]=$this->setUserMoney($this->member['member_id'],$order['currency_trade_id'], ($trade_num*$trade_price)-($mefee), 'inc', 'num');//卖出币数增加至余额币数
                //卖出到账结束
                $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_id'], $trade_num-$feenumber, 'inc', 'num');
                // dump($trade_num*$price);
                // die();
                $r[]=$this->setUserMoney($trade_order['member_id'],$trade_order['currency_trade_id'],$trade_order_money, 'dec', 'forzen_num');//扣除买入的订单冻结金额
                //手续费
                //
                $r[]=$this->addFinance($order['member_id'], 11, '交易手续费',$mefee, 2, $order['currency_trade_id']);
                $r[]=$this->addFinance($trade_order['member_id'], 11, '交易手续费',$partyfee, 2, $order['currency_id']);
                break;
        }
        $coin=null;
        //修正最终成交的价格
       $r[]=M('Orders')->where("num=trade_num and orders_id={$order['orders_id']}")->setField('price',$trade_price);
        $r[]=M('Orders')->where("num=trade_num and orders_id={$trade_order['orders_id']}")->setField('price',$trade_price);
        //写入成交表
        if($order['type']=='buy')
        {
            //买入
            $tradeid=$this->addTrade($order['member_id'], $order['currency_id'], $order['currency_trade_id'],$trade_price, $trade_num, $order['type'],$mefee);
            if($tradeid>0)
            {
                $res[]=true;
            }else{
                $res[]=false;
            }
            $partrade=$this->addTrade($trade_order['member_id'], $trade_order['currency_id'], $trade_order['currency_trade_id'], $trade_price, $trade_num, $trade_order['type'],$partyfee);
            if($partrade>0)
            {
                $res[]=true;
            }else{
                $res[]=false;
            }
            $this->meFee($order['member_id'],$mefee,$trade_order['member_id'],$type,$tradeid);//添加我的手续费分利记录
            $this->meFee($trade_order['member_id'],$partyfee,$trade_order['member_id'],$trade_type,$partrade);
        }else{
            
            $coin=null;
            //卖出
            if($medivfee==true)
            {
                $coin=($trade_num*$trade_price)*($medivfee['currency_'.$type.'_fee']/100);
            }else{
                $coin=($trade_num*$trade_price)*($diycurrency['currency_'.$type.'_fee']/100);
            }
            $tradeid=$this->addTrade($order['member_id'], $order['currency_id'], $order['currency_trade_id'],$trade_price, $trade_num, $order['type'],$coin);

            if($tradeid>0)
            {
                $res[]=true;
            }else{
                $res[]=false;
            }
            
           
            $partrade=$this->addTrade($trade_order['member_id'], $trade_order['currency_id'], $trade_order['currency_trade_id'], $trade_price, $trade_num, $trade_order['type'],$feenumber);
            if($partrade>0)
            {
                $res[]=true;
            }else{
                $res[]=false;
            }
            if($pardivfee==true)
            {
                $partyfee=$trade_num*($pardivfee['currency_'.$trade_type.'_fee']/100);//存在自定义的手续费设置
            }else{
                $partyfee=$trade_num*$price*($diycurrency['currency_'.$trade_type.'_fee']/100);
            }
            $this->meFee($order['member_id'],$coin,$trade_order['member_id'],$type,$tradeid);//添加我的手续费分利记录
            $this->meFee($trade_order['member_id'],$feenumber,$trade_order['member_id'],$trade_type,$partrade);
        }
        // die;
        $num =$num- $trade_num;
        if ($num>0){
            //递归
           $r[]= $this->trade($currencyId, $type, $num, $price,$diycurrency);
        }
        return $r;
        
    }
    /**
    ***添加主动操作者的手续返利
    * @param int $memember 用户编号（数据表 yang_member）
    * @param float $fee    手续费    
    * @param int $partymemberid 产生手续费的用户编号 
    * @param Staring $type 动作类型
    * @param int $order_id 产生分利的订单编号
    * @param Array $diycurrency 币种信息
    **/
    private function meFee($memember,$fee,$partymemberid,$type,$order_id)
    {
        //我的手续费
        $rebate=$this->invitRebate($fee,$type);//获取获得手续费
        $rebatetype=$this->getOrderType($type);//获取交易类型
        $member_id=$this->getByUpUser($memember);//获取上级用户
        if($member_id==false)
        {
            //没有上级用户
            $config=M('config')->where(['key'=>'invit_member_id'])->field('value')->find();//获取默认的返利用户
            $member_id=$config['value'];
        }
        $map=array();
        $map['order_type']=$type;
        $map['member_id']=$member_id;
        $map['member_id_bottom']=$memember;
        $map['ctime']=$_SERVER['REQUEST_TIME'];
        $map['order_id']=$order_id;
        $map['cfee']=$fee;//手续费
        $map['rebate']=$rebate;//返利值
        $map['rebatetype']=$rebatetype;
        $invit=M('invit');
        $invit->add($map);
        // exit;
    }
    /***
    ******获取上级用户编号******
    * @param $memberId int 用户编号
    */
    private function getByUpUser($memberId)
    {
        $map=array();
        $map['invit_time']=array('EGT',time());
        $map['member_id']=$memberId;
        $user=M('member')->where($map)->field('pid')->find();
        return $user['pid'];
    }
    /**计算返利额数
    * @param float $fee 总手续费
    * @param String $type 交易类型
    * 
    **/
    private function invitRebate($fee,$type)
    {
        $config=M('config');
        $rebate=$config->where(['key'=>'invit_onerebate'])->find();
        $res=$fee*($rebate['value']/100);
        //返回计算的手续费结果
        return $res;

    }
    /**
    ******交易类型获取********
    * @param String $type 交易类型（买/卖）
    * 买 $type=buy
    * 卖 $type=sell
    ****/
    private function getOrderType($type)
    {
        //返利类型:1币；2账户余额
        $status=null;
        switch ($type) {
            case 'sell':
                $status=2;
                break;
            case 'buy':
                $status=1;
                break;

        }
        return $status;
    }
    public function partyFee()
    {
        //对方手续费
    }
    /**
     * 返回一条挂单记录
     * @param int $currencyId 币种id
     * @param float $price 交易价格
     * @return array 挂单记录
     */
    private function getOneOrders($type,$currencyId,$price){
        switch ($type){
            case 'buy':$gl='egt';$order='price desc,add_time asc'; break;
            case 'sell':$gl='elt'; $order='price asc,add_time asc';break;
        }
        $where['currency_id']=$currencyId;
        $where['type']=$type;
        $where['price']=array($gl,$price);
        $where['status']=array('in',array(0,1));
        return M('Orders')->where($where)->order('add_time desc')->order($order)->find();
    }
    /**
     * 返回用户第一条未成交的挂单
     * @param int $memberId 用户id
     * @param int $currencyId 币种id
     * @return array 挂单记录
     */
    private function getFirstOrdersByMember($memberId,$type,$currencyId){
        $where['member_id']=$memberId;
        $where['currency_id']=$currencyId;
        $where['type']=$type;
        $where['status']=array('in',array(0,1));
        return M('Orders')->where($where)->order('add_time desc')->find();
    }
    /**
     *  返回指定价格排序的订单
     * @param char $type  buy sell
     * @param float $price   交易价格
     * @param char $order 排序方式
     */
    private function getOrdersByPrice($currencyId,$type,$price,$order){
        switch ($type){
            case 'buy': $gl='elt';break;
            case 'sell': $gl='egt';break;
        }
        $where['currency_id']=$currencyId;
        $where['price']=array($gl,$price);
        $where['status']=array('in',array(0,1));
        return  M('Orders')->where($where)->order("price  $order")->select();
    }


    /**
     * 增加交易记录
     * @param unknown $member_id
     * @param unknown $currency_id
     * @param unknown $currency_trade_id
     * @param unknown $price
     * @param unknown $num
     * @param unknown $type
     * @return boolean
     */
    private function  addTrade($member_id,$currency_id,$currency_trade_id,$price,$num,$type,$fee){
        // switch ($type){
        // 	case 'buy':$fee=$fee;break;
        // 	case 'sell':$fee=$price*$num*$fee;break;
        // 	default:$fee=0;
        // }
        $this->dividend($price*$num,$member_id);
        $data=array(
            'member_id'=>$member_id,
            'currency_id'=>$currency_id,
            'currency_trade_id'=>$currency_trade_id,
            'price'=>$price,
            'num'=>$num,
            'fee'=>$fee,
            'money'=>$price*$num,
            'type'=>$type,
        );
        if (D('Trade')->create($data)){
            $res=D('Trade')->add();
            if ($res){
                return $res;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }


}