<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class HelpController extends CommonController {
	function _initialize(){
		parent::_initialize();
		$this->assign('head_status','5');
	}
	public function  _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Public:404');
	}
	
	public function index(){
    	$article = M('Article');
    	//$article_id为帮助中心内传来的文章id
    	$article_id = I('get.article_id');
    	$where['position_id'] = '60';
		if($article_id){
			$where['article_id'] = $article_id;
			$art_one = $article->where($where)->find();
		}else{
			$art_one = $article->where($where)->find();
			$article_id = $art_one['article_id'];
		}
		//数据库是否有数，没有返回404
//     	$count = $article->where($where)->count();
// 		if($count==0){
// 			$this->display('Public:404');
// 			return;
// 		}

		$art_list = $article->where("position_id='60'")->select();
		//var_dump($art_list);die;
		//将数据库中html标签字符串化，显示
			$art_one['title']=html_entity_decode($art_one['title']);
			$art_one['content']=html_entity_decode($art_one['content']);

    	$this->assign('article_id',$article_id);	
		$this->assign('art_one',$art_one);	
		$this->assign('art_list',$art_list);		
    	$this->display();	
    }

}