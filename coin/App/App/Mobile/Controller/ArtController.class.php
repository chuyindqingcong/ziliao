<?php
namespace Mobile\Controller;
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
	public function art_list(){
		$gonggao = M('Article')->where("position_id ='103'")->order('add_time desc')->limit("20")->select();
        $this->assign('gonggao',$gonggao);     //平台公告
        $dongtai = M('Article')->where("position_id ='102'")->order('add_time desc')->limit("20")->select();
        $this->assign('dongtai',$dongtai);     //币种动态
        $zixun = M('Article')->where("position_id ='101'")->order('add_time desc')->limit("20")->select();
        $this->assign('zixun',$zixun);         //行业资讯
        $zhengce = M('Article')->where("position_id ='104'")->order('add_time desc')->limit("20")->select();
        $this->assign('zhengce',$zhengce);     //最新政策
		$this->display();
     }
     /**
	 * 页面左边为选中的信息，通过url传递的id获取，并获取到该文章的position_id，用position_id来确定右边的分类名和分类中需要的多个标题
	 * return array 文章分类，分页，查询信息
	 */
    public function details(){
    	$id = I('get.id');
    	$art_one = M('Article')->where("article_id='$id'")->find();//查找到单一的文章	
    	//将数据库中html标签字符串化，显示
		$art_one['title']=html_entity_decode($art_one['title']);
		$art_one['content']=html_entity_decode($art_one['content']);
		$this->assign('article',$art_one);
    	$this->display();	
    }

}