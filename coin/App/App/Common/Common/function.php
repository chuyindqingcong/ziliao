<?php
/**
 * Created by PhpStorm.
 * User: "姜鹏"
 * Date: 16-3-8
 * Time: 下午4:57
 */
/**
 * 格式化信息类型
 * @param $type 参数(信息类型的参数）
 * @return string 返回值 把数字类型的参数 转换成汉字类型
 *
 */
 /*
function message_format_type($type){
    switch($type){
        case 1:$name="";break;
    }
    return $name;
}


/**
 * 验证邮箱
 * @param $email
 * @return bool
 */
function checkEmail($email){
    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    if(preg_match($pattern, $email)){
        return true;
    }else{
        return false;
    }
}
/**
 * 验证手机号支持以下号段
 *      移动：134、135、136、137、138、139、150、151、152、157、158、159、182、183、184、187、188、178(4G)、147(上网卡)；
联通：130、131、132、155、156、185、186、176(4G)、145(上网卡)；
电信：133、153、180、181、189 、177(4G)；
卫星通信：1349
虚拟运营商：170
 * @param $mobile
 * @return bool
 */
function checkMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0-9]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 截取字符串
 * @param $str
 * @param int $start
 * @param $length
 * @param string $charset
 * @param bool $suffix
 * @return string
 */

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
        $slice= mb_substr($str, $start, $length, $charset);
    }elseif(function_exists('iconv_substr')) {
        $slice= iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $fix='';
    if(strlen($slice) < strlen($str)){
        $fix='';
    }
    return $suffix ? $slice.$fix : $slice;
}
//委托状态格式化
function enstrustStatus($num){
    switch ($num){
        case 0:$data="未成交";break;
        case 1:$data="部分成交";break;
        case 2:$data="已成交";break;
        case 3:$data="已撤销";break;
        case 4:$data="全部";break;
        default:$data="暂无";
    }
    return $data;
}
//充值状态格式化
function payStatus($num){
    switch ($num){
        case 0:$data="请付款";break;
        case 1:$data="充值成功";break;
        case 2:$data="充值失败";break;
        case 3:$data="已失效";break;
        default:$data="暂无";
    }
    return $data;
}
//提现状态格式化
function drawStatus($num){
    switch ($num){
        case 0:$data="未通过";break;
        case 1:$data="未通过";break;
        case 2:$data="通过";break;
        case 3:$data="审核中";break;
        default:$data="暂无";
    }
    return $data;
}
//充值状态格式化
function zhongchouStatus($num){
    switch ($num){
        case 0:$data="新众筹";break;
        case 1:$data="众筹开始";break;
        case 2:$data="众筹结束";break;
        case 3:$data="众筹结束";break;
        default:$data="暂无";
    }
    return $data;
}

/**委托记录状态
 * @param $status  状态
 * @return string
 */
function getOrdersStatus($status){
    switch($status){
        case 0 : $data =  "挂单";
            break;
        case 1 : $data =  "部分成交";
            break;
        case 2 : $data =  "成交";
            break;
        case -1 : $data =  "已撤销";
            break;
        default: $data="未知状态";
    }
    return $data;
}

/**委托记录type
 * @param $type
 * @return string
 */
function getOrdersType($type){
    switch($type){
        case "buy": $data="买入";
            break;
        case "sell" : $data="卖出";
            break;
        default: $data="未知状态";
    }
    return $data;
}

/**
 * 验证密码长度在6-20个字符之间
 * @param $pwd
 * @return bool
 */
function checkPwd($pwd){
    $pattern="/^[\\w-\\.]{6,20}$/";
    if(preg_match($pattern, $pwd)){
        return true;
    }else{
        return false;
    }
}


/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
 
 
/**
 * 邮件发送函数
 */
    function sendMail($mailhost,$mailusername,$mailpassword,$to, $title, $content) {
     
        Vendor('PHPMailer.PHPMailerAutoload');

        $mail = new PHPMailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->SMTPSecure = "ssl";
        $mail->Port       = 465;

        $mail->Host=$mailhost;//C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
        $mail->Username =$mailusername;// C('MAIL_USERNAME'); //你的邮箱名
        $mail->Password = $mailpassword;//C('MAIL_PASSWORD') ; //邮箱密码
        $mail->From =$mailusername;// C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
        $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
        $mail->AddAddress($to,"尊敬的客户");
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
        $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
        $mail->Subject =$title; //邮件主题
        $mail->Body = $content; //邮件内容
        $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
        return($mail->Send());
    }
/**
 * 人民币格式化
 * @param $num
 * @return array|bool|string
 */
function num_format($num){
    if(!is_numeric($num)){
        return false;
    }
    $rvalue='';
    $num = explode('.',$num);//把整数和小数分开
    $rl = !isset($num['1']) ? '' : $num['1'];//小数部分的值
    $j = strlen($num[0]) % 3;//整数有多少位
    $sl = substr($num[0], 0, $j);//前面不满三位的数取出来
    $sr = substr($num[0], $j);//后面的满三位的数取出来
    $i = 0;
    while($i <= strlen($sr)){
        $rvalue = $rvalue.','.substr($sr, $i, 3);//三位三位取出再合并，按逗号隔开
        $i = $i + 3;
    }
    $rvalue = $sl.$rvalue;
    $rvalue = substr($rvalue,0,strlen($rvalue)-1);//去掉最后一个逗号
    $rvalue = explode(',',$rvalue);//分解成数组
    if($rvalue[0]==0){
        array_shift($rvalue);//如果第一个元素为0，删除第一个元素
    }
    $rv = $rvalue[0];//前面不满三位的数
    for($i = 1; $i < count($rvalue); $i++){
        $rv = $rv.','.$rvalue[$i];
    }
    if(!empty($rl)){
        $rvalue = $rv.'.'.$rl;//小数不为空，整数和小数合并
    }else{
        $rvalue = $rv;//小数为空，只有整数
    }
    return $rvalue;
}
//******************************
/**
 * 发送短信
*
* @param string $mobile 手机号码
* @param string $name 短信头
* @param string $user_name 短信账户
* @param string $user_password 短息密码
* @param string $needstatus 是否需要状态报告
* @param string $product 产品id，可选
* @param string $extno   扩展码，可选
*/
//$r = sandPhone($phone,$this->config['CODE_NAME'],$this->config['CODE_USER_NAME'],$this->config['CODE_USER_PASS']);

function sandPhone( $mobile, $name, $user_name, $user_password, $needstatus = 'true', $product = '', $extno = '') {
	$PORT=80;//端口号默认80
	$IP="222.73.117.156";//"222.73.117.156";
	$chuanglan_config['api_account']=iconv('UTF-8', 'UTF-8',$user_name);
	$chuanglan_config['api_password']=iconv('UTF-8', 'UTF-8', $user_password);
	$chuanglan_config['api_send_url']="http://".$IP.":".$PORT."/msg/HttpBatchSendSM";
	$code = rand(100000,999999);
	session(array('name'=>'code','expire'=>600));
	session('code',$code);  //设置session
	session('num',session('num')+1);  //设置session
	session('time',time());
	
	/*if (session('num')>3){	
			$arr[1]="121";
			return $arr;
 	}*/
	
	$data="您好，您的验证码是".$code;//要发送的短信内容
	/*
	$content=mb_convert_encoding("$data",'UTF-8', 'UTF-8');
	//创蓝接口参数
	$postArr = array (
			'account' => $chuanglan_config['api_account'],
			'pswd' => $chuanglan_config['api_password'],
			'msg' => $content,
			'mobile' => $mobile,
			'needstatus' => $needstatus,
			'product' => $product,
			'extno' => $extno
	);

	$result = curlPost( $chuanglan_config['api_send_url'] , $postArr);
	$result=execResult($result);
	return $result;
	
	*/
	$statusStr = array(
		"0" => "短信发送成功",
		"-1" => "参数不全",
		"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
		"30" => "密码错误",
		"40" => "账号不存在",
		"41" => "余额不足",
		"42" => "帐户已过期",
		"43" => "IP地址限制",
		"50" => "内容含有敏感词"
	);	
	$smsapi = "http://www.smsbao.com/"; //短信网关
	$user = $user_name; //短信平台帐号
	$pass = md5($user_password); //短信平台密码
	$content=$data;//要发送的短信内容
	$phone = $mobile;
	$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
	$result =file_get_contents($sendurl) ;
	return $statusStr[$result];
	
	
}



function sandPhone2( $data,$mobile, $name, $user_name, $user_password, $needstatus = 'true', $product = '', $extno = '') {
    $PORT=80;//端口号默认80
    $IP="222.73.117.156";//"222.73.117.156";
    $chuanglan_config['api_account']=iconv('UTF-8', 'UTF-8',$user_name);
    $chuanglan_config['api_password']=iconv('UTF-8', 'UTF-8', $user_password);
    $chuanglan_config['api_send_url']="http://".$IP.":".$PORT."/msg/HttpBatchSendSM";
  
   
    
    //$data=$data;//要发送的短信内容
   
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );  
    $smsapi = "http://www.smsbao.com/"; //短信网关
    $user = $user_name; //短信平台帐号
    $pass = md5($user_password); //短信平台密码
    $content=$data;//要发送的短信内容
    $phone = $mobile;
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    $result =file_get_contents($sendurl) ;
    return $statusStr[$result];
    
    
}
/**
 * 处理返回值
 *
 */
function execResult($result){
	$result=preg_split("/[,\r\n]/",$result);
	return $result;
}


/**
 * 通过CURL发送HTTP请求
 * @param string $url  //请求URL
 * @param array $postFields //请求参数
 * @return mixed
 */
function curlPost($url,$postFields){
	$postFields = http_build_query($postFields);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
	$result = curl_exec ( $ch );
	curl_close ( $ch );

	return $result;
}

function chuanglan_status($num){
	switch ($num){
		case 0;$name="短消息发送成功";break;
		case 101;$name="无此用户";break;
		case 102;$name="密码错";break;
		case 103;$name="提交过快(提交速度超过流速限制)";break;
		case 104;$name="系统忙（因平台侧原因，暂时无法处理提交的短信）";break;
		case 105;$name="敏感短信（短信内容包含敏感词）";break;
		case 106;$name="消息长度错（>536或<=0）";break;
		case 107;$name="包含错误的手机号码";break;
		case 108;$name="手机号码个数错（群发>50000或<=0;单发>200或<=0）";break;
		case 109:$name="无发送额度（该用户可用短信数已使用完）";break;
		case 110:$name="不在发送时间内";break;
		case 111:$name="超出该账户当月发送额度限制";break;
		case 112:$name="无此产品，用户没有订购该产品";break;
		case 113:$name="extno格式错（非数字或者长度不对)";break;
		case 115:$name="自动审核驳回";break;
		case 116:$name="签名不合法，未带签名（用户必须带签名的前提下）";break;
		case 117:$name="IP地址认证错,请求调用的IP地址不是系统登记的IP地址";break;
		case 118:$name="用户没有相应的发送权限";break;
		case 119:$name="用户已过期";break;
		case 120:$name="短信内容不在白名单中";break;
		case 121:$name="您已经超出短信发送次数限制";break;
		default:$name="系统错误，请及时联系管理员";break;
	}
	return $name;

}
//********************

/** 短信宝短信认证
 * @param $phone 电话号码
 * @param $name  发送标题
 * @param $user  短息接口用户名
 * @param $pass  短信接口密码
 * @return mixed 错误信息
 */
function sandPhone1 ($phone,$name,$user,$pass){
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词",
        "100"=>'您操作太频繁，请稍后再试',
        "101"=>'您已经超出短信发送次数限制'
    );
    if (session('num')>3){
        return $statusStr['101'];
    }
    $smsapi = "http://api.smsbao.com/";
    $pass = md5($pass); //短信平台密码
    $time=session('time');
    if (time()-$time<180&&!empty($time)){
        return $statusStr['100'];
    }
    $code=rand(1000, 9999);
    session(array('name'=>'code','expire'=>600));
    session('code',$code);  //设置session
    session('num',session('num')+1);  //设置session
    session('time',time());
    $content="【".$name."】您的验证码为".$code."，请不要泄漏给他人。";//要发送的短信内容
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    $result =file_get_contents($sendurl) ;
//     dump($user);dump($pass);dump($phone);dump($content);die;
    return $statusStr[$result];
}
/**
 * 验证手机
 * @param $code
 * @return bool
 */
function checkPhoneCode($code){
    if (session('code')!=$code){
        return  false;
    }else {
        return true;
    }
}

/**
 * 随机数字英文字符
 * @param $param 长度
 * @return string 随机数
 */
function getRandom($param){
    $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for($i=0;$i<$param;$i++)
    {
        $key .= $str{mt_rand(0,32)};    //生成php随机数
    }
    return $key;
}
/**
 * 财务日志添加
 * @param unknown $member_id 用户id
 * @param unknown $type	   类型
 * @param unknown $content 说明
 * @param unknown $money	变动资金
 * @param unknown $money_type	支出=2/收入=1
 * @param unknown $currency_id	币种 默认为0 为人民币
 * @return boolean
 */
function  addFinanceLog($member_id,$type,$content,$money,$money_type,$currency_id=0){
	$finance=M('Finance');
	$data['money_type']=$money_type;//收入或者支出
	$data['member_id']=$member_id;//用户id
	$data['type']=$type;//类型
	$data['content']=$content;//变动说明
	$data['money']=$money;//变动资金数量
	$data['add_time']=time();//添加时间
	$data['currency_id']=$currency_id;//币种
	$data['ip']=get_client_ip();//ip
	$data['status']=0;
	$r=$finance->data($data)->add();
	if($r){
		return true;
	}else {
		return false;
	}
}
/**
 * 格式化挂单记录status状态
 * @param unknown $status   状态
 * @return unknown
 */
 function formatOrdersStatus($status){
	switch($status){
		case 0: $status = '未成交' ;break;
		case 1: $status = '部分成交' ;break;
		case 2: $status = '已成交' ;break;
		case -1: $status = '已撤销' ;break;
		default: $status = '未成交' ;break;
	}
	return  $status;
}
/**
 * 格式化用户名
 * @param unknown $currency_id   币种id
 * @return unknown
 */
 function getCurrencynameByCurrency($currency_id){
     if(isset($currency_id)){
     	if($currency_id==0){
             return "人民币";
         }
     	return  M('Currency')->field('currency_name')->where("currency_id='{$currency_id}'")->find()['currency_name'];
	}else{
         return "未知钱币";
     }
}

/**
 * 格式化用户名
 * @param unknown $member_id
 */
 function getMemberNameByMemberid($member_id){
 	$where['member_id']= $member_id;
 	$list = M('Member')->field('name')->where($where)->find();
 	return !empty($list)?$list['name']:'无';
 }

function getMemberEmailByMember_id($member_id){
    $where['member_id']= $member_id;
    $list = M('Member')->field('email')->where($where)->find();
    return !empty($list)?$list['email']:'无';
}

//格式化挂单买单还是卖单
function fomatOrdersType($type){
    switch ($type){
        case 'buy':$type='买';break;
        case 'sell':$type='卖';break;
        default:$type='无';
    }
    return $type;
}

//计算卖出比例
function setOrdersTradeNum($num,$trade_num){
   return 100-($trade_num/$num*100);
}

/**委托记录状态
 * @param $status  状态
 * @return string
 */
function formatMemberStatus($status){
    switch($status){
        case 0 : $status =  "挂单";
            break;
        case 1 : $status =  "部分成交";
            break;
        case 2 : $status =  "成交";
            break;
        default: $status="未知状态";
    }
    return $status;
}

/**
 * 根据众筹id号查找一共众筹次数
 * @param $id 用户ID
 * @return mixed 次数
 */
function getIssueMemberCountByIssueId($id){
    $re = M('Issue_log')->where("iid = '{$id}'")->count("DISTINCT uid");
    if($re){
        return $re;
    }else{
        return 0;
    }
}


/**
 *  给分页传参数
 * @param  mixed $Page 分页对象
 * @param array $parameter 传参数组
 */
function setPageParameter($Page,$parameter){
    foreach ($parameter as $k=> $v){
        if (isset($v)){
            $Page->parameter[$k]=$v;
        }
    }
}
/**
 * 获取用户状态
 * @param string $status 状态
 * @return boolean|string 状态
 */
function getMemberStatus($status){
	if(empty($status)){
		return false;
	}
	switch($status){
		case 0 : $status =  "未填写个人信息";
		break;
		case 1 : $status =  "正常";
		break;
		case 2 : $status =  "禁用";
		break;
		default: $status="未知状态";
	}
	return $status;
}
       /**
 * 导出数据为excel表格
 * 
 * @param $data 一个二维数组,结构如同从数据库查出来的数组        	
 * @param $title excel的第一行标题,一个数组,如果为空则没有标题        	
 * @param $filename 下载的文件名
 *        	@examlpe
 *        	$stu = M ('User');
 *        	$arr = $stu -> select();
 *        	exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function exportexcel($data = array(), $title = array(), $filename = 'report') {
	header ( "Content-type:application/octet-stream" );
	header ( "Accept-Ranges:bytes" );
	header ( "Content-type:application/vnd.ms-excel" );
	header ( "Content-Disposition:attachment;filename=" . $filename . ".xls" );
	header ( "Pragma: no-cache" );
	header ( "Expires: 0" );
	// 导出xls 开始
	if (! empty ( $title )) {
		foreach ( $title as $k => $v ) {
			$title [$k] = iconv ( "UTF-8", "GB2312", $v );
		}
		$title = implode ( "\t", $title );
		echo "$title\n";
	}
	if (! empty ( $data )) {
		foreach ( $data as $key => $val ) {
			foreach ( $val as $ck => $cv ) {
				$data [$key] [$ck] = iconv ( "UTF-8", "GB2312", $cv );
			}
			$data [$key] = implode ( "\t", $data [$key] );
		}
		echo implode ( "\n", $data );
	}
}
 
/**
 * 检测sql
 * @param unknown $sql_str 语句
 * @return unknown 
 */
function inject_check($sql_str) {
	$check= eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
	if($check){
		return true;
	}else{
		return false;
	}
}

/**
 * Thinkphp默认分页样式转Bootstrap分页样式
 * @author H.W.H
 * @param string $page_html tp默认输出的分页html代码
 * @return string 新的分页html代码
 */
function bootstrap_page_style($page_html){
    if ($page_html) {
        $page_show = str_replace('<div>','<nav><ul class="pagination">',$page_html);
        $page_show = str_replace('</div>','</ul></nav>',$page_show);
        $page_show = str_replace('<span class="current">','<li class="active"><a>',$page_show);
        $page_show = str_replace('</span>','</a></li>',$page_show);
        $page_show = str_replace(array('<a class="num"','<a class="prev"','<a class="next"','<a class="end"','<a class="first"'),'<li><a',$page_show);
        $page_show = str_replace('</a>','</a></li>',$page_show);
    }
    return $page_show;
}

/**
     * 图片上传处理
     * @param [String] $path [保存文件夹名称]
     * @param [String] $thumbWidth [缩略图宽度]
     * @param [String] $thumbHeight [缩略图高度]
     * @return [Array] [图片上传信息]
     */
function _upload($file,$thumbWidth = '' , $thumbHeight = '') {
        switch($file['type'])
	    {
	        case 'image/jpeg': $ext = 'jpg'; break;
	        case 'image/gif': $ext = 'gif'; break;
	        case 'image/png': $ext = 'png'; break;
	        case 'image/tiff': $ext = 'tif'; break;
	        default: $ext = ''; break;
	    }
	    if (empty($ext)){
	        return false;
	    }
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath  =      './Public/Uploads/'; // 设置附件上传目录
		// 上传文件
		$info   =  $upload->uploadOne($file);
		if(!$info) {
			// 上传错误提示错误信息
			$this->error($upload->getError());exit();
		}else{
			
			// 上传成功
			$pic=$info['savepath'].$info['savename'];
			$a='/Uploads'.ltrim($pic,".");
			$b='/Uploads'.ltrim($info['savepath'],".").'thumb_'.$info['savename'];
			$url2=C('THUMP_PATH').ltrim($pic,".");
			
			$url3=C('THUMP_PATH').ltrim($info['savepath'],".").'thumb_'.$info['savename'];
			$image = new \Think\Image(); 
			$image->open($url2);

			$image->thumb(150,150)->save($url3);
			$re['pic1']=$a;
			$re['pic2']=$b;
		}
		return $re;
        
    }
	
	function sfrz($name,$sfzh,$kh,$phone)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://apis.haoservice.com/creditop/BankCardQuery/QryBankCardBy4Element?accountNo=".$kh."&name=".$name."&idCardCode=".$sfzh."&bankPreMobile=".$phone."&key=80bafe524101410d96ba15f30d664c3e",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return $err;
		} else {
		  return $response;
		}
	}




    //创建TOKEN
function creatToken() {
    $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
    session('TOKEN', authcode($code));
}

//判断TOKEN
function checkToken($token) {
    if ($token == session('TOKEN')) {
        session('TOKEN', NULL);
        return TRUE;
    } else {
        return FALSE;
    }
}


function authcode($str) {
    $key = "jnwewr12349";
    $str = substr(md5($str), 8, 10);
    return md5($key . $str);
}

function is_mobile(){
    //判断是不是手机浏览器
    // returns true if one of the specified mobile browsers is detected
    // 如果监测到是指定的浏览器之一则返回true

    $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

    $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

    $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

    $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

    $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

    $regex_match.=")/i";

    // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
    return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}
