<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
use Admin\Model\CurrencyModel;
class FeeController extends AdminController {
	public function memberfee()
	{
		$trade_fee=D('Currency');
		$list=$trade_fee->where(['member_id'=>I('get.member_id')])->select();
		// dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	public function feeadd()
	{
		if(IS_POST)
		{
			$member_id=I('post.member_id');//用户编号
			$currency_id=I('post.currency_id');//币种编号
			$buy=I('post.buy');//买入手续费
			$sell=I('post.sell');//卖出手续费
			$currency=array();//初始化币种信息
			if($currency_id==true)
			{
				$currencyobj=M('currency');
				$currency=$currencyobj->where(['currency_id'=>$currency_id])->find();
				if($currency==false)
				{
					$this->error('币种编号不存在');
					die;
				}
			}else{

					$this->error('币种编号不存在');
					die;
			}
			if($member_id == false)
			{
				$this->error('来源不正确,error:member_id=null');
			}
			
			$trade_fee=M('trade_fee');
			$where=array();//条件查询
			$where['currency_id']=$currency_id;
			$where['member_id']=$member_id;
			if($trade_fee->where($where)->find() == true)
			{

				$this->error('规则已经存在');
			}else{
				$data=array();//post提交的数据
				$data['currency_id']=$currency_id;//币种编号
				$data['member_id']=$member_id;//用户编号
				$data['currency_buy_fee']=$buy;//买入费率
				$data['currency_sell_fee']=$sell;//卖出费率
				$data['ctime']=$_SERVER['REQUEST_TIME'];//请求服务器时间
				$trade_fee->add($data);
				$this->success('添加成功');
				die;
			}
		}
		//手续费规则添加
		$this->display();
	}
	public function delete()
	{
		if(IS_POST)
		{
			$trade_fee=M('trade_fee');
			if($trade_fee->where(['trade_fee_id'=>I('post.trade_fee_id')])->delete())
			{
				$this->ajaxReturn(['code'=>0]);
			}else{
				$this->ajaxReturn(['code'=>1]);
			}
		}
	}
}