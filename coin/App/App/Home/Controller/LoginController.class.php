<?php
/**
 * Created by PhpStorm.
 * User: "姜鹏"
 * Date: 16-3-9
 * Time: 上午9:06
 */

namespace Home\Controller;
use Common\Controller\CommonController;
use Think\Verify;

class LoginController extends CommonController{
    /**
     * 展示界面
     */
    public function index(){
        if(session('USER_KEY_ID')){
            $this->redirect('Index/index');
            return;
        }
        $this->display();
    }
    /***
    *微信登录
    **/
    public function wxLogin()
    {
        $url='https://open.weixin.qq.com/connect/qrconnect?appid='.C('appid').'&redirect_uri=http%3A%2F%2Fwww.9coin.com/Home/Login/wxballback&response_type=code&scope=snsapi_login&state=d978a056d2965beb2de82df3c654e7dc#wechat_redirect';
        header('Location:'.$url);
        die();
    }
    /**
     * QQ登录模块
     * 
     */
    public function qqlogin()
    {
        if(I('get.bind'))
        {
            session('login.bind',1);
        }
        require_once THINK_PATH."Library/Vendor/QQLogin/qqConnectAPI.php";//QQ登录SDK
        $qc = new \QC();
        $qc->qq_login();//调用返回数据并打开
        die();
    }
    /**
    *获取威信openid值
    *@ $code 必填的 微信登录授权成功后返回的数据
    ***/
    static private function getxxOpenid($code)
    {

            $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('appid').'&secret='.C('AppSecret').'&code='.$code.'&grant_type=authorization_code';
           $res=file_get_contents($url);//get请求 获取openid
           return $res;
    }
     public function wxBind()
    {
        //会员中心的微信绑定
        $url='https://open.weixin.qq.com/connect/qrconnect?appid='.C('appid').'&redirect_uri=http%3A%2F%2Fwww.9coin.com/Home/Login/wxballbacktwo&response_type=code&scope=snsapi_login&state=d978a056d2965beb2de82df3c654e7dc#wechat_redirect';
        header('Location:'.$url);
        die();
    }
    /****
     * 微信会员中心绑定回调
    **/
    public function wxballbacktwo($code)
    {
        $code=I('get.code');
        $return=self::getxxOpenid($code);
        $res=json_decode($return,true);
        if($res==true)
        {
            $openid=$res['openid'];
            $user=M('member')->where(['openid'=>$openid])->find();
            if($user==true)
            {
                if($user['member_id'] == session('USER_KEY_ID'))
                {
                    echo '账号已经被绑定，不能重复绑定';
                    die();
                }else{
                    echo '账号已经被其他用户绑定，请先解除再绑定';
                    die();
                }
            }else{
                $status=M('member')->where(['member_id'=>session('USER_KEY_ID')])->save(['openid'=>$res['openid']]);
                if($status)
                {
                    echo '<script>window.close();</script>';
                    die();
                }
            }
        }else{
            $this->redirect('Index/index');
        }
    }
    /**
    ***微信登录回调
    *@$code 微信登录返回code获取openid
    */
    public function wxballback()
    {
        $code=I('get.code');
        $return=self::getxxOpenid($code);
        $res=json_decode($return,true);
        if($res==true)
        {
            $openid=$res['openid'];
            $user=M('member')->where(['openid'=>$openid])->find();
            if($user==true)
            {
                if($user['is_lock'])
                {
                    $this->error('帐号无法登录,请联系客服',U('Login/index'));
                    die();
                }else{
                    session('user',$user);
                    session('USER_KEY_ID',$user['member_id']);
                    session('STATUS',$user['status']);//用户状态
                    $this->redirect('Home/Index/index');
                }
            }else{
                session('ol.openid',$res['openid']);
                session('ol.type','openid');     
                $this->redirect('Login/loginBind');
            }
        }else{
           $this->redirect('Index/index'); 
        }
    }
    /**
     * QQ 互联回调
     * 
     */
        public function qqballback()
    {
        echo THINK_PATH."Library/Vendor/QQLogin/qqConnectAPI.php";
        
        require_once THINK_PATH."Library/Vendor/QQLogin/qqConnectAPI.php";
        $qc = new \QC();  
        $acs = $qc->qq_callback();//callback主要是验证 code和state,返回token信息，并写入到文件中存储，方便get_openid从文件中度  
        $oid = $qc->get_openid();//根据callback获取到的token信息得到openid,所以callback必须在openid前调用  
        $qc = new \QC($acs,$oid);  
        $uinfo = $qc->get_user_info();
        if(session('login.bind')==true)
        {
            session('login.bind',null);
            $member = M('member');
            $user=$member->where(['qqopenid'=>$oid])->find();
            if($user)
            {
                //检测是否被其他账号绑定
                echo '对不起账号已经被绑定，请解除绑定后再试';
                die();
            }
            if($member->where(['member_id'=>session('USER_KEY_ID')])->save(['qqopenid'=>$oid]))
            {
                //状态修改
                echo '<script>window.close();</script>';
                die();
            }else{
                echo '<script>window.close();</script>';
                die();
            }
        }
        if($uinfo==true)
        {
            // 1  QQ      2微信
            //登录完成业务逻辑开始
            $user=M('member')->where(['qqopenid'=>$oid])->find();
            if($user)
            {
                if($user['is_lock'])
                {
                    $this->error('帐号无法登录,请联系客服',U('Login/index'));
                    die();
                }else{
                    session('user',$user);
                    session('USER_KEY_ID',$user['member_id']);
                    session('USER_KEY',$user['email']);//用户名
                    session('STATUS',$user['status']);//用户状态
                }
            }else{
                    session('ol.openid',$oid);//Other login 第三方登录
                    session('ol.type','qqopenid');
                    session('qq.userinfo',$uinfo);
            }
            $this->redirect('Login/loginBind');
        }else{
            //登录失败业务逻辑
        }
    }
    /***
    *登录账号进行账号绑定
     * @openid
     */
    public function loginBind()
    {
        //Other login 第三方登录
        if(session('ol.openid')==false)
        {
            $this->redirect('Login/index');
        }
        $this->display();
    }
    /**
     *校验绑定账号是否合法
     * @email 邮箱
     * @phone 手机号码
     * @pwd 密码
     ***/
    public function loginBindCheck()
    {
        if(IS_POST)
        {
            $email=I('post.email');
            $pwd=I('post.pwd');
            if(checkMobile($email))
            {
                //手机登录
                $user=M('member')->where(['phone'=>$email,'pwd'=>md5($pwd)])->find();
                if($user['is_lock'])
                {
                    $this->ajaxReturn(['code'=>1,'error'=>'用户被锁定，请联系客服']);
                }else{
                    $arr=array();
                    $opentype=session('ol.type');
                    $arr[$opentype]=session('ol.openid');
                    if(self::upOpenid($arr,$user['member_id']))
                    {
                        session('USER_KEY_ID',$user['member_id']);
                        session('USER_KEY',$user['email']);//用户名
                        session('STATUS',$user['status']);//用户状态
                        $this->ajaxReturn(['code'=>0]);
                    }else{
                        $this->ajaxReturn(['code'=>1,'error'=>'账号绑定失败']);
                    }
                }
            }else{
                //邮箱登录
                $user=M('member')->where(['email'=>$email,'pwd'=>md5($pwd)])->find();
                if($user['is_lock'])
                {
                    $this->ajaxReturn(['code'=>1,'error'=>'用户被锁定，请联系客服']);
                }else{
                    $arr=array();
                    $opentype=session('ol.type');
                    $arr[$opentype]=$opentype;
                    if(self::upOpenid($arr,$user['member_id']))
                    {
                        session('USER_KEY_ID',$user['member_id']);
                        session('USER_KEY',$user['email']);//用户名
                        session('STATUS',$user['status']);//用户状态
                        $this->ajaxReturn(['code'=>0]);
                    }else{
                        $this->ajaxReturn(['code'=>1,'error'=>'账号绑定失败']);
                    }

                }
            }
        }

    }
    /***
     * openid 更新
     * @openid 第三方登录 字段名
     * @member_id 用户唯一编号
     * @openid_type 绑定账号类型
    **/
    private static function upOpenid($arr,$member_id)
    {
        if(M('member')->where(['member_id'=>$member_id])->save($arr))
        {

            return true;
        }else{
            return false;
        }
    }
    /**
     * 处理登录请求
     * 全部用ajax提交
     */
    public function checkLog(){
        $email = I('post.email');
        $pwd = md5(I('post.pwd'));
        $M_member = D('Member');
        //再次判断
        if((checkEmail($email) || checkMobile($email))==false){
            $data['status']=2;
            $data['info']="请输入正确的手机或者邮箱";
            $this->ajaxReturn($data);
        }
        //判断传值是手机还是email
        $info = checkEmail($email)?$M_member->logCheckEmail($email):$M_member->logCheckMo($email);
        if($info['status']==2){
            $data['status']=2;
            $data['info']="账户异常，请联系客服";
            $this->ajaxReturn($data);
        }
        //验证手机或邮箱
        if($info==false){
            $data['status']=2;
            $data['info']="邮箱或者手机不存在";
            $this->ajaxReturn($data);
        }
        //验证密码
        if($info['pwd']!=$pwd){
            //$this->error('密码输入错误');
            $data['status']=2;
            $data['info']="密码输入错误";
            $this->ajaxReturn($data);
        }
        //获取下方能用到的参数
        /*
        $new_ip = get_client_ip();
        $old_login_ip = $info['login_ip']?$info['login_ip']:$info['ip'];
        $card = I('post.year').I('post.month').I('post.day');
        $idcard = substr($info['idcard'],6,8);
        //验证身份信息如果身份证存在并且 当前IP 和上次登录Ip不一样
        if($old_login_ip != $new_ip && $info['idcard'] ){
            if($card != $idcard){
                $data['status']=2;
                $data['info']="生日与您当前填写不符";
                $this->ajaxReturn($data);
            }
        }
        */
//        $this->pullMessage($info['member_id'],$info['login_time']?$info['login_time']:$info['reg_time'] );
        if($this->pullMessage($info['member_id'],$info['login_time']?$info['login_time']:$info['reg_time'])==false){
            $data['status']=2;
            $data['info']="服务器繁忙,请稍后重试12";
            $this->ajaxReturn($data);
        }
        //如果当前操作Ip和上次不同更新登录IP以及登录时间
        $data['login_ip'] = $new_ip;
        $data['login_time']= time();
        $where['member_id'] = $info['member_id'];
        $r = $M_member->where($where)->save($data);
        if($r===false){
            $data['status']=2;
            $data['info']="服务器繁忙,请稍后重试";
            $this->ajaxReturn($data);
        }
        session('user',$info);
        session('USER_KEY_ID',$info['member_id']);
        session('USER_KEY',$info['email']);//用户名
        session('STATUS',$info['status']);//用户状态

        $data['status']=1;
        $data['info']="登录成功";
        $this->ajaxReturn($data);
    }
    // public function qqLogin(){
    //  $app_id = C('SZ_QQ_APP_ID');
    //  $app_key = C('SZ_QQ_APP_KEY');
    //  $callback = C('SZ_QQ_CALLBACK');
    //  $qq = new \Common\Api\QQConnect;
    //  /* callback返回openid和access_token */
    //  $back = $qq->callback($app_id , $app_key, $callback);
    //  //防止刷新
    //  empty($back) && $this->error("请重新授权登录",U('Login/index'));
    //  $user_info = $qq->get_user_info($app_id,$back['token'],$back['openid']);
    //  $Member = M('Member');
    //  $where['openid']=$back['openid'];
    //  $MemberArray = $Member->where("openid='".$back['openid']."'")->field('member_id,username,status')->find();
    //  if($MemberArray['member_id']!=""){
    //      session('USER_KEY_ID',$MemberArray['member_id']);
    //      session('USER_KEY',$MemberArray['username']);//用户名
    //      session('STATUS',$MemberArray['status']);//用户状态
    //      $this->error("登陆成功",U('Index/index'));
                
    //  }else{
                
    //      $add['openid'] = $back['openid'];
    //      $add['threepwd'] = $back['openid'];
    //      $add['pwdtrade'] = md5('111111');
    //      $add['head']=$back['figureurl_2'];
    //      $add['name']=$back['nickname'];
            
                
                
    //      if($Member->create($add)){
    //          $userid = $Member->add();
    //          if($userid){
    //              session('USER_KEY_ID',$userid);
    //              session('USER_KEY',$back['openid']);//用户名
    //              session('STATUS',0);//用户状态
    //              $this->error("登陆成功",U('Index/index'));
    //          }else{
    //              $this->error("登陆失败",U('Login/index'));
    //          }
    //      }
    //  }
    // }
    /**
     * 忘记密码
     */
    public function findpwd(){
        if(IS_AJAX){
            // if(empty($_POST['email'])){
            //     $data['status']=2;
            //     $data['info']="请填写邮箱";
            //     $this->ajaxReturn($data);
            // }

            // if(!checkEmail($_POST['email'])){
            //     $data['status']=2;
            //     $data['info']="请输入正确的邮箱";
            //     $this->ajaxReturn($data);
            // }
            // if(empty($_POST['captcha'])){
            //     $data['status']=2;
            //     $data['info']="请填写验证码";
            //     $this->ajaxReturn($data);
            // }
            $verify = new Verify();
            if(!$verify->check($_POST['yzm'])){
                $data['status']=2;
                $data['info']="验证码输入错误";
                $this->ajaxReturn($data);
            }
            $info = M('Member')->where(array('email'=>$_POST['email']))->find();
            $info2= M('Member')->where(array('phone'=>$_POST['email']))->find();
            if($info==false && $info2==false){
                $data['status']=2;
                $data['info']="用户不存在";
                $this->ajaxReturn($data);
            }

            if($info)
            {
               $token = strtoupper(md5($_POST['email']).md5(time()));//大写md5当前邮箱+当前时间
                $url = "http://".$_SERVER['SERVER_NAME'].U('Login/resetPwd',array('key'=>$token));
                $content = "<div>";
                $content.= "您好，<br><br>请点击链接：<br>";
                $content.= "<a target='_blank' href='{$url}' >重置您的密码</a>";
                $content.= "<br><br>如果链接无法点击，请复制并打开以下网址：<br>";
                $content.= "<a target='_blank' href='{$url}' >{$url}</a>";
               // $r = setPostEmail($this->config['EMAIL_HOST'],$this->config['EMAIL_USERNAME'],$this->config['EMAIL_PASSWORD'],$this->config['name'].'团队',$_POST['email'],$this->config['name'].'团队[密码找回]',$content);
                
                $re=sendMail($this->config['EMAIL_HOST'],$this->config['EMAIL_USERNAME'],$this->config['EMAIL_PASSWORD'],$_POST['email'], $this->config['name'].'团队[密码找回]',$content);


                if($r){
                    $data['status']=2;
                    $data['info']="服务器繁忙,请稍后重试";
                    $this->ajaxReturn($data);
                }
                $member_id = $info['member_id'];
                $data['member_id'] = $info['member_id'];
                $data['token'] = $token;
                $data['add_time'] = time();
                if(M('Findpwd')->add($data)){
                    $data['status']=1;
                    $data['info']="邮箱已发送";
                    $this->ajaxReturn($data);
                }else{
                    $data['status']=2;
                    $data['info']="服务器繁忙,请稍后重试";
                    $this->ajaxReturn($data);
                } 
            }
            else
            {

                
                $pwd = rand(100000,999999);
                $dpwd="您的初始化密码是:".$pwd;
                $r = sandPhone2($dpwd,$_POST['email'],$this->config['CODE_NAME'],$this->config['CODE_USER_NAME'],$this->config['CODE_USER_PASS']);
                
                

                if($r==0){
                    $data['stauts']=1;
                    $data['info']="发送成功";

                    
                    $User = M("member"); // 实例化User对象
                    // 要修改的数据对象属性赋值
                    $d['pwd']=md5($pwd);
                  
                    $User->where('phone='.$_POST['email'])->save($d);




                    $this->ajaxReturn($data);exit;
                }else{
                    $data['stauts'] =-2;
                    $data['info']="发送失败,错误码:";
                    $this->ajaxReturn($data);exit;
                }
            }
            
        }else{
            $this->display();
        }
    }
    /**
     * 根据发送邮箱地址显示修改密码界面
     */
    public function resetPwd(){
        $token = I('key');
        if (empty($token)) {
            $this->success('无效的链接1', U('Index/index'));
            return;
        }
        $findpwd_info = M('Findpwd')->where("token = '$token'")->find();
        if ($findpwd_info === false) {
            $this->success('无效的链接', U('Index/index'));
            return;
        }
        if (time() - $findpwd_info['add_time'] > 24 * 60 * 60) {
            M('findpwd')->delete($findpwd_info['id']);
            $this->success('邮件已过期', U('Index/index'));
            return;
        }
        if(IS_POST){
            $verify = new Verify();
            if(!$verify->check($_POST['captcha'])){
                $data['status']=2;
                $data['info']="验证码输入错误";
                $this->ajaxReturn($data);
            }
            if(empty($_POST['pwd'])){
                $data['status']=2;
                $data['info']="请输入密码";
                $this->ajaxReturn($data);
            }
            if(!checkPwd($_POST['pwd'])){
                $data['status']=2;
                $data['info']="密码长度在6-20个字符之间";
                $this->ajaxReturn($data);
            }
            if($_POST['repwd'] != $_POST['pwd']){
                $data['status']=2;
                $data['info']="确认密码和密码不一致";
                $this->ajaxReturn($data);
            }
            $member_info = M('member')->where(array('member_id'=>$findpwd_info['member_id']))->find();
            if(!empty($member_info['idcard'])){
                if($_POST['idcard']!=$member_info['idcard']){
                    $data['status']=2;
                    $data['info']="身份证输入错误";
                    $this->ajaxReturn($data);
                }
            }
            $member_newPwd = I('pwd','','md5');
            $r = M('member')
                ->where(array('member_id'=>$member_info['member_id']))
                ->setField('pwd',$member_newPwd);
            if($r===false){
                $data['status']=2;
                $data['info']="服务器繁忙,请稍后重试";
                $this->ajaxReturn($data);
            }else{
                M('findpwd')->delete($findpwd_info['id']);
                $data['status']=1;
                $data['info']="修改成功";
                $this->ajaxReturn($data);
            }
        }else{
            $this->assign('key',$token);
            $this->display();
        }
    }
    /**
     * 显示验证码
     */
    public function showVerify(){
        $config =   array(
            'fontSize'  =>  14,              // 验证码字体大小(px)
            'useCurve'  =>  true,            // 是否画混淆曲线
            'useNoise'  =>  false,            // 是否添加杂点
            'imageH'    =>  35,               // 验证码图片高度
            'imageW'    =>  110,               // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
            'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
        );
        $Verify =     new Verify($config);
        $Verify->entry();
    }
    /**
     * ajax判断Ip
     * @param $email
     */
    public function checkIp($email){
        //判断传过来的是手机号还是email
        $data = array();
        if(!checkEmail($email) && !checkMobile($email)){
            $data['status'] = 2;
            $data['msg'] = '请输入正确的邮箱或手机号码';
            $this->ajaxReturn($data);
        }
        if(checkEmail($email)){
            $where['email']  = $email;
        }else{
            $where['phone']  = $email;
        } 
        //检查用户是否存在
        $info =  M('Member')->where($where)->find();
        if(!$info){
            $data['status'] = 2;
            $data['msg'] = '用户不存在';
            $this->ajaxReturn($data);
        }
        //检查是否做了身份认证
        if($info['idcard']){
            //如果login_ip不存在那么就是第一次登录取注册IP
            $old_login_ip = $info['login_ip']?$info['login_ip']:$info['ip'];
            $new_ip = get_client_ip();
            if($old_login_ip!=$new_ip){
                $data['status'] = 1;
                $data['msg'] = '系统监测到您的账号本次登录IP和上次不同，为了保障您的账户资产安全，请输入您在'.$this->config['name'].'预留的身份证上的出生日期；如还未实名认证，请联系客服认证。';
                $this->ajaxReturn($data);
            }
        }
        $data['status'] = 0;
        $data['msg'] = '';
        $this->ajaxReturn($data);
    }
    /**
     * 退出
     */
    public function loginOut(){
        if ($_SESSION['USER_KEY_ID']!=null){
            session(null);
        }
        $this->redirect('Index/index');
    }

    /**
     * 读取消息库中有自己消息的列表并且存储至个人消息库中
     * @param $id 用户ID
     * @param $login_time 用户最后一次登录时间
     * @return bool 返回 成功失败
     */
    public function pullMessage($id,$login_time){
        if(empty($id)){
            return false;
        }
        if(empty($login_time)){
            return false;
        }
        //消息库
        $M_message_all = M('message_all');
        //用户消息库
        $M_message = M('message');
        $messageAllWhere['add_time'] = array('EGT',$login_time);
        $messageAllWhere['_string'] = " u_id= -1 or  u_id = $id";
        $message_info = $M_message_all->where($messageAllWhere)->select();
        if($message_info){
            foreach ($message_info as $vo) {
                $data[] = array(
                    'member_id'=>$id,
                    'title'=>$vo['title'],
                    'type' => $vo['type'],
                    'content'=> $vo['content'],
                    'add_time'=> $vo['add_time'],
                    'status' => 0,//未读
                    'message_all_id'=> $vo['id'],
                );
            }
            if($M_message->addAll($data)===false){
                return false;
            }
        }
        return true;
    }
}