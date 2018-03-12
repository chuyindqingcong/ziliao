<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class IndexController extends CommonController {
 	public function _initialize(){
 		parent::_initialize();
 	}
	//空操作
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
    public function index(){
         if(is_mobile()==true)
        {
            $this->redirect('Mobile/Index/index');
        }       

        //页面右方公告，提示，资信 
        $art_model = D('Article');
        $info1 = $art_model->info(1);
        $info_red1 = $art_model->info_red(1);//标红
        $info2 = $art_model->info(2);        //非标红
        $info_red2 = $art_model->info_red(2);//标红
        $this->assign('info1',$info1);       //非标红
        $this->assign('info_red1',$info_red1);
        $this->assign('info2',$info2);
        $this->assign('info_red2',$info_red2);
        

        //幻灯
        $flash=M('Flash')->where('type=1')->order('sort')->limit(6)->select();
        $this->assign('flash',$flash);
        //幻灯*4
        $index_ad_1=M('Flash')->where('type=5')->order('sort')->find();
        $this->assign('index_ad_1',$index_ad_1);
        //币种
        $currency=$this->currency_4();
        foreach ($currency as $k=>$v){
            $list=$this->getCurrencyMessageById($v['currency_id']);
            $currency[$k]=array_merge($list,$currency[$k]);
            $list['new_price']?$list['new_price']:0;
            $currency[$k]['currency_all_money'] = floatval($v['currency_all_num'])*$list['new_price'];
        }
        //*********选择进盟币,安全可信赖begin*******
        $all_money = M('Trade')->sum('money');
        $all_money = $this->config['transaction_false']+$all_money;
//        $all_money = 212315984125.123;
        $all_money = (string)round($all_money);
        for($i=0;$i<strlen($all_money);$i++){
            $arr[strlen($all_money)-1-$i] = $all_money[$i];
//            $arr[$i] = $all_money[$i];
        }
        $this->assign('arr',$arr);
        //*********选择进盟币,安全可信赖end*******

        //*******众筹begin*******/////
        $issue_list=M('Issue')
            ->field(C("DB_PREFIX")."issue.*,".C("DB_PREFIX")."currency.currency_name as name")
            ->join("left join ".C("DB_PREFIX")."currency on ".C("DB_PREFIX")."currency.currency_id=".C("DB_PREFIX")."issue.currency_id")
            ->order(' id desc ')->select();
        $this->assign('issue_list',$issue_list);
        //*******众筹end*******/////
        $sum_money = num_format($all_money);


        $news = M('Article')->where("position_id ='101'")->order('showdate desc')->limit("13")->select();
        for ($i=0; $i < count($news); $i++) { 
            $sub = strip_tags(htmlspecialchars_decode($news[$i]['content']));     //预定义的 HTML 实体转换为字符
                        $qian=array(" ","　","\t","\n","\r","&nbsp;");$hou=array("","","","","");
            $news[$i]['art_sub'] = str_replace($qian,$hou,msubstr($sub, $start=0, 50, $charset="utf-8", $suffix=false));
                        //  截取1段字符作为摘要顺便去掉空格
        }
        $this->assign('news',$news);     //行业资讯

        $bizhong = M('Article')->where("position_id ='102'")->order('showdate desc')->limit("10")->select();
        for ($i=0; $i < count($bizhong); $i++) { 
            $sub = strip_tags(htmlspecialchars_decode($bizhong[$i]['content']));     //预定义的 HTML 实体转换为字符
                        $qian=array(" ","　","\t","\n","\r","&nbsp;");$hou=array("","","","","");
            $bizhong[$i]['art_sub'] = str_replace($qian,$hou,msubstr($sub, $start=0, 50, $charset="utf-8", $suffix=false));
                        //  截取1段字符作为摘要顺便去掉空格
        }
        $this->assign('bizhong',$bizhong);     //币种动态

        $gonggao = M('Article')->where("position_id ='103'")->order('article_id desc')->limit("9")->select();
        $this->assign('gonggao',$gonggao);     //平台公告

        $zhengce = M('Article')->where("position_id ='104'")->order('showdate desc')->limit("9")->select();
        $this->assign('zhengce',$zhengce);     //最新政策


        $this->assign('sum_money',$sum_money);
        $this->assign('currency',$currency);
        $this->assign('empty','暂无数据');
        $this->display();
     }
     public function aboutus(){
        $this->display();
     }

    public function alipay_call()
    {
        require_once THINK_PATH.'Library/Vendor/Alipay/alipay.config.php';
        require_once THINK_PATH.'Library/Vendor/Alipay/lib/alipay_notify.class.php';

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

                $order = M('Pay')->where("order_id='$out_trade_no'")->find();
                $data['status'] = 1;
                $data['pay_id'] = $order['pay_id'];
                $m = M('Pay')->save($data);   //更新支付状态

                $id = $order['member_id'];
                $user = M('Member')->where("member_id='$id'")->find();
                $date['rmb'] = $user['rmb']+$order['money'];
                $date['member_id'] = $id;
                $n = M('Member')->save($date);   //更新用户账户余额

           

        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }

    public function testrpc()
    {
        Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client(C('RPC_SERVER'));
       
        $from="0xb8df38fcea311798af6b6229ad083640ac470efc";
        $to="0x658f7e8b4d1e6264d48e4636e94c3ee3a968cb8a";
        $i=0.06;
        $i=$i*1000000000000000000;

        $value='0x'.base_convert($i, 10, 16);

        //$result=$client->personal_transaction($from,$to,$value,'ETHetc2jjw!q2w3e');
        //$result=$client->getBalance($from);
        $condition['currency_id']=37;
        $condition['chongzhi_url']="is not null";
        $result=M('currency_user')->where($condition)->find();
        var_dump($result);

       
 
        

    }

   

        
}