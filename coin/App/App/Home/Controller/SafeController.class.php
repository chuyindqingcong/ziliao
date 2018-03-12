<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class SafeController extends HomeController {
 	public function _initialize(){
 		parent::_initialize();
 	}
	//空操作
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
	public function index(){
        $u_info = M('Member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();


        $this->assign('u_info',$u_info);
		$this->assign('empty','暂无数据');
        $this->display();
     }
     public function freeBind()
     {
         //解除绑定
         if(IS_AJAX)
         {
             $type=I('post.type');
             $map=array();
             $map[$type]=null;
             $member=M('member');
             if($member->where(['member_id'=>session('USER_KEY_ID')])->save($map))
             {
                 $this->ajaxReturn(['code'=>0]);
             }else{
                 $this->ajaxReturn(['code'=>1,'解除绑定失败！']);
             }
         }
     }
	public function shenfen(){
		if(IS_AJAX)
		{
			// $data['name']=trim(I('post.xm'));
			// $data['idcard']=trim(I('post.sfzh'));
			$data['safe_time']=time();
			$data['status']= 1;
			$data['shenhestatus']= 3;
			$fm=I('post.fm');
			$zm=I('post.zm');

			$shou=I('post.shou');

			

		    list($type,$data2) = explode(',', $zm);    
		    if(strstr($type,'image/jpeg')!==''){    
		        $ext = '.jpg';    
		    }elseif(strstr($type,'image/gif')!==''){    
		        $ext = '.gif';    
		    }elseif(strstr($type,'image/png')!==''){    
		        $ext = '.png';    
		    }    
		    $time=time().rand(000,999).$ext; 

		    $path=__APP__.'Uploads/idcard/'.$time; // 上传路径:绝对路径 
		    $res=file_put_contents($path,base64_decode($data2),true); 


		    list($type1,$data1) = explode(',', $fm);    
		    if(strstr($type1,'image/jpeg')!==''){    
		        $ext1 = '.jpg';    
		    }elseif(strstr($type1,'image/gif')!==''){    
		        $ext1 = '.gif';    
		    }elseif(strstr($type1,'image/png')!==''){    
		        $ext1 = '.png';    
		    }    
		    $time1=time().rand(000,999).$ext1; 

		    $path1=__APP__.'Uploads/idcard/'.$time1; // 上传路径:绝对路径 
		    $res1=file_put_contents($path1,base64_decode($data1),true); 



		    list($t,$dd) = explode(',', $shou);    
		    if(strstr($t,'image/jpeg')!==''){    
		        $ex = '.jpg';    
		    }elseif(strstr($t,'image/gif')!==''){    
		        $ex = '.gif';    
		    }elseif(strstr($t,'image/png')!==''){    
		        $ex = '.png';    
		    }    
		    $t=time().rand(000,999).$ex; 

		    $p=__APP__.'Uploads/idcard/'.$t; // 上传路径:绝对路径 
		    $res2=file_put_contents($p,base64_decode($dd),true); 

		    $data['pic1'] ='/'.$path;
		    $data['pic2'] ='/'.$path1;
		    $data['pic3']='/'.$p;

	
		    if($res && $res1 && $res2)
		    {
		    	$re2=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->save($data);

				if($re2==0)
				{
					$d['info']="高级实名认证提交成功,请等待平台审核";
					$d['status']=1;
					$this->ajaxReturn($d);
					exit;
				}
				if($re2)
				{
					$d['info']="高级实名认证提交成功,请等待平台审核";
					$d['status']=1;
					$this->ajaxReturn($d);
					exit;
				}
				else
				{
					$d['info']="服务器繁忙，请重试！";
					$this->ajaxReturn($d);
					exit;
				}
		    }
		    else
		    {
		    	    $d['info']="服务器繁忙，请重试！";
					$this->ajaxReturn($d);
					exit;
		    }


		

     }
     else
		{
			$result=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();
			if($result['shenhestatus']==3)
			{
				$this->assign('u_info',$result);
				$this->display('shengfen_shenhe');

			}

			else
			{
				if($result['shenhestatus']==1)
				{
					$this->assign('u_info',$result);
					$this->display('shenfen_tongguo');

				}
				else
				{
					$this->display('shenfen_chuji');
				}
			}

			
			
			
		}
 }

    public function shenfen_tongguo(){
    	$u_info = M('Member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();
    	
        $this->assign('u_info',$u_info);
    	$this->display();
    }

    public function shenfen_gaoji(){
    	$u_info = M('Member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();
    	
        $this->assign('u_info',$u_info);
    	$this->display('shenfen');
    }

	public function d_pass(){
		if(IS_AJAX)
		{
			$oldpwd=I('post.oldpwd');
			$d['pwd']=md5(I('post.newpwd2'));
			
			$re=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();
			
			if($re['pwd']!=md5($oldpwd))
			{
				$data['info']="你输入原始的密码不正确";
				$this->ajaxReturn($data);
				exit;
			}
			else
			{
				$re2=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->save($d);
				if($re2)
				{
					$data['info']="密码修改成功,请重新登录！";
					$data['status']=1;
					session(null);
					$this->ajaxReturn($data);
					exit;
				}
				else
				{
					$data['info']="数据更新失败";
					$this->ajaxReturn($data);
					exit;
				}
			}
		}
		else
		{
			$this->display();
		}
        
     }
     public function j_pass(){
        $this->display();
     }
     public function email(){
        $this->display();
     }
     public function jiaoyi(){
		 if(IS_AJAX)
		 {
			$data['typepwd1']=I('post.optinon');
			$pwd=md5(I('post.pwd'));
			$where['member_id']=session('USER_KEY_ID');
			$where['pwdtrade']=$pwd;
			$re=M('member')->where($where)->find();
			if($re)
			{
				$re2=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->save($data);
				
				if($re2||$re2==0)
				{
					$d['status']=1;
					$d['info']="设置成功";
					$this->ajaxReturn($d);
					exit;
				}
				else{
					
					$d['info']="设置失败";
					$this->ajaxReturn($d);
					exit;
				}
			}
			else
			{
				$d['info']="交易密码输入错误";
				$this->ajaxReturn($d);
				exit;
			}
			
			
		 }
		 else{
			 
			 $re=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->find();
			 $this->assign('jykz',$re['typepwd1']);
			 $this->display();
		 }
        
     }
     public function phone(){
		 if(IS_AJAX)
		 {
			$d['phone']=I('post.phone');
			$code=I('post.code');
			if($code==session('code'))
			{
				$re=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->save($d);
				
				if($re)
				{
					$data['status']=1;
					$data['info']="手机号更换成功";
					$this->ajaxReturn($data);
					exit;
				}
				else{
					
					$data['info']="数据更新失败";
					$this->ajaxReturn($data);
					exit;
				}
				
			}
			else
			{
				$data['info']="验证码填写错误";
				$this->ajaxReturn($data);
				exit;
			}
			
		 }
		 else
		 {
			 $this->display();
		 }
        
     }
     public function change_email(){
		 if(IS_AJAX)
		 {
			$d['email']=I('post.email');
			$yzm=I('post.yzm');
			
			if($yzm==session('yzm'))
			{
				$re=M('member')->where("member_id = {$_SESSION['USER_KEY_ID']}")->save($d);
				
				if($re)
				{
					$data['status']=1;
					$data['info']="新邮箱已经绑定";
					$this->ajaxReturn($data);
					exit;
				}
				else{
					
					$data['info']="数据更新失败";
					$this->ajaxReturn($data);
					exit;
				}
				
			}
			else
			{
				$data['info']="验证码填写错误";
				$this->ajaxReturn($data);
				exit;
			}
		 }
		 else
		 {
			$this->display();
		 }
       
     }

	 public function mobilebind(){
		
		$this->assign('empty','暂无数据');
        $this->display();
     }




}