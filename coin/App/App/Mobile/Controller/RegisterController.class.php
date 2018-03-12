<?php
namespace Mobile\Controller;
use Think\Controller;
class RegisterController extends Controller{


	public function index()
	{
		//注册首页
		$this->assign('title','用户注册');
		$this->display();
	}
	/***
     * 执行数据添加
     * post数据校验
	**/
	public function regQuery()
    {
       $member = D('Member');
       $code=I('post.code_p');
       if($code!=session('code'))
       {
           $this->ajaxReturn(['code'=>1,'error'=>'验证码错误']);
       }
       if(!$member->create($_POST,4))
       {
           $error=$member->getError();//获取错误信息
           $this->ajaxReturn(['code'=>1,'error'=>$error]);
       }else{
           $pid=I('post.pid');//邀请码
           if($pid == true)
           {
               //邀请码存在
               if($member->where(['member_id'=>$pid])->find())
               {
                   $map['phone']=I('phone');
                   $map['pwd']=md5(I('pwd'));
                   $map['pwdtrade']=md5(I('pwdtrade'));
                   $map['pid']=I('spid');
                   $map['reg_time']=$_SERVER['REQUEST_TIME'];

                   //邀请码id存在
                   if($memeber_id=$member->add($map))
                   {
                       session('USER_KEY_ID',$memeber_id);
                       session('USER_KEY',$map['phone']);//用户名
                       session('STATUS',0);//用户状态
                       session('code',null);
                       //数据插入成功
                       $this->ajaxReturn(['code'=>0,'url'=>U('Index/index')]);
                   }else{
                       //插入失败
                       $this->ajaxReturn(['code'=>1,'error'=>'注册失败，请咨询客服人员']);
                   }
               }else{
                   $this->ajaxReturn(['code'=>1,'error'=>'邀请码存在！']);
               }
           }else{
               //用户未填写邀请码
               $map['phone']=I('phone');
               $map['pwd']=md5(I('pwd'));
               $map['pwdtrade']=md5(I('pwdtrade'));
               $map['reg_time']=$_SERVER['REQUEST_TIME'];
               if($memeber_id=$member->add($map))
               {
                   session('USER_KEY_ID',$memeber_id);
                   session('USER_KEY',$map['phone']);//用户名
                   session('STATUS',0);//用户状态
                   //数据插入成功
                   session('code',null);
                   $this->ajaxReturn(['code'=>0,'url'=>U('Index/index')]);
               }else{
                   $this->ajaxReturn(['code'=>1,'error'=>'注册失败，请咨询客服人员']);
               }
           }

       }
    }
}