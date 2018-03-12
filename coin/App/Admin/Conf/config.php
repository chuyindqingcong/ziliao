<?php
return array(
	//'配置项'=>'配置值'
	'SESSION_AUTO_START' => true, //是否开启session
	//***********************************SESSION设置**********************************
    'SESSION_OPTIONS'         =>  array(
        'expire'              =>  3600*8,                      //SESSION保存15天
        'use_trans_sid'       => 1,                               //跨页传递
        'use_only_cookies'    =>  0,                               //是否只开启基于cookies的session的会话方式
    ),
);