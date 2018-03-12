<?php
namespace Mobile\Model;
use Think\Model;
class MemberModel extends Model{
	protected $_validate =array(
		array('phone','require','手机号码必须填写'),
		array('code_p','require','校验码不能为空'),
		array('pwd','require','登录密码不能为空'),
        array('repwd','pwd','登录密码与确认密码不一致',0,'confirm'),
        array('pwdtrade','require','交易密码不能为空'),
        array('repwdtrade','pwdtrade','交易密码与确认密码不一致',0,'confirm'),
    );
	protected $_auto = array(
	    array('pwd','md5',3,'function'),
	    array('pwdtrade','md5',3,'function'),
    );
    protected $_map=array(
        'pid'=>'pid'
    );
}