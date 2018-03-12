<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
class CurrencyController extends AdminController {
	protected $currency;
	public function _initialize(){
		parent::_initialize();
		$this->currency=M('Currency');
	}
	//空操作
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
	
	/**
	 * 提币记录
	 */
	public function tibi_index(){
		
		$cuid=I('cuid');
		$email=I('email');
		if(!empty($cuid)){
			$where[C("DB_PREFIX")."tibi.currency_id"]=$cuid;
			$this->assign("id",$cuid);
		}
		if(!empty($email)){
			$name=M("Member")->where("email='{$email}'")->find();
			$where[C("DB_PREFIX")."tibi.user_id"]=$name['member_id'];
		}
		$where[C("DB_PREFIX")."tibi.status"]=array("in",array(0,1));
		
		$field=C("DB_PREFIX")."tibi.*,".C("DB_PREFIX")."member.phone,".C("DB_PREFIX")."currency.currency_name";
		$count      = M("Tibi")->where($where)->join(C("DB_PREFIX")."member on ".C("DB_PREFIX")."tibi.user_id=".C("DB_PREFIX")."member.member_id")
		->join(C("DB_PREFIX")."currency on ".C("DB_PREFIX")."tibi.currency_id=".C("DB_PREFIX")."currency.currency_id")->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		//给分页传参数
		setPageParameter($Page, array('cuid'=>I("cuid"),'email'=>$name['member_id']));
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = M("Tibi")->field($field)->where($where)->join(C("DB_PREFIX")."member on ".C("DB_PREFIX")."tibi.user_id=".C("DB_PREFIX")."member.member_id")
		->join(C("DB_PREFIX")."currency on ".C("DB_PREFIX")."tibi.currency_id=".C("DB_PREFIX")."currency.currency_id")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
		
		//读取币种表
		
		$curr=M("Currency")->select();
		$this->assign("curr",$curr);
		
		$this->display();
	}
	
	/**
	 * 充值记录
	 */
	public function chongzhi_index(){
		$cuid=I('cuid');
		$email=I('email');
	
		if(!empty($cuid)){
			$where[C("DB_PREFIX")."tibi.currency_id"]=I("cuid");
			$this->assign("id",I("cuid"));
		}
		if(!empty($cuid)){
			$name=M("Member")->where("email='{$email}'")->find();
			$where[C("DB_PREFIX")."tibi.user_id"]=$name['member_id'];
		}
		$where[C("DB_PREFIX")."tibi.status"]=array("in",array(2,3));
			
		$field=C("DB_PREFIX")."tibi.*,".C("DB_PREFIX")."member.email,".C("DB_PREFIX")."currency.currency_name";
		
		$count      = M("Tibi")
		->where($where)
		->join(C("DB_PREFIX")."member on ".C("DB_PREFIX")."tibi.user_id= ".C("DB_PREFIX")."member.member_id")
		->join(C("DB_PREFIX")."currency on ".C("DB_PREFIX")."tibi.currency_id=".C("DB_PREFIX")."currency.currency_id")->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		//给分页传参数
		setPageParameter($Page, array('cuid'=>$cuid,'email'=>$email));
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = M("Tibi")->field($field)->where($where)->join(C("DB_PREFIX")."member on ".C("DB_PREFIX")."tibi.user_id=".C("DB_PREFIX")."member.member_id")
		->join(C("DB_PREFIX")."currency on ".C("DB_PREFIX")."tibi.currency_id=".C("DB_PREFIX")."currency.currency_id")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
	
	
		//读取币种表
	
		$curr=M("Currency")->select();
		$this->assign("curr",$curr);
	
		$this->display();
	}
	
	Public function add(){
		if(IS_POST){
			if(!empty($_POST['currency_id'])){
				$cu=M("Currency")->where("currency_name='{$_POST['currency_name']}' and currency_id <> '{$_POST['currency_id']}'")->find();
			}else{
				$cu=M("Currency")->where("currency_name='{$_POST['currency_name']}'")->find();
			}
			if(!empty($cu)){
				$this->error("币种名称已经存在");
			}
			
			foreach ($_POST as $k=>$v){
				$data[$k]=$v;
			}
			$data['add_time']=time();
			if($_FILES["Filedata"]["tmp_name"]){
				$data['currency_logo']=$this->upload($_FILES["Filedata"]);
			}
			
			if(!empty($_POST['currency_id'])){
				$rs=$this->currency->save($data);
				$currency_id=$data['currency_id'];
			}else{
				$rs=$this->currency->add($data);
				$currency_id=$rs;
			}
			if($_FILES["pic"]["tmp_name"]){
				$te['pic']=$this->upload($_FILES["pic"]);
				$te['currency_id']=$currency_id;
				$te['add_time']=time();
				$te['status']=0;
				M('Currency_pic')->add($te);
			}
			if($rs){
				$this->success("操作成功");
	    	}else{
	    		$this->error('操作失败');
	    	}
		}else{
			if(!empty($_GET['currency_id'])){
				$list=$this->currency->where('currency_id='.$_GET['currency_id'])->find();
				$this->assign('list',$list);
				$currency_pic=M('Currency_pic')->where('currency_id='.$_GET['currency_id'])->order('add_time')->select();
				$this->assign('pic',$currency_pic);
			}
			
			$currency_currency=M("Currency")->where("currency_id <> '{$_GET['currency_id']}'")->select();
			$this->assign("currency_currency",$currency_currency);
			
			$this->display();
		}
	}
	public function index(){
		$list=$this->currency->select();
		foreach ($list as $k=>$v){
			$list[$k]['qianbao_balance']=$this->get_qianbao_balance($v);
		}
		$this->assign('list',$list);
		$this->assign('empty','暂无数据');
		$this->display();
    }

    public function newbi(){
        $list=M('Newbi')->select();
        $this->assign('list',$list);
        $this->assign('empty','暂无数据');
        //var_dump($list);
        $this->display();
    }

    public function add_newbi(){
        if(IS_POST){
                if ($_FILES["Filedata"]['tmp_name']) {
                    $data['currency_logo'] = $this->upload($_FILES["Filedata"]);
                }
                if ($_FILES["upload_pic1"]['tmp_name']) {
                    $data['pic1'] = $this->upload($_FILES["upload_pic1"]);
                }
                if ($_FILES["upload_pic2"]['tmp_name']) {
                    $data['pic2'] = $this->upload($_FILES["upload_pic2"]);
                }
                if ($_FILES["upload_pic3"]['tmp_name']) {
                    $data['pic3'] = $this->upload($_FILES["upload_pic3"]);
                }
                if ($_FILES["upload_pic4"]['tmp_name']) {
                    $data['pic4'] = $this->upload($_FILES["upload_pic4"]);
                }
                if ($_FILES["upload_pic5"]['tmp_name']) {
                    $data['pic5'] = $this->upload($_FILES["upload_pic5"]);
                }
                if ($_FILES["upload_pic6"]['tmp_name']) {
                    $data['pic6'] = $this->upload($_FILES["upload_pic6"]);
                }
                $data['currency_name'] = I('post.currency_name');
                $data['currency_mark'] = I('post.currency_mark');
                $data['en_name']       = I('post.en_name');
                $data['atter']         = I('post.atter');
                $data['currency_content'] = I('post.currency_content');
                $data['web_url']       = I('post.web_url');
                $data['bbs_url']       = I('post.bbs_url');
                $data['pay_url']       = I('post.pay_url');
                $data['uix_url']       = I('post.uix_url');
                $data['developer']     = I('post.developer');
                $data['q_time']        = I('post.q_time');
                $data['pubdate']       = I('post.pubdate');
                $data['core']          = I('post.core');
                $data['currency_all_num']= I('post.currency_all_num');
                $data['status']        = I('post.status');
                $data['add_time']       = time();
                if (!empty($_POST['currency_id'])) {
                    $data['currency_id'] = $_POST['currency_id'];
                    $rs=M('Newbi')->save($data);
                }else{
                    $rs=M('Newbi')->add($data);
                }
                if ($rs) {
                    $this->success('添加成功！',U('Currency/newbi'));
                }else{
                    $this->error('添加失败！');
                }
        }else{
            if($_GET['currency_id']){
                $list=M('Newbi')->where('currency_id='.$_GET['currency_id'])->find();
                $this->assign('list',$list);
            }
            $this->display();
        }
    }

    public function shangxian(){
    	if(!empty($_GET['currency_id'])){
    		$rs=M('Currency')->where('currency_id='.$_GET['currency_id'])->setField('is_lock',0);
    	}
    	if($rs){
    		$this->success('操作成功');
    	}else{
    		$this->error('操作失败');
    	}
    }
    //删除的时候，要判断个人账户下有没有钱
    public function del(){
    	if(empty($_GET['currency_id'])){
    		$this->error('删除数据不存在');
    	}
    	$rs=M('Currency_user')->where('currency_id='.$_GET['currency_id'])->sum("num");
    
    	if($rs>0){
    		$this->error('该币种尚有用户持有，不能删除');
    	}
    
    	$list=$this->currency->where('currency_id='.$_GET['currency_id'])->delete();
    	M('Currency_user')->where('currency_id='.$_GET['currency_id'])->delete();
    	if($list){
    		$this->success('删除成功');
    	}else{
    		$this->error('删除失败');
    	}
    }

    public function del_newbi(){
        if(empty($_GET['currency_id'])){
            $this->error('删除数据不存在');
        }
    
        $list=M('Newbi')->where('currency_id='.$_GET['currency_id'])->delete();
        if($list){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //删除对应的币种详情图片
    public function delCurrencyPic(){
    	$id=I('get.id');
    	$list=M('Currency_pic')->where('id='.$id)->delete();
    	if($list){
    		$this->success('删除成功');
    	}else{
    		$this->error('删除失败');
    	}
    }
    
 
    
    /**
     * 给某个用户钱包转账
     */
    public function  set_member_currencyForQianbao(){
    	$cuid=intval(I("cuid"));
    	if(empty($cuid)){
    		$this->error("无效货币参数",U("Currency/index"));exit();
    	}
    	$currency=M("Currency")->where("currency_id='$cuid'")->find();
    	
    	$currency['balance']=$this->get_qianbao_balance($currency);
    
    	if(empty($currency)){
    		$this->error("无效货币",U("Currency/index"));exit();
    	}
    	if(IS_POST){
    	
    		$admin=M("Admin")->where("admin_id='{$_SESSION['admin_userid']}'")->find();
    		if(empty($_POST['password'])){
    			$this->error("请输入管理员密码");
    		}
    		if(md5($_POST['password'])!=$admin['password']){
    			$this->error("您输入的管理员密码错误");
    		}
    		
    		$username=I("name");//用户名
    		$num=I('num');//数量
    		if(empty($username)){
    			$this->error("请输入用户名");exit();
    		}
    		if(empty($num)||!is_numeric($num)){
    			$this->error("数量请输入数字类型");exit();
    		}
    		$member=M("Member")->where("email='$username'")->find();
    		if(empty($member)){
    			$this->error("查无此人，请核实");exit();
    		}
    		
    		$qa=M("Qianbao_address")->where("user_id='{$member['member_id']}' and currency_id='{$cuid}'")->find();
    		if(empty($qa['qianbao_url'])){
    			$this->error("此用户没有绑定提币地址，无法转账");exit();
    		}
    		//判断看这个钱包地址是否是真实地址
    		if(!$this->check_qianbao_address($qa['qianbao_url'],$currency)){
    		$this->error("提币地址不是一个有效地址");exit();
    		}
    		$num=floatval($num);
    		$data['fee']=0;//手续费
    		$data['currency_id']=$cuid;
    		$data['user_id']=$qa['user_id'];
    		$data['url']=$qa['qianbao_url'];
    		$data['name']=$qa['name'];
    		$data['num']=$num;
    		$data['actual']=$num;//实际到账价格
    		$data['status']=0;
    		$data['add_time']=time();
    		
    		$tibi=$this->qianbao_tibi($qa['qianbao_url'],$num,$currency);//提币程序
    		
    		if($tibi){//成功写入数据库
    			$data['ti_id']=$tibi;
    			$re=M("Tibi")->add($data);
    			//减钱操作
//     			M("Currency_user")->where("member_id='{$_SESSION['USER_KEY_ID']}' and currency_id='$cuid'")->setDec("num",$num);
    			$this->success("转账成功，请耐心等待",U('Currency/index'));exit();
    		
    		}else{//失败提示
    			$this->error("转账失败");exit();
    		}
    	}
    	
    	$this->assign("currency",$currency);
    	$this->display();
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
    	if($address['isvalid']){
    		return true;
    	}else{
    		return false;
    	}
    }
    /**
     * 提币引用的方法
     * @param unknown $url 钱包地址
     * @param unknown $money 提币数量
     */
    private function qianbao_tibi($url,$money,$currency){
     	require_once 'App/Common/Common/easybitcoin.php';
    $bitcoin = new \Bitcoin($currency['rpc_user'],$currency['rpc_pwd'],$currency['rpc_url'],$currency['port_number']);
    $bitcoin->walletlock();//强制上锁
    $bitcoin->walletpassphrase($currency['qianbao_key'],20);
    $id=$bitcoin->sendtoaddress($url,$money);
    $bitcoin->walletlock();
    	return $id;
    }
    /**
     * 修改涨停跌停价格
     */
    public function savePrice(){
    	$currency_id = trim(I('id','','intval'));
    	if(empty($currency_id)){
    		$data['status'] = -1;
    		$data['info'] = '参数错误';
    		$this->ajaxReturn($data);
    	}
    	if(!is_numeric($_POST['price_up']) || !is_numeric($_POST['price_down'])){
    		$data['status'] = -2;
    		$data['info'] = '填写必须是数字';
    		$this->ajaxReturn($data);
    	}
    	$r[] = M('Currency')->where("currency_id = {$currency_id}")->setField('price_up',$_POST['price_up']);
    	$r[] = M('Currency')->where("currency_id = {$currency_id}")->setField('price_down',$_POST['price_down']);
    	if(in_array($r, false)){
    		$data['status'] = -3;
    		$data['info'] = '服务器繁忙,请稍后重试';
    		$this->ajaxReturn($data);
    	}else{
    		$data['status'] = 1;
    		$data['info'] = '修改成功';
    		$this->ajaxReturn($data);
    	}
    }

     /**
     *同性充币
     */
    
    public function txcz()
    {
        if(IS_POST)
        {
            
             //防止重复提交 如果重复提交跳转至相关页面
            if (!checkToken($_POST['TOKEN'])) {
                 $this->error("重复提交");
                return;
            }

         
 
           $num=intval(I('post.num'));
           if($num<1)
           {
            $this->error("充值次数不能为空");
            return;
           }

            
            require_once 'App/Common/Common/easybitcoin.php';
            $bitcoin = new \Bitcoin('tongxingpoints','admin','127.0.0.1','23215');
            //$bitcoin=new \Bitcoin('jnxyl','jnxyl123','127.0.0.1','20167');
          

            $result=$bitcoin->listtx($num);

            $result=$result['ConfirmTx'];
            $arrlist=array();
            foreach ($result as $key => $value) {
                $data['ti_id']=$value;

                $re=M('tibi')->where($data)->find();
                if(!$re)
                {
                    $arrlist[$key]=$value;
                }
            }

            foreach ($arrlist as $key => $value) {

                $list=$bitcoin->gettxdetail($value);//23215

                $id=M('currency')->where('port_number=23215')->getField('currency_id');
                $userid=M("Currency_user")->where("chongzhi_url='".$list['desaddr']."'")->getField('member_id');

                $data["currency_id"]=$id;//货币id写入
                $data['user_id']=$userid;
                $data['url']=$list['desaddr'];//地址
                    
                $data['add_time']=$list['confirmedtime'];//时间
                $data['num']=$list['money']/100000000;//数量
                $data['status']=3;
                $data['check_time']=$list['confirmedtime'];
                $data['height']=$list['confirmHeight'];//写入height
                $data['ti_id']=$list['hash'];//写入height
                $num=M("Currency_user")->where("member_id='{$userid}' and currency_id='$id'")->getField('num');
                
                $re=M("Tibi")->add($data);
                if($re)
                {
                    M("Currency_user")->where("member_id='{$userid}' and currency_id='$id'")->setInc("num",$data['num']);//给User表加钱
                }
                else
                {
                    $this->error("执行失败,请稍候重试");
                    return;
                }
                
            }
            
                
            $this->success("执行成功"); 

               
           
           
            
          } 
        else
        {
            
           //创建token
            creatToken();
            
            
            $this->display();
            
                 
        
         }
    }
}