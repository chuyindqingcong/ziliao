<?php
namespace Admin\Controller;
use Think\Controller;
class TransrecordController extends Controller {

	public function ethrecord()
	{
	Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client(C('RPC_SERVER'));
        $result=$client->listaddress();
        //$result=M('currency_user')->where('currency_id=37')->find();
        $arr_address=$result['result'];
        
        //获取最新区块高度
        $arr_height = $client->getBlocknew();

        $new_height=4155887;//base_convert($arr_height['result'], 16, 10);
        
        //获取上次运算区块高度
        $old_heiht=M('blockheight')->where('id=1')->getField('ethheight');

        //循环计算每个区块
        for($i=$old_heiht+1;$i<=$new_height;$i++)
        {
        	$h='0x'.base_convert($i, 10, 16);
        	$arr_trans = $client->getBlockByNumber($h);
                // if($i==4155884)
                // {
                //         print_r("<pre>");
                //       print_r($arr_trans) ;
                //         print_r("<pre>");
                //         die;
                // }
               
        	foreach ($arr_address as $key => $value) 
        	{
        		
        		foreach ($arr_trans['result']['transactions'] as $k => $v) 
        		{


                                

        			if($value==$v['to'])
        			{       

                                        
                                        $user_id=M()->query("select * from yang_currency_user where currency_id=37 and chongzhi_url='".$value."'");

        				$data['user_id']=$user_id[0]['member_id'];
        				$data['url']=$v['from'];
        				$data['check_time']=base_convert($arr_trans['result']['timestamp'], 16, 10);
        				$data['status']=2;//充值成功
        				$data['ti_id']=$v['blockHash'];
        				$data['height']=$i;
        				$data['currency_id']=37;        				
        				$data['actual']=base_convert($v['value'], 16, 10)/1000000000000000000;

        				$r=M('tibi')->add($data);
                                        if($r)
                                        {       
                                                $h['ethheight']=$i;
                                                M('blockheight')->where("id=1")->save($h);
                                        }

        			}
        		}
        	}
        }
        


	}

}