<?php
namespace Mobile\Controller;
use Think\Controller;
/**
* 
*/
class LoginController extends Controller
{	
	public function index()
	{
		//登录首页
		$this->assign('title','用户登录');
		$this->display();
	}
	public function loginCheck()
    {
        if(IS_POST)
        {
            $email=I('post.email');
            $pwd=I('post.pwd');
            if(checkMobile($email))
            {
                //手机登录
                $user=M('member')->where(['phone'=>$email,'pwd'=>md5($pwd)])->find();
                if($user)
                {
                    if($user['is_lock'])
                    {
                        $this->ajaxReturn(['code'=>1,'error'=>'用户被锁定，请联系客服']);
                    }else{
                        session('USER_KEY_ID',$user['member_id']);
                        session('USER_KEY',$user['email']);//用户名
                        session('STATUS',$user['status']);//用户状态
                        $this->ajaxReturn(['code'=>0]);
                    }
                }else{
                    $this->ajaxReturn(['code'=>1,'error'=>'用户名或密码错误！']);
                }
            }else{
                //邮箱登录
                $user=M('member')->where(['email'=>$email,'pwd'=>md5($pwd)])->find();
                if($user)
                {
                    if($user['is_lock'])
                    {
                        $this->ajaxReturn(['code'=>1,'error'=>'用户被锁定，请联系客服']);
                    }else{
                        session('USER_KEY_ID',$user['member_id']);
                        session('USER_KEY',$user['email']);//用户名
                        session('STATUS',$user['status']);//用户状态
                        $this->ajaxReturn(['code'=>0]);
                    }
                }else{
                    $this->ajaxReturn(['code'=>1,'error'=>'用户名或密码错误！']);
                    }
                }
            }
    }
}