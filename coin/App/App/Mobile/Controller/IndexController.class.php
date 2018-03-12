<?php
namespace Mobile\Controller;
use Common\Controller\CommonController;
/**
* 2017/7/18
*手机入口控制器
*/
class IndexController extends CommonController
{
	public function _initialize(){
	 	$this->assign('session',$_SESSION['USER_KEY_ID']);
	 	$this->assign('h_status',1);  //头部变蓝状态
 		parent::_initialize();
 	}
	public function index()
	{
		//幻灯
		$flash=M('Flash')->where('type=5')->order('sort')->limit(6)->select();
		$this->assign('flash',$flash);
		$gonggao = M('Article')->where("position_id ='103'")->order('add_time desc')->find();
        $this->assign('gonggao',$gonggao);     //平台公告
		$this->display();
		//手机首页
	}
	public function header()
	{
		$this->display();
	}

	public function json_cur(){
        $currency = M('Currency')->where('is_line=1')->order('sort')->select();//时间降序排列，越接近当前时间越高
		$c = M('Currency')->where('is_line=1')->order('sort')->select();
		
		foreach ($currency as $k=>$v){
            $list=$this->getCurrencyMessageById($v['currency_id']);
            $currency[$k]=array_merge($list,$currency[$k]);
            $list['new_price']?$list['new_price']:0;
            $currency[$k]['currency_all_money'] = floatval($v['currency_all_num'])*$list['new_price'];
            $currency[$k]['st'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."'><img src='".$currency[$k]['currency_logo']."' style='width:0.33rem;height:0.33rem;margin-right:0.1rem;' />".$currency[$k]['currency_name']."</a>";
             $currency[$k]['new_price'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."'>".$currency[$k]['new_price']."</a>";
            if ($currency[$k]['24H_change']>0) {
            	$currency[$k]['24H_change'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."' class='green'>".$currency[$k]['24H_change']."%</a>";
            }elseif ($currency[$k]['24H_change']<0) {
            	$currency[$k]['24H_change'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."' class='red'>".$currency[$k]['24H_change']."%</a>";
            }
            if ($currency[$k]['24H_done_money']=='') {
            	$currency[$k]['24H_done_money'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."'>0</a>";
            }else {
            	$currency[$k]['24H_done_money'] = "<a href='".U('Order/index',array('currency'=>$currency[$k]['currency_mark']))."'>".sprintf("%.4f", $currency[$k]['24H_done_money'])."</a>";
            }
        }
		$this->ajaxReturn($currency);
		//print_r($currency);
	}
}