<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class ArtController extends CommonController {
	//空操作
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
	/**
	 * 消息整体列表展示
	 * return array 文章分类，分页，查询信息
	 */
	public function index(){
		//检测从哪里进来到这里，如果有参数，判断分类（position）1为系统，2为资源，没有参数，正常显示
		$article = M('Article');
		$id = intval(I('get.id'));//强制转换为数字型
		$category = M('Article_category')->where("id='$id'")->find();
		$this->assign('cate',$category); //分类名称与ID
		$this->assign('id',$id); //get.id
		if(!empty($id)){
			$where['position_id'] = $id;
		}

		$count = $article->where($where)->count();//根据分类查找数据数量
		$Page = new \Think\Page($count,8);//实例化分页类，传入总记录数和每页显示数
		$Page->lastSuffix = true;//最后一页不显示为总页数
        
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('first','首页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
		$page_show = bootstrap_page_style($Page->show());//重点在这里

		

		$list = $article->where($where)->order('article_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();//时间降序排列，越接近当前时间越高
		foreach ($list as $k=>$v){
			$list[$k]['title']=strip_tags(html_entity_decode($v['title']));
			$list[$k]['content']=strip_tags(html_entity_decode($v['content']));
		}
		
		$this->assign('page',$page_show);
		$this->assign('list',$list);

		//幻灯
		$flash=M('Flash')->where('type=4')->order('sort')->limit(6)->select();
		$this->assign('flash',$flash);
		$flash1=M('Flash')->where('type=2')->order('sort')->find();
		$this->assign('flash1',$flash1);
		$flash2=M('Flash')->where('type=3')->order('sort')->find();
		$this->assign('flash2',$flash2);

		//热门文章
		$hot = M('Article')->order('views desc')->limit(6)->select();
		$this->assign('hot',$hot);

		$this->display();
     }
     /**
	 * 页面左边为选中的信息，通过url传递的id获取，并获取到该文章的position_id，用position_id来确定右边的分类名和分类中需要的多个标题
	 * return array 文章分类，分页，查询信息
	 */
    public function details(){
    	$article = M('Article');
    	$art_cat = M('Article_category');
    	//index 传id
    	$id = I('id');
    	$team_id = I('get.team_id');
    	if(!empty($id)){
    	 $where['article_id'] = $id;
    	}
    	if(!empty($team_id)){
    	 $where['article_id'] = $team_id;
    	}

    	$count = $article->where($where)->count();
    	$m = $article->where($where)->setInc('views');
		if($count==0){
			$this->display('Public:404');
			return;
		}
			
		$art_one = $article->where($where)->find();//查找到单一的文章	
		$c_id = $art_one['position_id'];
		$category = M('Article_category')->where("id='$c_id'")->find();
		$this->assign('cate',$category); //分类名称与ID
		//将数据库中html标签字符串化，显示
		$art_one['title']=html_entity_decode($art_one['title']);
		$art_one['content']=html_entity_decode($art_one['content']);
		//根据单一文章的position_id,查找一定量的同类型标题
		$where_artlist['position_id'] = $art_one['position_id'];
		$where_artlist['article_id'] = array('GT',$id);
    	$art_list = $article->where($where_artlist)->order('article_id desc')->find();
    	$where_artlist1['position_id'] = $art_one['position_id'];
		$where_artlist1['article_id'] = array('lt',$id);
		$art_list1 = $article->where($where_artlist1)->order('article_id desc')->find();

		if($art_one['seo'] == true)
    	{
    		//seo关键字初始化
    	    $this->assign('keywords',$art_one['seo']);
        }
    	$this->assign('article',$art_one);
		$this->assign('art_one',$art_one);		
    	$this->assign('art_list',$art_list);
    	$this->assign('art_list1',$art_list1);

    	$flash1=M('Flash')->where('type=2')->order('sort')->find();
		$this->assign('flash1',$flash1);
		$flash2=M('Flash')->where('type=3')->order('sort')->find();
		$this->assign('flash2',$flash2);

		//热门文章
		$hot = M('Article')->order('views desc')->limit(6)->select();
		$this->assign('hot',$hot);

    	$this->display();	
    }

    public function newbi(){
    	$list=M('Newbi')->where('currency_id='.$_GET['currency_id'])->find();
    	$this->assign("list",$list);
    	$newlist=M('Newbi')->where('currency_id!='.$_GET['currency_id'])->select();
    	$this->assign("newlist",$newlist);
    	$this->display();
    }

}