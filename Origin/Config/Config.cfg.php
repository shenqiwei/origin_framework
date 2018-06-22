<?php
/**
-*- coding: utf-8 -*-
-*- system OS: windows2008 -*-
-*- work Tools:Phpstorm -*-
-*- language Ver: php7.1 -*-
-*- agreement: PSR-1 to PSR-11 -*-
-*- filename: IoC.Origin.Config.Config-*-
-*- version: 1.0 -*-
-*- structure: common framework -*-
-*- designer: 沈启威 -*-
-*- developer: 沈启威 -*-
-*- partner: 沈启威 -*-
-*- chinese Context:
-*- create Time: 2017/01/01 9:35
-*- update Time: 2017/01/12 10:45
-*- IoC 主配置文件
 */
return array(
    // 应用主目录
    'ROOT_APPLICATION' => 'Apply/', // 应用控制器目录
    // 默认访问文件根目录
    'DEFAULT_APPLICATION' => 'Home/',
    // 应用目录
    'APPLICATION_BUFFER' => 'Buffer/', // 缓存文件目录（可以在配置文件中设置是否使用缓存）
    'APPLICATION_METHOD' => 'Common/', // 公共方法文件目录，系统公共方法公共调用文件存储位置，内建应用
    'APPLICATION_FUNCTION' => 'Common/', // 公共方法文件目录，系统公共方法公共调用文件存储位置
    'APPLICATION_CONFIG' => 'Config/', // 开发者或用户自定义或改写系统配置文件存储位置
    'APPLICATION_CONTROLLER' => 'Controller/', // 执行程序文件目录
    'APPLICATION_MODEL' => 'Model/', // 数据操作语句文件目录
    'APPLICATION_VIEW' => 'View/', // 模板（Template）文件目录
    // 插件目录
    'ROOT_PLUGIN' => 'PlugIn/', // 应用插件目录
    // 资源目录
    'ROOT_RESOURCE' => 'Resource/', // 资源主目录
    //web结构下应用
    'ROOT_RESOURCE_JS' => 'Jscript', // javascript资源目录
    'ROOT_RESOURCE_MEDIA' => 'Media', // 多媒体资源目录
    'ROOT_RESOURCE_STYLE' => 'Style', // 样式表资源目录
    'ROOT_RESOURCE_TEMP' => 'Template', // 模板资源目录
    'ROOT_RESOURCE_PLUGIN' => 'Plug-In', //第三方插件
    'ROOT_RESOURCE_PUBLIC' => 'Public', //公共文件目录
    'ROOT_RESOURCE_UPLOAD' => 'Upload', //上传目录
    // 命名空间跟名
    'ROOT_NAMESPACE' => '\\Apply', //根命名空间
    // 日志主目录
    'ROOT_LOG' => 'Logs/',
    // 日志目录
    'LOG_ACCESS' => 'Access/', // 服务请求链接日志
    'LOG_CONNECT' => 'Connect/', // 数据库连接日志
    'LOG_EXCEPTION' => 'Error/', // 系统异常信息日志
    'LOG_INITIALIZE' => 'Initialize/', // 框架初始化日志
    'LOG_OPERATE' => 'Action/', // 系统操作日志
    // 引导信息
    'DEFAULT_CONTROLLER' => 'index', // 默认访问方法名
    'DEFAULT_METHOD' => 'index', // 默认访问文件名
    'DEFAULT_VIEW' => 'index', // 默认访问模板名
    'CLASS_SUFFIX' => '.class.php', // 类默认扩展名
    'METHOD_SUFFIX' => '.func.php', // 方法默认扩展名，内建应用
    'CONFIG_SUFFIX' => '.cfg.php', // 配置默认扩展名
    'MODEL_SUFFIX' => '.model.php', //数据模型扩展名
    'VIEW_SUFFIX' => '.html', //显示模板扩展名
    'IMPL_SUFFIX' => '.impl.php', //接口类型扩展名
    'LOG_SUFFIX' => '.log', //日志类型扩展名
    // 会话session设置, 当前版本只对会话进行基础支持，所以部分设置暂时不使用
    'SESSION' => array(
        'SAVE_PATH'=> '', // session存储位置，一般php.ini设置,如果需要修改存储位置,再填写
        'NAME'=> 'PHPSESSID', // 指定会话名以用做 cookie 的名字.只能由字母数字组成，默认为 PHPSESSID
        'SAVE_HANDLER' => 'files', // 定义了来存储和获取与会话关联的数据的处理器的名字.默认为 files
        'AUTO_START' => 0, // 指定会话模块是否在请求开始时自动启动一个会话.默认为 0（不启动）
        'GC_PROBABILITY' => 1, // 与 gc_divisor 合起来用来管理 gc（garbage collection 垃圾回收）进程启动的概率.默认为 1
        'GC_DIVISOR' => 100, // 与 gc_probability 合起来定义了在每个会话初始化时启动 gc（garbage collection 垃圾回收）进程的概率.默认为 100
        'GC_MAXLIFTTIME' => 1440, // 指定过了多少秒之后数据就会被视为“垃圾”并被清除,垃圾搜集可能会在 session 启动的时候开始
        'SERIALIZE_HANDLER' => 'php', // 定义用来序列化／解序列化的处理器名字
        'USE_STRICT_MODE' => 0, //
        'REFERER_CHECK'=> '', // 用来检查每个 HTTP Referer 的子串,如果客户端发送了 Referer 信息但是在其中并未找到该子串,则嵌入的会话 ID 会被标记为无效,默认为空字符串
        'ENTROPY_FILE' => '', // 会给出一个到外部资源（文件）的路径,在会话 ID 创建进程中被用作附加的熵值资源
        'ENTROPY_LENGTH' => 0, // 指定了从上面的文件中读取的字节数,默认为 0
        'CACHE_LIMITER' => 'nocache', // 指定会话页面所使用的缓冲控制方法.默认为 nocache
        'CACHE_EXPIRE' => 180, // 以分钟数指定缓冲的会话页面的存活期,此设定对 nocache 缓冲控制方法无效,默认为 180
        'USE_TRANS_SID' => 1, // 指定是否启用透明 SID 支持.默认为 0（禁用）
        'HASH_FUNCTION' => 0, // 允许用户指定生成会话 ID 的散列算法.'0' 表示 MD5（128 位）,'1' 表示 SHA-1（160 位）
        'HASH_BITS_PER_CHARACTER' => 4, // 允许用户定义将二进制散列数据转换为可读的格式时每个字符存放多少个比特,可能值为 '4'（0-9，a-f）默认,'5'（0-9，a-v）,以及 '6'（0-9，a-z，A-Z，"-"，","）
    ),
    // 会话cookie设置, 根据系统安全规范，cookie不推荐使用
    'COOKIE' => array(
        'COOKIE_LIFETIME' => 0, // 以秒数指定了发送到浏览器的 cookie 的生命周期,值为 0 表示“直到关闭浏览器”,默认为 0
        'COOKIE_PATH' => '/', // 指定了要设定会话 cookie 的路径
        'COOKIE_DOMAIN' => '', // 指定了要设定会话 cookie 的域名,默认为无
        'COOKIE_SECURE' => '', // 指定是否仅通过安全连接发送 cookie,默认为 off
        'COOKIE_HTTPONLY' => '', // 标记cookie，只有通过HTTP协议访问，这意味着饼干不会访问的脚本语言,比如JavaScript，这个设置可以有效地帮助降低身份盗窃XSS攻击,仅部分浏览器支持
        'USE_COOKIES' => 1, // 指定是否在客户端用 cookie 来存放会话 ID,默认为 1
        'USE_ONLY_COOKIES' => 1, // 指定是否在客户端仅仅使用 cookie 来存放会话 ID,启用此设定可以防止有关通过 URL 传递会话 ID 的攻击,默认值改为1
    ),
    // 数据库执行设置
    'DEFAULT_ENGINE_TYPE' => 'innodb', // 数据驱动类型
    'DATA_USE_TRANSACTION' => true, // 数据驱动类型为innodb时，事务操作设置才会生效
    'DATA_CONNECT_MAX' => 0, // 数据服务最大访问数量,设置该参数后,连接将会被监听,当到达最大连接值时,系统将挂起连接服务,直到有空余连接位置，默认值0（不作限制）
    // 如果服务器启用缓冲器，则该限制只针对系统数据操作用户
    'DATA_CONNECT_THREAD' => 0, // 连接是否使用线程,当前版本暂不支持线程
    'DATA_USE_FACTORY' => 0, // 是否是用数据工厂模式,当前版本暂不支持线程
    //SQL设置信息
    'DATA_TYPE' => 'mysql', //选择数据库类型,当前版本只支持mysql
    'DATA_HOST' => 'localhost', // mysql服务访问地址
    'DATA_USER' => 'root', // mysql登录用户
    'DATA_PWD' => '', // mysql登录密码
    'DATA_PORT' => '3306', // mysql默认访问端口
    'DATA_DB' => 'test', // mysql访问数据库
    'DATA_USE_MEMCACHE' => 0, // mysql是否使用memcache进行数据缓冲,默认值是0（不启用）,启用memcache需要在部署服务器上搭建memcache环境，
    // 如果需要多地缓冲还需搭建多个缓冲服务器，否则该功能无法生效
    'MEMCACHE_SET_ADDRESS' => array(
        'ADDRESS'=> '',
        'CAPACITY'=> 0,
    ),
    // 数据库服务器配置(多地址结构)
    'DATA_MATRIX_CONFIG' => array(
        array(
            "DATA_NAME" =>"", // 当前数据源名称
            'DATA_HOST' => 'localhost', // mysql服务访问地址
            'DATA_USER' => 'root', // mysql登录用户
            'DATA_PWD' => '', // mysql登录密码
            'DATA_PORT' => '3306', // mysql默认访问端口
            'DATA_DB' => 'test', // mysql访问数据库
            'DATA_USE_MEMCACHE' => 0, // mysql是否使用memcache进行数据缓冲,默认值是0（不启用）,启用memcache需要在部署服务器上搭建memcache环境，
            // 如果需要多地缓冲还需搭建多个缓冲服务器，否则该功能无法生效
            'MEMCACHE_SET_ADDRESS' => array(
                'ADDRESS'=> '',
                'CAPACITY'=> 0
            ),
        ),
    ),
    // 数据缓冲及数据显示模式设置
    'LABEL_TYPE' => 0, // 系统支持标签格式（0:默认格式(Origin格式),1:html格式,2:自然语句格式)
    'BUFFER_TYPE' => 0, //使用缓存器类型（0:不适用缓存器,1:使用数据缓存,2:生成php缓存文件,3:生成静态文件）
    'BUFFER_TIME' => 0, //缓存器生命周期（0:不设限制,内容发生变化,缓存器执行更新,大于0时以秒为时间单位进行周期更新）
    // 路由类型用于决定程序使用地址解析结构
    'ROUTE_CATALOGUE' => 'Route/', // 路由主目录
    'ROUTE_FILES' => 'Route.php', // 路由文件
    // web地址控制及地址监听模式设置
    'URL_TYPE' => 'http/https', // 设置web访问的超文本传输协议模式(0:http/https,1:http,2:https),可以使用数字设置也可以直接使用描述设置
    'URL_LISTEN' => 0, // 是否启用地址监听(0:false,1:true) 默认 0：不监听
    'URL_HOST' => 'localhost', // web默认地址(默认可以使用localhost或127.0.0.1)不添加传输协议头
    'URL_HOST_ONLY' => 0, //是否固定web域名信息(0:false,1:true) 默认 0：不固定域名信息

);