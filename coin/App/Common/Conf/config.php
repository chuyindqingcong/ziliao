<?php
return array (
		// 加载语言包
		'LOAD_EXT_CONFIG' => 'adminEntrance,db,wxlogin', // 加载扩展配置文件
		'LANG_AUTO_DETECT' => false, // 关闭语言的自动检测，如果你是多语言可以开启
		'LOG_RECORD' => true, // 开启日志记录
		'LANG_SWITCH_ON' => TRUE, // 开启语言包功能，这个必须开启
		
		'DEFAULT_LANG' => 'zh-cn', // zh-cn文件夹名字 /lang/zh-cn/common.php
		'TMPL_PARSE_STRING' => array (
				'__ADMINCSS__' => '/Public/admin/css',
				'__ADMINJS__' => '/Public/admin/js',
				'__ADMINIMG__' => '/Public/admin/images',
				'__HOMECSS__' => '/Public/home/css',
				'__HOMEJS__' => '/Public/home/js',
				'__HOMEIMG__' => '/Public/home/images',
				'__STATICCSS__' => '/Public/static/css',
				'__STATICJS__' => '/Public/static/js',
				'__STATICCIMG__' => '/Public/static/images',
				'__ADMIN__' => '/Public/admin',
				'__HOME__' => '/Public/Home',
				'__STATIC__' => '/Public/static',
				'__PLUGINS__' => '/Public/plugins',
				'__PLUGINSJS__' => '/Public/plugins/js',
				'__PLUGINSCSS__' => '/Public/plugins/css',
				'__PUBLICNEWJS__' => '/Public/new/js',
				'__PUBLICNEWCSS__' => '/Public/new/css' 
		)
		,
		'SZ_QQ_SCOPE'=> 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr',
		'SZ_QQ_APP_ID'=> '101293145',
		'SZ_QQ_CALLBACK'=> 'http://www.bsd38.com/Home/Login/qqLogin',
		'SZ_QQ_APP_KEY'=> '50388dac9fd9fea0ac2e842b46fb9782',
		// URl
		'URL_CASE_INSENSITIVE' => true,
		'URL_MODEL' => 2,
		'URL_HTML_SUFFIX' => 'html',
		'URL_ROUTER_ON' => true, // 是否开启URL路由
		                                  // Cookie
		'COOKIE_PREFIX' => 'odr',
		
		
		'APP_GROUP_LIST' => 'Home,Admin,Mobile', // 项目分组设定
		                                  // 模版
		'TMPL_ACTION_SUCCESS' => './App/Common/jump.html',
		'TMPL_ACTION_ERROR' => './App/Common/jump.html',
		
		'TMPL_L_DELIM' => '{', // 模板引擎普通标签开始标记
		'TMPL_R_DELIM' => '}', // 模板引擎普通标签结束标记
		                                 
		// 显示错误信息
		'SHOW_ERROR_MSG' => true,
//          'SHOW_PAGE_TRACE' =>true,
		//关闭debug模式错误跳入404 



//   		'TMPL_EXCEPTION_FILE' =>'./404.html' 

		'THUMP_PATH'=>'C:/phpStudy/WWW/virtual/Uploads',
		
		
		//邮件配置
	'MAIL_HOST' =>'smtp.163.com',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    
  
    'MAIL_FROMNAME'=>'第九交易网',//发件人姓名
   
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
    'RPC_SERVER'=>'http://106.14.212.207/index.php/Home/Server',
    'ETC_RPC_SERVER'=>'http://47.91.231.187/index.php/Home/Server',
    'ETH_WALLET'=>'0xb8df38fcea311798af6b6229ad083640ac470efc',
    'ETC_WALLET'=>'AA'
   
);
