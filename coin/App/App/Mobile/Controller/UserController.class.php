<?php
namespace Mobile\Controller;
use Common\Controller\CommonController;
/**
* 2017/7/18
*手机入口控制器
*/
class UserController extends CommonController
{
	 public function _initialize(){
 		parent::_initialize();
 		if (empty($_SESSION['USER_KEY_ID'])) {
 			$this->redirect('/Mobile/Login/index', 3, '未登陆,请登陆!');
 		}
 		$this->assign('session',$_SESSION['USER_KEY_ID']);
 	}
	public function jiaoyi()
	{
        $where['member_id']=$_SESSION['USER_KEY_ID'];
        $currency_user=M('Currency_user')
            ->join("left join ".C('DB_PREFIX')."currency on ".C('DB_PREFIX')."currency.currency_id=".C('DB_PREFIX')."currency_user.currency_id")
            ->field(''.C('DB_PREFIX').'currency_user.*,('.C('DB_PREFIX').'currency_user.num+'.C('DB_PREFIX').'currency_user.forzen_num) as count,'.C('DB_PREFIX').'currency.currency_name,'.C('DB_PREFIX').'currency.currency_mark,'.C('DB_PREFIX').'currency.currency_logo')
            ->where($where)->order('sort')->select();
        $allmoneys = null;
        foreach ($currency_user as $k=>$v){
            $Currency_message=$this->getCurrencyMessageById($v['currency_id']);
            $allmoney=$currency_user[$k]['count']*$Currency_message['new_price'];
            $allmoneys+=$allmoney;
        }
        $member_rmb=$this->member;
        $allmoneys=$allmoneys+$member_rmb['count'];

        $u_info = M('Member')->field('rmb,forzen_rmb')->where($where)->find();
        $this->assign('u_info',$u_info);
        $this->assign('allmoneys',$allmoneys);
        $this->assign('currency_user',$currency_user);
        $this->assign('h_status',2);  //头部变蓝状态
        $this->display();
	}

    public function pay(){
        $config=$this->config;
        $member=$this->member;
        $order_num=$this->getPaycountByName($member['name']);
        //随机数
        $num =  0.01*rand(10,99);
        $fee=floatval($config['pay_fee']+0.01*$order_num+$num);
        //支付表
        //$where['member_name']=$this->member['name'];
        $where['member_id']=$this->member['member_id'];
        $where['type'] = array('NEQ',3);
        //分页
        $pay = M('Pay'); // 实例化User对象
        $count = $pay->where($where)->count();// 查询满足要求的总记录数
        $Page  = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $list=$pay->where($where)->order('pay_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        // foreach ($list as $k=>$v){
        //     $list[$k]['status']=payStatus($v['status']);
        // }
        
        $bank = M('Website_bank')->where('status = 1')->select();
        //充值说明
        $art=M('Article')->where('article_id=102')->find();
        $art['content']=html_entity_decode($art['content']);
        $this->assign('art',$art);
        $this->assign('page',$show);
        $this->assign('bank',$bank);
        $this->assign('list',$list);
        $this->assign('fee',$fee);
        $this->assign('h_status',2);  //头部变蓝状态
        $this->display();
    }	

	//获取会员有多少人工充值订单
	public function getPaycountByName($name){
		$list=M('Pay')->where("member_name='".$name."'")->count();
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	public function memberCenter()
    {
        //会员中心首页
        $uinfo=M('member')->where(['member_id'=>session('USER_KEY_ID')])->find();//获取会员信息
        $this->assign('session','true');
        $this->assign('userinfo',$uinfo);
        $this->assign('h_status',3);  //头部变蓝状态
        $this->display();
    }
    public function passwd()
    {
        //修该密码
        $this->assign('h_status',3);  //头部变蓝状态
        $this->display();
    }
    public function ulp()
    {
        //update login password 更新密码
        if(IS_AJAX)
        {
            $map=array();
            $passwd=I('post.pwd');//获取登录密码
            if($passwd)
            {
                $memberobj=M('member');//实例化member数据对象
                $where=array();
                $where['pwd']=md5($passwd);
                $where['phone']=session('user.phone');
                if($memberobj->where($where)->find())
                {
                    $newpwd=I('post.newpwd');
                    if($newpwd)
                    {
                        $rpwd=I('post.rpwd');
                        if($rpwd == $newpwd)
                        {

                            if($memberobj->where($where)->save(['pwd'=>md5($newpwd)]))
                            {
                                session(null);
                                $this->ajaxReturn(['code'=>0,'error'=>'修改成功！']);
                            }else{
                                $this->ajaxReturn(['code'=>0,'error'=>'修改失败,请咨询客服人员']);
                            }
                        }else{
                            $this->ajaxReturn(['code'=>1,'error'=>'新密码与确认密码不相同']);
                        }
                    }else{
                        $this->ajaxReturn(['code'=>1,'error'=>'新密码不能为空']);
                    }
                    $this->ajaxReturn(['code'=>0]);
                }else{
                    $this->ajaxReturn(['code'=>1,'error'=>'登录密码不正确']);
                }
            }
        }else{
            die();
        }
    }
    public function tradepasswd()
    {
        //修改交易密码
        $this->assign('h_status',3);  //头部变蓝状态
        $this->display();
    }
    public function utp()
    {
        //update transaction password 修改交易密码
        if(IS_AJAX)
        {
            $map=array();
            $passwd=I('post.pwd');//获取登录密码
            if($passwd)
            {
                $memberobj=M('member');//实例化member数据对象
                $where=array();
                $where['pwdtrade']=md5($passwd);
                $where['phone']=session('user.phone');
                if($memberobj->where($where)->find())
                {
                    $newpwd=I('post.newpwd');
                    if($newpwd)
                    {
                        $rpwd=I('post.rpwd');
                        if($rpwd == $newpwd)
                        {

                            if($memberobj->where($where)->save(['pwdtrade'=>md5($newpwd)]))
                            {
                                session(null);
                                $this->ajaxReturn(['code'=>0,'error'=>'修改成功！']);
                            }else{
                                $this->ajaxReturn(['code'=>0,'error'=>'修改失败,请咨询客服人员']);
                            }
                        }else{
                            $this->ajaxReturn(['code'=>1,'error'=>'新密码与确认密码不相同']);
                        }
                    }else{
                        $this->ajaxReturn(['code'=>1,'error'=>'新密码不能为空']);
                    }
                    $this->ajaxReturn(['code'=>0]);
                }else{
                    $this->ajaxReturn(['code'=>1,'error'=>'交易密码不正确']);
                }
            }
        }else{
            die();
        }
    }
    public function logout()
    {
        //退出登录
        session(null);
        $this->redirect('Login/index');
    }    
}