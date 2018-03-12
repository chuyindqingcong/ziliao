<?php
namespace Admin\Controller;
use Think\Controller;
class TransrecordController extends Controller {
        //以太坊 监控充币记录
        public function ethrecord()
        {
        	Vendor('phpRPC.phprpc_client');
                $client = new \PHPRPC_Client(C('RPC_SERVER'));
                $result=$client->listaddress();
                //$result=M('currency_user')->where('currency_id=37')->find();
                $arr_address=$result['result'];
                
                //获取最新区块高度
                $arr_height = $client->getBlocknew();

                $new_height=base_convert($arr_height['result'], 16, 10);
                
                //获取上次运算区块高度
                $old_heiht=M('blockheight')->where('id=1')->getField('ethheight');

                //循环计算每个区块
                for($i=$old_heiht+1;$i<=$new_height;$i++)
                {
                	$h='0x'.base_convert($i, 10, 16);
                	$arr_trans = $client->getBlockByNumber($h);
                       
                	foreach ($arr_address as $key => $value) 
                	{
                                if(empty($arr_trans['result']['transactions']))
                                {
                                        continue;
                                }
                		
                		foreach ($arr_trans['result']['transactions'] as $k => $v) 
                		{

                			if($value==$v['to'])
                			{       

                                                
                                $user_id=M()->query("select * from yang_currency_user where currency_id=37 and chongzhi_url='".$value."'");

                				$data['user_id']=$user_id[0]['member_id'];
                				$data['url']=$v['from'];
                                                $data['myurl']=$v['to'];

                				$data['check_time']=base_convert($arr_trans['result']['timestamp'], 16, 10);
                				$data['status']=2;//确认中
                				$data['ti_id']=$v['blockHash'];
                				$data['height']=$i;
                				$data['currency_id']=37;        				
                				$data['num']=base_convert($v['value'], 16, 10)/1000000000000000000;

                				$r=M('tibi')->add($data);
                                                

                			}
                		}
                	}
                }

                
              
                $hh['ethheight']=$new_height;
                M('blockheight')->where("id=1")->save($hh);
        
                


        }
        
        //监控10个确认 eth  ,为用户钱包增加币
        public function eth_confirm_chongzhi()
        {
                Vendor('phpRPC.phprpc_client');
                $client = new \PHPRPC_Client(C('RPC_SERVER'));
                
                
                //获取最新区块高度
               $arr_height = $client->getBlocknew();
               $new_height=base_convert($arr_height['result'], 16, 10);


               $arr_address=M()->query("select * from yang_tibi where currency_id=37 and status=2");

               if(empty($arr_address))
               {
                die;
               }

             
               foreach ($arr_address as $key => $value) {
                      
                      if($value['height']>$new_height-10)
                      {
                        continue;
                      }


                      $h='0x'.base_convert($value['height'], 10, 16);
                      $arr_trans = $client->getBlockByNumber($h);



                      if(!empty($arr_trans['result']['transactions']))
                      {
                        //当前区块的交易信息
                        foreach ($arr_trans['result']['transactions'] as $k => $v)
                        {
                                
                                if($value['ti_id']==$v['blockHash'])
                                {

                                        $data['status']=3;
                                        $con['ti_id']=$v['blockHash'];

                                        $condition['member_id']=$value['user_id'];
                                        $condition['currency_id']=$value['currency_id'];
                                               
                                        $model=new \Think\Model();
                                        $model->startTrans();
                                       

                                        
                                        $r1=M('tibi')->where($con)->save($data);
                                        $r2=M('Currency_user')->where($condition)->setInc("num",$value['num']);

                                        if($r1 && $r2)
                                        {
                                                $model->commit();
                                               
                                        }
                                        else
                                        {
                                                $model->rollback();
                                                
                                        }
                                }
                                else
                                {       
                                        $tag=$tag+1;
                                }

                               


                                        
                        }

                        if($tag==count($arr_trans['result']['transactions']))
                        {
                               $dd['status']=5;
                               $tt['ti_id']=$value['ti_id'];
                               M('tibi')->where($tt)->save($dd);
                        }
        

                      }
                }
                      



               

             

        }


                //以太经典 监控充币记录
        public function etcrecord()
        {
                Vendor('phpRPC.phprpc_client');
                $client = new \PHPRPC_Client(C('ETC_RPC_SERVER'));
                $result=$client->listaddress();
                //$result=M('currency_user')->where('currency_id=37')->find();
                $arr_address=$result['result'];
                
                //获取最新区块高度
                $arr_height = $client->getBlocknew();

                $new_height=base_convert($arr_height['result'], 16, 10);
                
                //获取上次运算区块高度
                $old_heiht=M('blockheight')->where('id=1')->getField('etcheight');

                //循环计算每个区块
                for($i=$old_heiht+1;$i<=$new_height;$i++)
                {
                        $h='0x'.base_convert($i, 10, 16);
                        $arr_trans = $client->getBlockByNumber($h);
                       
                        foreach ($arr_address as $key => $value) 
                        {
                                if(empty($arr_trans['result']['transactions']))
                                {
                                        continue;
                                }
                                
                                foreach ($arr_trans['result']['transactions'] as $k => $v) 
                                {

                                        if($value==$v['to'])
                                        {       

                                                
                                                $user_id=M()->query("select * from yang_currency_user where currency_id=37 and chongzhi_url='".$value."'");

                                                $data['user_id']=$user_id[0]['member_id'];
                                                $data['url']=$v['from'];
                                                $data['myurl']=$v['to'];

                                                $data['check_time']=base_convert($arr_trans['result']['timestamp'], 16, 10);
                                                $data['status']=2;//确认中
                                                $data['ti_id']=$v['blockHash'];
                                                $data['height']=$i;
                                                $data['currency_id']=37;                                        
                                                $data['num']=base_convert($v['value'], 16, 10)/1000000000000000000;

                                                $r=M('tibi')->add($data);
                                                

                                        }
                                }
                        }
                }

                
              
                $hh['etcheight']=$new_height;
                M('blockheight')->where("id=1")->save($hh);
        
                


        }
        
        //监控10个确认 etc  ,为用户钱包增加币
       public function etc_confirm_chongzhi()
        {
                Vendor('phpRPC.phprpc_client');
                $client = new \PHPRPC_Client(C('ETC_RPC_SERVER'));
                
                
                //获取最新区块高度
               $arr_height = $client->getBlocknew();
               $new_height=base_convert($arr_height['result'], 16, 10);


               $arr_address=M()->query("select * from yang_tibi where currency_id=37 and status=2");

               if(empty($arr_address))
               {
                die;
               }

             
               foreach ($arr_address as $key => $value) {
                      
                      if($value['height']>$new_height-10)
                      {
                        continue;
                      }


                      $h='0x'.base_convert($value['height'], 10, 16);
                      $arr_trans = $client->getBlockByNumber($h);



                      if(!empty($arr_trans['result']['transactions']))
                      {
                        //当前区块的交易信息
                        foreach ($arr_trans['result']['transactions'] as $k => $v)
                        {
                                
                                if($value['ti_id']==$v['blockHash'])
                                {

                                        $data['status']=3;
                                        $con['ti_id']=$v['blockHash'];

                                        $condition['member_id']=$value['user_id'];
                                        $condition['currency_id']=$value['currency_id'];
                                               
                                        $model=new \Think\Model();
                                        $model->startTrans();
                                       

                                        
                                        $r1=M('tibi')->where($con)->save($data);
                                        $r2=M('Currency_user')->where($condition)->setInc("num",$value['num']);

                                        if($r1 && $r2)
                                        {
                                                $model->commit();
                                               
                                        }
                                        else
                                        {
                                                $model->rollback();
                                                
                                        }
                                }
                                else
                                {       
                                        $tag=$tag+1;
                                }

                               


                                        
                        }

                        if($tag==count($arr_trans['result']['transactions']))
                        {
                               $dd['status']=5;
                               $tt['ti_id']=$value['ti_id'];
                               M('tibi')->where($tt)->save($dd);
                        }
        

                      }
                }
                      



               

             

        }


}