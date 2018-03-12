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
        // //判定是否手机登录
        //  if(is_mobile()==true)
        // {
        //     $this->redirect('Mobile/Index/index');
        // }       

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
        
        //币种介绍
        $bi_list=M('Newbi')->where("status=1")->order('add_time desc')->limit('12')->select();
        $this->assign('bi_list',$bi_list);

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


        $news1 = M('Article')->where("position_id ='101' AND zhiding=0 AND toutiao=0")->order('showdate desc')->limit("16")->select();
        $this->assign('news1',$news1);     //行业资讯

        $news = M('Article')->where("position_id ='101' AND zhiding=1 AND toutiao=0")->order('showdate desc,article_id desc')->limit("10")->select();
        for ($i=0; $i < count($news); $i++) { 
            if (empty($news[$i]['atter'])) {
                    $sub = strip_tags(htmlspecialchars_decode($news[$i]['content']));     //预定义的 HTML 实体转换为字符
                    $qian=array(" ","　","\t","\n","\r","&nbsp;");$hou=array("","","","","");
                    $news[$i]['art_sub'] = str_replace($qian,$hou,msubstr($sub, $start=0, 50, $charset="utf-8", $suffix=false));
                        //  截取1段字符作为摘要顺便去掉空格
            }else{
                    $news[$i]['art_sub'] = $news[$i]['atter'];
            }

        }
        $this->assign('news',$news);     //行业资讯

        $bizhong = M('Article')->where("position_id ='102' AND zhiding=1 AND toutiao=0")->order('showdate desc,article_id desc')->limit("1")->select();
        for ($i=0; $i < count($bizhong); $i++) { 
            if (empty($news[$i]['atter'])) {
                $sub = strip_tags(htmlspecialchars_decode($bizhong[$i]['content']));     //预定义的 HTML 实体转换为字符
                $qian=array(" ","　","\t","\n","\r","&nbsp;");$hou=array("","","","","");
                $bizhong[$i]['art_sub'] = str_replace($qian,$hou,msubstr($sub, $start=0, 50, $charset="utf-8", $suffix=false));
                        //  截取1段字符作为摘要顺便去掉空格
            }else{
                $news[$i]['art_sub'] = $news[$i]['atter'];
            }
        }
        $this->assign('bizhong',$bizhong);     //币种动态

        $bizhong1 = M('Article')->where("position_id ='102' AND zhiding=0 AND toutiao=0")->order('showdate desc')->limit("10")->select();
        $this->assign('bizhong1',$bizhong1);     //币种动态

        $gonggao = M('Article')->where("position_id ='103' AND toutiao=0")->order('article_id desc')->limit("7")->select();
        $this->assign('gonggao',$gonggao);     //平台公告

        $zhengce = M('Article')->where("position_id ='104' AND toutiao=0")->order('showdate desc')->limit("13")->select();
        $this->assign('zhengce',$zhengce);     //最新政策

        $dongtai = M('Article')->where("position_id ='106' AND toutiao=0")->order('showdate desc')->limit("2")->select();
        for ($i=0; $i < count($dongtai); $i++) { 
            if (empty($news[$i]['atter'])) {
                $sub = strip_tags(htmlspecialchars_decode($dongtai[$i]['content']));     //预定义的 HTML 实体转换为字符
                $qian=array(" ","　","\t","\n","\r","&nbsp;");$hou=array("","","","","");
                $dongtai[$i]['art_sub'] = str_replace($qian,$hou,msubstr($sub, $start=0, 50, $charset="utf-8", $suffix=false));
                        //  截取1段字符作为摘要顺便去掉空格
            }else{
                $news[$i]['art_sub'] = $news[$i]['atter'];
            }
        }
        $this->assign('dongtai',$dongtai);     //币动态

        $toutiao = M('Article')->where("zhiding=0 AND toutiao=1")->order('showdate desc')->limit("4")->select();
        $this->assign('toutiao',$toutiao);     //币头条

        $toutiao1 = M('Article')->where("zhiding=1 AND toutiao=1")->order('showdate desc')->limit("2")->select();
        $this->assign('toutiao1',$toutiao1);     //币头条


        $this->assign('sum_money',$sum_money);
        $this->assign('currency',$currency);
        $this->assign('empty','暂无数据');
        $this->assign('accountMoney',$this->getAccountCount());
        $this->display();
     }
     public function aboutus(){
        $this->display();
     }
    private function getAccountCount()
    {
        $where['member_id']=$_SESSION['USER_KEY_ID'];
        $currency_user=M('Currency_user')
            ->join("left join ".C('DB_PREFIX')."currency on ".C('DB_PREFIX')."currency.currency_id=".C('DB_PREFIX')."currency_user.currency_id")
            ->field(''.C('DB_PREFIX').'currency_user.*,('.C('DB_PREFIX').'currency_user.num+'.C('DB_PREFIX').'currency_user.forzen_num) as count,'.C('DB_PREFIX').'currency.currency_name,'.C('DB_PREFIX').'currency.currency_mark,'.C('DB_PREFIX').'currency.currency_logo')
            ->where($where)->order('sort')->select();
        $allcount=null;
        foreach ($currency_user as $k=>$v){
            $Currency_message=$this->getCurrencyMessageById($v['currency_id']);
            $currency_user[$k]['new_price']=$Currency_message['new_price'];
            $currency_user[$k]['cmoney']=self::listCountMoney($currency_user[$k]);
            $allcount+=$currency_user[$k]['cmoney'];
        }
        $member_rmb=$this->member;
        $allcount+=$member_rmb['rmb']+$member_rmb['forzen_rmb'];
        return $allcount;
    }
    /***
    *   获取帐号列表单个总资产
    *
    */
    static private function listCountMoney($arr)
    {
        $info=$arr;
        $res=($info['num']+$info['forzen_num'])*$info['new_price'];
        return $res;
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
       
        $from="0xdee735ca962080580fb92f083ede27cdedbbd821";
        //$to="0x658f7e8b4d1e6264d48e4636e94c3ee3a968cb8a";
        // $i=0.06;
        // $i=$i*1000000000000000000;

        // $value='0x'.base_convert($i, 10, 16);

        // //$result=$client->personal_transaction($from,$to,$value,'ETHetc2jjw!q2w3e');
        $result=$client->listaddress();
        //$result=$client->getBlocknew();
        
        //$result=$client->getNewaddress('!q2w3e');
        // $condition['currency_id']=37;
        // $condition['chongzhi_url']="is not null";
        // $result=M('currency_user')->where($condition)->find();
        var_dump($result);

       
 
        

    }

    public function test()
    {
        $where['currency_id']=37;
        $time=time();
        //一天前的时间
        $old_time=strtotime(date('Y-m-d',$time));
        //最新价格
        $order='add_time desc';
        

        $rs=M('Trade')->where($where)->order($order)->find();

        $data['new_price']=$rs['price'];
        //判断价格是升是降
        $re1=M('Trade')->where($where)->where("add_time<$old_time")->order($order)->find();
        if($re1['price']>$rs['price']){
            //说明价格下降
            $data['new_price_status']=0;
        }else{
            $data['new_price_status']=1;
        }
        //24H涨跌
        $tg=$time-60*60*24;
       
        $condition['add_time'] = array('between',array($tg,$time));
        $conditon['currency_id']=37;
        
        $re2=M('Trade')->where($conditon)->order('price')->find();
        var_dump($re2);

       // p($rs);

        die;
        if ($re['price']!=0){
            $data['24H_change']=sprintf("%.2f", ($rs['price']-$re['price'])/$re['price']*100);
            if($data['24H_change']==0){
                $data['24H_change']=100;
            }
        }else {
            $data['24H_change']=100;
        }
    }

   

        
}