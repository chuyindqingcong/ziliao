<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-3-8
 * Time: 下午2:23
 */

namespace Admin\Model;
use Think\Model\ViewModel;
class CurrencyModel extends ViewModel
{
	public $viewFields=array(
		'trade_fee'=>array('currency_id','currency_buy_fee','currency_sell_fee','trade_fee_id','ctime'),
		'currency'=>array('currency_name','_on'=>'trade_fee.currency_id=currency.currency_id','_type'=>'RIGHT'),
		);
}