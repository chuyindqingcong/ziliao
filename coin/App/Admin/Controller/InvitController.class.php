<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
use Think\Page;
class InvitController extends AdminController {
    protected  $day='invit_tay';//有效期天数key
    protected  $rebate='invit_onerebate';//一级返利key
    protected  $member_id='invit_member_id';
  public function config()
  {
      //返利设置
      $value=array();
      $config=M('config');
      $value[$this->day]=$config->where(['key'=>$this->day])->find();
      $value[$this->rebate]=$config->where(['key'=>$this->rebate])->find();
      $value[$this->member_id]=$config->where(['key'=>$this->member_id])->find();
      $this->assign('tay',$value[$this->day]['value']);//默认有效期天数
      $this->assign('onerebate',$value[$this->rebate]['value']);//一级返利
      $this->assign('member_id',$value[$this->member_id]['value']);//一级返利
      $this->display();
  }
  public function rebate()
  {
      //返利记录
      $list=array();//查询结果
      $where=array();
      if(I('get.invit_id')==true)
      {
        $invit_id=I('get.invit_id');
        $where['invit_id']=$invit_id;
      }else{

      }
      if(I('get.start')==true && I('get.endtime')==true)
      {
        $start=strtotime(I('get.start'));
        $endtime=strtotime(I('get.endtime'));
        $where['ctime'] = array('BETWEEN',array($start,$endtime));
      }else if(I('get.start')==true)
      {
        $start=strtotime(I('get.start'));
        $where['ctime']=array('EGT',$start);
      }else if(I('get.endtime')==true)
      {
        $endtime=I('get.endtime');
        $where['ctime']=array('ELT',$endtime);
      }
      if(I('get.status')==1)
      {
        $where['status']=0;
      }else if(I('get.status')==2)
      {
        $where['status']=1;
      }
      $invit=D('invit');
      $count      =  $invit->where($where)->count();
      $Page       = new Page($count,20);
      $show       = $Page->show();
      $list=$invit->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('invit_id desc')->select();
      $cfee=array();
      $where['rebatetype']=1;//获取返利为币的类型
      $cfee['coin']=$invit->where($where)->sum('cfee');
      $where['rebatetype']=2;//获取返利为RMB的类型
      $cfee['rmb']=$invit->where($where)->sum('cfee');
      $this->assign('page',$show);
      $this->assign('list',$list);
      $this->assign('cfee',$cfee);
      $this->display();
  }
  public function configUpdate()
  {
      //返利设置更新
      $config=M('config');
      $days=I('post.day');//提交的默认有效期天数
      $onerebate=I('post.onerebate');//提交的一级返利
      $member_id=I('post.invit_member_id');
      $res[]=$config->where(['key'=>$this->day])->save(['value'=>$days]);
      $res[]=$config->where(['key'=>$this->rebate])->save(['value'=>$onerebate]);
      $res[]=$config->where(['key'=>$this->member_id])->save(['value'=>$member_id]);
      $this->success('修改成功');
  }
  public function executeRebate()
  {
     $list=array();//查询结果
      $where=array();
      $where['status']=0;
      if(I('get.invit_id')==true)
      {
        $invit_id=I('get.invit_id');
        $where['invit_id']=$invit_id;
      }else{

      }
      if(I('get.start')==true && I('get.endtime')==true)
      {
        $start=strtotime(I('get.start'));
        $endtime=strtotime(I('get.endtime'));
        $where['ctime'] = array('BETWEEN',array($start,$endtime));
      }else if(I('get.start')==true)
      {
        $start=strtotime(I('get.start'));
        $where['ctime']=array('EGT',$start);
      }else if(I('get.endtime')==true)
      {
        $endtime=I('get.endtime');
        $where['ctime']=array('ELT',$endtime);
      }
      $invit=M('invit');
      $list=$invit->where($where)->order('invit_id desc')->select();
      $currency=M('currency_user');
      $member=M('member');
      $config=M('config')->where(['key'=>$this->member_id])->field('value')->find();//获取默认的返利用户
      $trade=M('trade');
      // $ratio=$config['value'];
      $number=0;
      foreach ($list as $key => $value)
      {
        $res=array();
     
        M()->startTrans();
        // $member->where(['member_id'=>$value['member_id']])->setInc();
        if($value['order_type']=='buy')
        {
          $info=$trade->where(['trade_id'=>$value['order_id']])->find();
          if($info){
          // dump($key);
          if($currency->where(['member_id'=>$value['member_id'],'currency_id'=>$info['currency_id']])->find()==false)
          {
            //没有currency_user数据
              $data=array();
              $data['member_id']=$value['member_id'];
              $data['currency_id']=$info['currency_id'];
              $data['num']=$value['rebate'];
              $data['status']=1;
              $res[]=$currency->add($data);
          }
          $surplus=$value['cfee']-$value['rebate'];//获取剩余的金额
          if($currency->where(['member_id'=>$config['value'],'currency_id'=>$info['currency_id']])->find()==false)
          {
            //没有currency_user数据
              $data=array();
              $data['member_id']=$config['value'];
              $data['currency_id']=$info['currency_id'];
              $data['num']=$surplus;
              $data['status']=1;
              $res[]=$currency->add($data);
          }
          $number++;
          $res[]=$currency->where(['member_id'=>$value['member_id'],'currency_id'=>$info['currency_id']])->setInc('num',$value['rebate']);//设置返利的币种数
          if($surplus>0)
          {
              $res[]=$currency->where(['member_id'=>$config['value'],'currency_id'=>$info['currency_id']])->setInc('num',$surplus);
          }
          $res[]=$invit->where(['invit_id'=>$value['invit_id']])->setField(['status'=>1,'endtime'=>time()]);//修改返利记录的状态
          }
        }
        else if($value['order_type']=='sell')
        {
            $number++;
            $res[]=$member->where(['member_id'=>$value['member_id']])->setInc('rmb',$value['rebate']);
            $surplus=$value['cfee']-$value['rebate'];
            if($surplus>0)
            {
              $res[]=$member->where(['member_id'=>$value['member_id']])->setInc('rmb',$surplus);
            }
            $res[]=$invit->where(['invit_id'=>$value['invit_id']])->setField(['status'=>1,'endtime'=>time()]);
        }
        foreach ($res as $key => $value) 
        {
          if($value==true)
          {
              M()->commit();
          }else{
             M()->rollback();
          }
        }
      }
      $this->ajaxReturn(['code'=>0,'number'=>$number]);
  }

}