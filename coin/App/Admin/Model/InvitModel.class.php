<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-3-8
 * Time: 下午2:23
 */

namespace Admin\Model;
use Think\Model\ViewModel;
class InvitModel extends ViewModel
{
	public $viewFields=array(
		'invit'=>array('invit_id','member_id','ctime','order_type','order_id','cfee','rebate','rebatetype','status','member_id_bottom','_type'=>'LEFT'),
		'trade'=>array('money','trade_id','num','_on'=>'invit.order_id=trade.trade_id','_type'=>'RIGHT'),
		'currency'=>array('currency_name','_on'=>'trade.currency_id=currency.currency_id','_type'=>'RIGHT')
		);
}