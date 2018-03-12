<?php
namespace Home\Controller;
use Home\Controller\HomeController;
use Think\Page;
use Think\Upload;

class PayController extends HomeController {
	
    //空操作
    public function _initialize(){
        parent::_initialize();
    }

    public function _empty(){
        header("HTTP/1.0 404 Not Found");
        $this->display('Public:404');
    }
    public function index(){
        $this->display();
    }
    //人工充值AJAX处理方式
    public function rechargeByMan(){
    	$config=$this->config;
    	$member=$this->member;
        $where['member_id'] = $member['member_id'];
        $count = M('Pay')->where($where)->count();
        $data['num']=$count+1;

    	$data['member_name']=I('post.member_name');
    	$data['money']=intval(I('post.money'));
    	$data['account']='人工充值';
    	
        $data['count']=I('post.count');
    	$data['type']=I('post.type');	
    	if(empty($data['member_name'])||empty($data['money'])||empty($data['type'])){
    		$arr['info']='请填写全部信息';
    		$arr['status']=0;
    		$this->ajaxReturn($arr);
    	}
    	if($member['status']!=1){
    		$arr['info']='请完成实名验证再进行充值';
    		$arr['status']=0;
    		$this->ajaxReturn($arr);
    	}
    	// if(strlen($data['account'])<11||strlen($data['account'])>20){
    	// 	$arr['info']='请输入正确的银行卡号或支付宝账号';
    	// 	$arr['status']=0;
    	// 	$this->ajaxReturn($arr);
    	// }
    	if($data['money']<$config['pay_min_money']){
    		$arr['info']="充值金额不能小于{$config['pay_min_money']}元";
    		$arr['status']=0;
    		$this->ajaxReturn($arr);
    	}
    	if($data['money']>$config['pay_max_money']){
    		$arr['info']="充值金额不能大于{$config['pay_max_money']}元";
    		$arr['status']=0;
    		$this->ajaxReturn($arr);
    	}
    	$data['member_id'] = session('USER_KEY_ID');
    	$data['add_time']=time();
        $data['pay_time']=time()+3600*24;
    	$data['status']=0;
    	$list=M('Pay')->add($data);
    	if($list){
    		$arr['info']="充值订单提交成功";
    		$arr['status']=1;
    		$arr['num']=$data['count'];
    		$this->ajaxReturn($arr);
    	}else{
    		$arr['info']="充值订单提交失败";
    		$arr['status']=0;
    		$this->ajaxReturn($arr);
    	}
    }

    //支付宝充值AJAX处理方式
    public function rechargeByzhifubao(){
        $config=$this->config;
        $member=$this->member;

        $where['member_id'] = $member['member_id'];
        $count = M('Pay')->where($where)->count();
        $data['num']=$count+1;

        $data['member_name']=I('post.member_name');
        $data['money']=I('post.money');
        $data['account']='支付宝';
        $data['count']=I('post.count');
        $data['type']=I('post.type');   
        if(empty($data['member_name'])||empty($data['money'])||empty($data['type'])){
            $arr['info']='请填写全部信息';
            $arr['status']=0;
            $this->ajaxReturn($arr);
        }
        if($member['status']!=1){
            $arr['info']='请完成实名验证再进行充值';
            $arr['status']=0;
            $this->ajaxReturn($arr);
        }
        // if(strlen($data['account'])<11||strlen($data['account'])>20){
        //  $arr['info']='请输入正确的银行卡号或支付宝账号';
        //  $arr['status']=0;
        //  $this->ajaxReturn($arr);
        // }
        if($data['money']<$config['pay_min_money']){
            $arr['info']="充值金额不能小于{$config['pay_min_money']}元";
            $arr['status']=0;
            $this->ajaxReturn($arr);
        }
        if($data['money']>$config['pay_max_money']){
            $arr['info']="充值金额不能大于{$config['pay_max_money']}元";
            $arr['status']=0;
            $this->ajaxReturn($arr);
        }
        $data['member_id'] = session('USER_KEY_ID');
        $data['add_time']=time();
        $data['pay_time']=time()+3600*24;
        $data['status']=0;
        $list=M('Pay')->add($data);

        //更改用户支付宝账户
        $ssss['member_id']  =session('USER_KEY_ID');
        $ssss['zhifubao']=I('post.member_name');
        M('Member')->save($ssss);

        if($list){
            $arr['info']="充值订单提交成功";
            $arr['status']=1;
            $arr['num']=$data['count'];
            $this->ajaxReturn($arr);
        }else{
            $arr['info']="充值订单提交失败";
            $arr['status']=0;
            $this->ajaxReturn($arr);
        }
    }
   
    //币充值页面
    public function bpay(){

    	$this->User_status();//判断是否需要进行信息补全
    	$id=I('currency_id');//货币id
    	if(empty($id)){
    		$this->error("请选择币种",U("User/index"));
    	}
    	$currency=$this->getCurrencyByCurrencyId($id);
    	
    	if(empty($currency)){
    		$this->error("无效币种请联系管理员",U("User/index"));
    	}
		$list=$this->getUserMoneyByCurrencyId($_SESSION['USER_KEY_ID'], $id);
		//设置充值地址
    	if(empty($list['chongzhi_url'])){
    		$address=$this->qianbao_new_address($currency);
    		$this->setCurrentyMemberByMemberId($_SESSION['USER_KEY_ID'], $id, 'chongzhi_url', $address);
    		$list['chongzhi_url']=$address;
    	}
		

	
    
    	//充值页面
    	$where['user_id']=$_SESSION['USER_KEY_ID'];
    	$where['status']=array('in',array(2,3));
    	$where['currency_id']=$id;//货币id
    	import('ORG.Util.Page');// 导入分页类
    	$count      = M("Tibi")->where($where)->count();// 查询满足要求的总记录数
    	$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show       = $Page->show();// 分页显示输出
    	// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    	$chongzhi = M("Tibi")->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('chongzhi',$chongzhi);// 赋值数据集
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign("list",$list);
    	$this->assign("currency",$currency);//货币信息
    	$this->display();
    }
    
    
    //提币的页面
    public function tcoin(){

    	$this->User_status();//判断是否需要进行信息补全
    	$cuid=I('currency_id');
    	$list=M("Qianbao_address")->where("user_id='{$_SESSION['USER_KEY_ID']}' and currency_id = '$cuid'")->find();
    	$this->assign("list",$list);
    	$currency=$this->getCurrencyByCurrencyId($cuid);
    	if(empty($currency)){
    		$this->error("错误操作，请联系管理员",U('User/index'));
    	}
    	
    	$where['user_id']=$_SESSION['USER_KEY_ID'];
    	$where['status']=array('in',array(0,1));
    	import('ORG.Util.Page');// 导入分页类
    	$count      = M("Tibi")->where($where)->count();// 查询满足要求的总记录数
    	$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show       = $Page->show();// 分页显示输出
    	// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    	$tibi_list = M("Tibi")->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign("tibi_list",$tibi_list);
    	$this->tibi_save($currency);
    	 
    	$cuser=M("Currency_user")->where("currency_id='$cuid' and member_id='{$_SESSION['USER_KEY_ID']}'")->find();
    	$member=M("member")->where("member_id='{$_SESSION['USER_KEY_ID']}'")->find();
        if($member['shenhestatus']!=1)
        {
            $this->error("提币必须完成高级实名认证才能操作",U('Home/Safe/shenfen'));
        }
		$this->assign("cuser",$cuser);//个人货币数量
    	$this->assign("member",$member);
    	$this->assign("cuid",$currency);//货币名称
    	$this->display();
    }
    /**
     * 修改提币记录
     */
    private function tibi_save($currency){
    	$where['user_id']=$_SESSION['USER_KEY_ID'];
    	$where['status']=0;
    	$list=M("Tibi")->where($where)->select();
    	if(!empty($list)){
    		foreach ($list as $k=>$v){
    			$x="";
    			$data="";
    			$x=$this->chakan_tibi_jilu($v['ti_id'],$currency);
    			if($x['confirmations']>2){
    				$data['check_time']=$x['time'];
    				$data['status']=1;
    				M("Tibi")->where("id='{$v['id']}'")->save($data);
    			}
    		}
    	}
    }
    /**
     *
     * 查看提币记录
     *
     * @param unknown $tid 提币ti_id
     * @return unknown
     */
    private function chakan_tibi_jilu($tid,$currency){
    	require_once 'App/Common/Common/easybitcoin.php';
    	$bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
//     	$result = $bitcoin->getinfo();
    	$list= $bitcoin->gettransaction($tid);
    	return $list;
    }
    
    /**
     * 提币方法
     */
    
    public function ti_bi(){


    	 $cuid=I("post.currency_id");//货币id

        

    	 $currency=M("Currency")->where("currency_id='$cuid'")->find();//这个是货币
    	 
         if($_POST['code']!=$_SESSION['code']){
    	 	$arr['status']=11;
    	 	$arr['info']="验证码不正确";
    	 	$this->ajaxReturn($arr);exit;
    	 }
    	 if(empty($currency)){
    	 	$arr['status']=10;
    	 	$arr['info']="无效货币，无法提币";
    	 	$this->ajaxReturn($arr);exit;
    	 }
    
    	if(empty($_POST['num'])){
    		$arr['status']=2;
    		$arr['info']="请填写提币数量";
    		$this->ajaxReturn($arr);exit;
    	}
    	if($_POST['num']<=0.0001){
    		$arr['status']=3;
    		$arr['info']="请填写大于0.0001的数量";
    		$this->ajaxReturn($arr);exit;
    	}
    	if($_POST['num']>$currency['currency_all_tibi']){
    		$arr['status']=4;
    		$arr['info']="已超出最大限制";
    		$this->ajaxReturn($arr);exit;
    	}
    	$num=floatval($_POST['num']);
    	if(empty($_POST['paypwd'])){
    		$arr['status']=5;
    		$arr['info']="请填写支付密码";
    		$this->ajaxReturn($arr);exit;
            
    	}
    	$user=M("Member")->where("member_id='{$_SESSION['USER_KEY_ID']}'")->find();

        if($user['shenhestatus']!=1)
        {
            $arr['status']=20;
            $arr['info']="请到安全中心完成高级实名认证";
            $this->ajaxReturn($arr);exit;
        }
    	if($user['pwdtrade']!=md5($_POST['paypwd'])){
    		$arr['status']=6;
    		$arr['info']="支付密码错误";
    		$this->ajaxReturn($arr);exit;
    	}
    	//判断是否已经添加提币地址
    	$list=M("Qianbao_address")->where("user_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$cuid'")->find();
    	 
    	if(empty($list)){
    		$arr['status']=7;
    		$arr['info']="请添加提币地址";
    		$this->ajaxReturn($arr);exit;
    	}
    	//判断看这个钱包地址是否是真实地址
    	if(!$this->check_qianbao_address($list['qianbao_url'],$currency)){
    		$arr['status']=8;
    		$arr['info']="提币地址不是一个有效地址";
    		$this->ajaxReturn($arr);exit;
    	}
    	//判断账户余额够不够
    	$money=M("Currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$cuid'")->find();
    	if($money['num']<$num){
    		$arr['status']=10;
    		$arr['info']="账户余额不足，无法提币";
    		$this->ajaxReturn($arr);exit;
    	}
    	if(!empty($this->config['tcoin_fee'])){
    		$actual=$num*(1-$this->config['tcoin_fee']/100);//计算出扣除手续费后的价格
    	}else{
    		$actual=$num;
    	}
    	$actual=(float)$actual;//实际到账
	
		
			$data['fee']=$this->config['tcoin_fee'];//手续费
			$data['currency_id']=$cuid;
			$data['user_id']=$_SESSION['USER_KEY_ID'];
			$data['url']=$list['qianbao_url'];
			$data['name']=$list['name'];
			$data['num']=$num;
			$data['actual']=$actual;//实际到账价格
			
			$data['add_time']=time();
		
    	
    	//$qb=M("Currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$cuid'")->find();
    	//$tibi=$this->qianbao_tibi($list['qianbao_url'],$actual,$currency,$qb['chongzhi_url']);//提币程序
    	 
		    $data['status']=0;//提币待审核 1审核通过 4 审核不通过
			

            


    		$model=new \Think\Model();
            $model->startTrans();

    		$r1=$model->table("yang_tibi")->add($data);
    		//减钱操作
    		$r2=$model->table("yang_currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$cuid'")->setDec("num",$num);

            $record['member_id']=$_SESSION['USER_KEY_ID'];
            $record['currency_id']=$cuid;
            $record['num']=$num;
            $record['status']="dec";// 
            //添加帐户减钱记录
            $r3=$model->table("yang_tibirecord")->add($record);
            


            if($r1 && $r2 && $r3)
            {
                $model->commit();
                $arr['status']=1;
                $arr['info']="提币成功，请等待处理";
                $this->ajaxReturn($arr);exit;
            }
            else
            {
                $model->rollback();
                $arr['status']=0;
                $arr['info']="提币失败，请稍后再尝试";
                $this->ajaxReturn($arr);exit;

            }



    		
    
    
    	 
    	 
    }
    
    /**
     * 添加钱包提现地址
     */
    public function add_qianbao_address(){
    	$cuid=I("currency_id");//货币id
    	$currency=M("Currency")->where("currency_id = '$cuid'")->find();
    	if(empty($currency)){
    		$arr['status']=2;
    		$arr['info']="无效币种，无法添加提币地址";
    		$this->ajaxReturn($arr);exit;

    	}
    	if(empty($_POST['name'])){
    		$arr['status']=3;
    		$arr['info']="新地址姓名不能为空";
    		$this->ajaxReturn($arr);exit;
    	}
    	if(empty($_POST['address'])){
    		$arr['status']=4;
    		$arr['info']="新地址不能为空";
    		$this->ajaxReturn($arr);exit;
    	}
    	//检测钱包地址是否有效
    	$jiance=$this->check_qianbao_address($_POST['address'],$currency);
    	if(!$jiance){
    		$arr['status']=5;
    		$arr['info']="钱包地址错误，不是一个真实有效的钱包地址";
    		$this->ajaxReturn($arr);exit;
    	}
    	//检测地址是否已经存在
    	$where['qianbao_url']=$_POST['address'];
    	$re=M("Qianbao_address")->where($where)->find();
    	if(!empty($re)){
    		$arr['status']=6;
    		$arr['info']="此地址已经绑定，请核实真实地址";
    		$this->ajaxReturn($arr);exit;
    	}
    	//检查此人是否已经有地址
    	$uq=M("Qianbao_address")->where("user_id='{$_SESSION['USER_KEY_ID']}'")->find();
    	 
    	$data['currency_id']=$cuid;//货币id
    	$data['name']=$_POST['name'];
    	$data['qianbao_url']=$_POST['address'];
    	$data['add_time']=time();
    	$data['user_id']=$_SESSION['USER_KEY_ID'];
    	$data['status']=1;
    	if(empty($uq)){
    		$qa=M("Qianbao_address")->add($data);
    	}else{
    		$qa=M("Qianbao_address")->where("id='{$uq['id']}'")->save($data);
    	}
    
    	if($qa){
    		$arr['status']=1;
    		$arr['info']="添加成功";
    		$this->ajaxReturn($arr);exit;


    	}else{
    		$arr['status']=7;
    		$arr['info']="添加失败";
    		$this->ajaxReturn($arr);exit;
    	}
    }
    
    /**
     * 删除钱包地址
     */
    public function del_address(){
    	$id=I('id');
    	$cuid=I("cuid");
    	if(empty($id)){
    		$this->error("无效数据");
    	}
    	if(empty($cuid)){
    		$this->error("无效货币");
    	}
    	$where['id']=$id;//提币地址的id
    	$where['currency_id']=$cuid;//提币的币种id
    	$where['user_id']=$_SESSION['USER_KEY_ID'];
    	$qa=M("Qianbao_address")->where($where)->find();
    	if(empty($qa)){
    		$this->error("非法操作");
    	}
    	 
    	$re=M("Qianbao_address")->where($where)->delete();
    	if($re){
    		$this->success("删除成功",U('Pay/tcoin',array('currency_id'=>$cuid)));
    	}else{
    		$this->error("删除失败");
    	}
    }
    /**
     * 充值方法
     * @return boolean
     */
    
    public function chongzhi_function(){
    	//     	$where['status']=array("in",array(3));//1与3分别为 提币成功 与充值成功;
    	//     	$where['user_id']=$_SESSION['USER_KEY_ID'];
    	//     	$count = M("Tibi")->where($where)->count();
    	$id=I("currency_id");//货币id；
		$addr=I("address");//货币地址；
		
		


    	if(empty($id)){
    		return false;
    	}
    	$currency=M("Currency")->where("currency_id='$id'")->find();//这个是货币
    	if(empty($currency)){
    		return false;
    	}
    	
    	//如果货币不存在 直接返回
    	$currency=M("Currency")->where("currency_id='$id'")->find();
    	if(empty($currency)){
    		return false;
    	}
    	
		//同行充值记录处理

		
		
		if($currency['port_number']==23215)
		{
			
		$re=1;
			
		}
				
		
		//其他币充值处理
		else
		{
		
			$list=$this->trade_qianbao($_SESSION['USER_KEY_ID'],$currency);
			foreach ($list as $k=>$v){
				$data["currency_id"]=$currency['currency_id'];//货币id写入
				if($v['category']=='receive'){
					$data[]=array();
					$data['user_id']=$_SESSION['USER_KEY_ID'];
					$data['url']=$v['address'];//地址
					$data['name']=$v['account'];//标签
					$data['add_time']=$v['time'];//时间
					$data['num']=$v['amount'];//数量
					$tibi_txid=M("Tibi")->where("ti_id='{$v['txid']}'")->find();
					if(!empty($tibi_txid)){
						//如果已经存在  而且是已经完成状态 不处理直接跳出循环
						if($tibi_txid['status']==3){
							continue;
						}
						if($v['confirmations']>2){
							$data['status']=3;//3表示充值完成
							$data['check_time']=$v['timereceived'];//确认时间
							$re=M("Tibi")->where("ti_id='{$v['txid']}'")->save($data);//修改状态 表示已经完成
							M("Currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$id'")->setInc("num",$v['amount']);//给User表加钱
						}
					}else{
						$data['ti_id']=$v['txid'];//写入交易id号
						if($v['confirmations']>2){
							$data['status']=3;//3表示充值完成
							$data['check_time']=$v['timereceived'];//确认时间
							$re=M("Tibi")->add($data);//修改状态 表示已经完成
							M("Currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}'  and currency_id='$id' ")->setInc("num",$v['amount']);//给User表加钱
						}else{
							$data['status']=2;//2表示充值中
							$re=M("Tibi")->add($data);
						}
					}    
				}
			}
			
		}
		
		
		if($re){
    		$arr['status']=$test;
    		$this->ajaxReturn($arr);
    	}
    	 
    	 
    }
     
    
    /**
     * 提币引用的方法
     * @param unknown $url 钱包地址
     * @param unknown $money 提币数量
     * 
     * 需要加密 *********************
     */
    private function qianbao_tibi($url,$money,$currency,$from_url){
    	require_once 'App/Common/Common/easybitcoin.php';
    	$bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
//     	$result = $bitcoin->getinfo();
        //$a=$bitcoin->validateaddress($url);
    	$bitcoin->walletlock();//强制上锁
    	$bitcoin->walletpassphrase($currency['qianbao_key'],30);
		$id=$bitcoin->sendtoaddress($url,$money);
    	$bitcoin->walletlock();
    	return $id;
    }
    
    
    /**
     * 查询某人的交易记录
     * @param unknown $user 用户名
     * @param unknown $count  从第几个开始查找
     * @return $list  返回此用户的交易列表
     */
    private function trade_qianbao($user,$currency){
    	require_once 'App/Common/Common/easybitcoin.php';
    	$bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
    	$result = $bitcoin->getinfo();
    	$list=$bitcoin->listtransactions($user,10,0);//$list=$bitcoin->listtransactions($user,10,0);
    	return $list;
		
    }
    
     
  
 
    
    /**
     * 获取新的一个钱包地址
     * @return unknown
     */
    private function qianbao_new_address($currency){
    	require_once 'App/Common/Common/easybitcoin.php';
    	$bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
    	$user=$_SESSION['USER_KEY_ID'];
		// if($currency['port_number']==23215)
		// {
			
		// 	$bitcoin->walletpassphrase($currency['qianbao_key'],20);
		// 	$address = $bitcoin->getnewaddress();
		// 	return $address['addr'];
		// }

		// else
		// {

		
  //   	$address = $bitcoin->getnewaddress($user);
		// return $address;
		// }
     
        switch ($currency['port_number'])
        {
        case 23215:
          $bitcoin->walletpassphrase($currency['qianbao_key'],20);
          $address = $bitcoin->getnewaddress();
          return $address['addr'];
          break;  
        case 10:
          Vendor('phpRPC.phprpc_client');
          $client = new \PHPRPC_Client(C('RPC_SERVER'));
          $result = $client->getnewaddress($user);
          return $result['result'];
          break;
        case 11:
          Vendor('phpRPC.phprpc_client');
          $client = new \PHPRPC_Client(C('ETC_RPC_SERVER'));
          $result = $client->getnewaddress($user);
          return $result['result'];
          break;

        default:
          $address = $bitcoin->getnewaddress($user);
          return $address;
        }   
    	
    }
    /**
     * 检测地址是否是有效地址
     *
     * @return boolean 如果成功返回个true
     * @return boolean 如果失败返回个false；
     *  @param unknown $url
     *  @param $port_number 端口号 来区分不同的钱包
     */
    private function check_qianbao_address($url,$currency){
    	
    	require_once 'App/Common/Common/easybitcoin.php';
 	    $bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
    	$address = $bitcoin->validateaddress($url);
		// if($currency['port_number']==23215)
		// {
		// 	if($address['ret']){
		// 		return true;
		// 	}else{
		// 		return false;
		// 	}
		// }
		// else
		// {
		// 	if($address['isvalid']){
		// 		return true;
		// 	}else{
		// 		return false;
		// 	}
		// }


        switch ($currency['port_number'])
        {
            case 23215:
                 if($address['ret']){
                 return true;
                 }else{
                     return false;
                 }
              break;  
            case 10:
              return true;
              break;
            default:
              if($address['isvalid']){
                     return true;
                 }else{
                     return false;
                 }

        } 
		
    	
    }


    public function tibi_shenhe($url,$money,$currency)
    {
       if(IS_POST)
       {
            $currency_id=I("post.currency_id");
            $url=I("post.url");
            $actual=I('post.actual');
            $status=I("post.status");
            $num=I("post.num");
            $id=I("post.id");
            $user_id=I("post.user_id");

            $currency=M('currency')->where('currency_id='.$currency_id)->find();
            if($status==1)//shenghe tongguo
            {



            switch ($currency['port_number']) {
                case '10'://eth
                    Vendor('phpRPC.phprpc_client');
                    $client = new \PHPRPC_Client(C('RPC_SERVER'));
                    
                    
                    break;
                case '23215'://tongxing
                    require_once 'App/Common/Common/easybitcoin.php';
                    $bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);

                    $bitcoin->walletlock();//强制上锁
                    $bitcoin->walletpassphrase($currency['qianbao_key'],30);
                    $hashid=$bitcoin->sendtoaddress($url,$money);//tongxing $id['hash']
                    $bitcoin->walletlock();
                              
                    break;
                default:
                    require_once 'App/Common/Common/easybitcoin.php';
                    $bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);

                    $bitcoin->walletlock();//强制上锁
                    $bitcoin->walletpassphrase($currency['qianbao_key'],30);
                    $hashid=$bitcoin->sendtoaddress($url,$money);//tongxing $id['hash']
                    $bitcoin->walletlock();
                    if($hashid)
                    {
                        $re['status']=1;
                        $re['ti_id']=$hashid;
                        $re['check_time']=time();

                        M('tibi')->where("id=".$id)->save($re);

                        $data['status']=1;
                        $data['info']="提币成功:default";
                        $this->ajaxReturn($data);
                        exit; 

                    }
                    else

                    {
                       $data['status']=0;
                       $data['info']="提币失败:default";
                       $this->ajaxReturn($data);
                        exit; 
                    }
                    break;
                        
                }

             }
             else//sheng he bu tong guo
             {

                //加币,返还用户
                $result=M("currency_user")->where("member_id='{$user_id}' and currency_id='$currency_id'")->setInc("num",$num);
                if($result)
                {
                    $data['status']=1;
                    $data['info']="退回成功";
                    $this->ajaxReturn($data);
                    exit;
                }

            }



            
       }
       
    }
	
	
}