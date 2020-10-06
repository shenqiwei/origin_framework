## Config 框架主配置目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/origin">返回</a>]
该目录中主要存储的是Origin框架各个功能配置设定内容项（Config.cfg.php）
通过该文件内容项的修改来为开发提供有利支持。配置选项，使用全字母大写，完整单词描述方式进行展现
`在不进行任何设置的情况下，框架可以进行基础的开发操作，满足网站及一般应用需求的情况，只需要在应用配置目录下的Config.cfg.php文件中，编写自己数据库配置内容既可以进行开发和功能实现编写

#### Config 快速入口
[`目录配置`](#config_dir) | [`文件访问配置`](#config_file) | [`会话配置`](#config_session) | [`数据源配置`](#config_db) | [`访问显示模式配置`](#config_view) | [`路由应用配置`](#config_route) 

#### Config配置项说明
<span id='config_dir'></span>
__目录配置：[[返回](#config)]__  
>`DEFAULT_APPLICATION` 默认访问文件根目录 默认值：`Home`(1.0后配置改为使用常量设置)       
`APPLICATION_METHOD` 公共方法文件目录，系统公共方法公共调用文件存储位置，内建应用 默认值：`Common`(1.0后配置不再影响框架内容)    
`APPLICATION_FUNCTION` 公共方法文件目录，系统公共方法公共调用文件存储位置 默认值：`Common`(1.0后配置不再影响框架内容)    
`APPLICATION_CONFIG` 开发者或用户自定义或改写系统配置文件存储位置 默认值：`Config`(1.0后配置不再影响框架内容)    
`APPLICATION_CLASSES` 执行程序文件目录 默认值：`Classes`(1.0后配置不再影响框架内容)    
`APPLICATION_MODEL` 数据操作语句文件目录 默认值：`Model`(1.0后配置不再影响框架内容)  
`APPLICATION_VIEW` 模板（Template）文件目录 默认值：`Template`(1.0后配置不再影响框架内容)    
`ROOT_RESOURCE` 资源主目录 默认值：`Resource`(1.0后配置改为使用常量设置)   
`ROOT_RESOURCE_PUBLIC` 公共文件目录 默认值：`Public`(1.0后配置不再影响框架内容)  
`ROOT_RESOURCE_UPLOAD` 上传目录 默认值：`Upload`(1.0后配置不再影响框架内容)  
`ROOT_RESOURCE_BUFFER` 缓存目录 默认值：`Buffer`(1.0后配置不再影响框架内容)   

__缓存状态配置：[[返回](#config)]__  
>`ROOT_USE_BUFFER` 缓存状态设置 默认值：`true` true：缓存文件机制启动，false：缓存文件机制关闭，不再生成新的缓存文件内容    

__日志目录配置：[[返回](#config)]__  
>`ROOT_LOG` 日志主目录 默认值：`Logs/`(1.0后配置改为使用常量设置)  
`LOG_ACCESS` 服务请求链接日志 默认值：`Access/`(1.0后配置改为使用常量设置)  
`LOG_CONNECT` 数据库连接日志 默认值：`Connect/`(1.0后配置改为使用常量设置)   
`LOG_EXCEPTION` 系统异常信息日志 默认值：`Error/`(1.0后配置改为使用常量设置)  
`LOG_INITIALIZE` 框架初始化日志 默认值：`Initialize/`(1.0后配置改为使用常量设置)  
`LOG_OPERATE` 系统操作日志 默认值：`Action/`(1.0后配置改为使用常量设置,已取消)  

<span id='config_file'></span>
__文件访问配置：[[返回](#config)]__
>`DEFAULT_CLASS` 默认访问控制器对象 默认值：`index`(1.0后配置改为使用常量设置)  
`DEFAULT_METHOD` 默认访问函数方法 默认值：`index`(1.0后配置改为使用常量设置)  
`DEFAULT_VIEW`  默认访问视图模板 默认值：`index`(1.0后配置改为使用常量设置) 

<span id='config_session'></span>
__会话配置：[[返回](#config)]__   
>`SESSION:SAVE_PATH` session存储位置，一般php.ini设置,如果需要修改存储位置,再填写  
`SESSION:NAME` 指定会话名以用做 cookie 的名字.只能由字母数字组成，默认为 `PHPSESSID`  
`SESSION:SAVE_HANDLER` 定义了来存储和获取与会话关联的数据的处理器的名字.默认为 `files`  
`SESSION:AUTO_START` 指定会话模块是否在请求开始时自动启动一个会话.默认为 `0（不启动）`  
`SESSION:GC_PROBABILITY` 与 gc_divisor 合起来用来管理 gc（garbage collection 垃圾回收）进程启动的概率.默认为 `1`  
`SESSION:GC_DIVISOR` 与 gc_probability 合起来定义了在每个会话初始化时启动 gc（garbage collection 垃圾回收）进程的概率.默认为 `100`  
`SESSION:'GC_MAXLIFTTIME` 指定过了多少秒之后数据就会被视为“垃圾”并被清除,垃圾搜集可能会在 session 启动的时候开始  
`SESSION:SERIALIZE_HANDLER` 定义用来序列化／解序列化的处理器名字  
`SESSION:USE_STRICT_MODE`  
`SESSION:REFERER_CHECK` 用来检查每个 HTTP Referer 的子串,如果客户端发送了 Referer 信息但是在其中并未找到该子串,则嵌入的会话 ID 会被标记为无效,默认为`空字符串 ''`  
`SESSION:ENTROPY_FILE` 给出一个到外部资源（文件）的路径,在会话 ID 创建进程中被用作附加的熵值资源  
`SESSION:ENTROPY_LENGTH` 指定了从上面的文件中读取的字节数,默认为 `0`  
`SESSION:CACHE_LIMITER` 指定会话页面所使用的缓冲控制方法.默认为 `nocache`  
`SESSION:CACHE_EXPIRE` 以分钟数指定缓冲的会话页面的存活期,此设定对 nocache 缓冲控制方法无效,默认为 `180`  
`SESSION:USE_TRANS_SID` 指定是否启用透明 SID 支持.默认为 `0（禁用）`  
`SESSION:HASH_FUNCTION` 允许用户指定生成会话 ID 的散列算法.'0' 表示 MD5（128 位）,'1' 表示 SHA-1（160 位）  
`SESSION:HASH_BITS_PER_CHARACTER` 允许用户定义将二进制散列数据转换为可读的格式时每个字符存放多少个比特,可能值为 '4'（0-9，a-f）默认,'5'（0-9，a-v）,以及 '6'（0-9，a-z，A-Z，"-"，","）  
`COOKIE:COOKIE_LIFETIME` 以秒数指定了发送到浏览器的 cookie 的生命周期,值为 0 表示“直到关闭浏览器”,默认为 `0`  
`COOKIE:COOKIE_PATH` 指定了要设定会话 cookie 的路径,默认为`空字符串 ''`  
`COOKIE:COOKIE_DOMAIN` 指定了要设定会话 cookie 的域名,默认为`空字符串 ''`  
`COOKIE:COOKIE_SECURE` 指定是否仅通过安全连接发送 cookie,默认为 `off`  
`COOKIE:COOKIE_HTTPONLY`标记cookie，只有通过HTTP协议访问，这意味着饼干不会访问的脚本语言,比如JavaScript，这个设置可以有效地帮助降低身份盗窃XSS攻击,仅部分浏览器支持  
`COOKIE:USE_COOKIES` 指定是否在客户端用 cookie 来存放会话 ID,默认为 `1`  
`COOKIE:USE_ONLY_COOKIES` 指定是否在客户端仅仅使用 cookie 来存放会话 ID,启用此设定可以防止有关通过 URL 传递会话 ID 的攻击,默认值改为`1`  

<span id='config_db'></span>
__数据源配置：[[返回](#config)]__  
>`DEFAULT_ENGINE_TYPE` 数据驱动类型（innodb,由关系数据库配置限定，该配置只负责辅助框架完成事务管理操作）  
`DATA_USE_TRANSACTION` 数据驱动类型为innodb时，事务操作设置才会生效  
`DATA_CONNECT_MAX` 数据服务最大访问数量,设置该参数后,连接将会被监听,当到达最大连接值时,系统将挂起连接服务,直到有空余连接位置，默认值0（不作限制）  
`DATA_CONNECT_THREAD` 连接是否使用线程,当前版本暂不支持线程  
`DATA_USE_FACTORY` 是否是用数据工厂模式,当前版本暂不支持线程  
`DATA_TYPE` 选择数据库类型,当前版本支持Mysql,MariaDB,SQL server(mssql),PostgreSQL(pgsql),sqlite,Oracle,Redis,MongoDB(基础操作支持)   
`DATA_HOST`  服务访问地址  
`DATA_USER`  登录用户  
`DATA_PWD` 登录密码  
`DATA_PORT`  默认访问端口 mysql:3306,mariadb:3306,PostgreSQL:5432,Redis:6379,MongoDB:27017    
`DATA_DB` 访问数据库  
`DATA_USE_MEMCACHE` mysql是否使用memcache进行数据缓冲,默认值是0（不启用）,启用memcache需要在部署服务器上搭建memcache环境(暂时取消该功能支持)  
`MEMCACHE_SET_ADDRESSL:ADDRESS`  
`MEMCACHE_SET_ADDRESSL:CAPACITY`  
`DATA_MATRIX_CONFIG:DATA_NAME` 当前数据源名称(多数据结构支持，亦支持分布式数据结构)  
`DATA_MATRIX_CONFIG:DATA_HOST` 服务访问地址  
`DATA_MATRIX_CONFIG:DATA_USER` 登录用户  
`DATA_MATRIX_CONFIG:DATA_PWD` 登录密码  
`DATA_MATRIX_CONFIG:DATA_PORT` 默认访问端口  
`DATA_MATRIX_CONFIG:DATA_DB` 访问数据库  

<span id='config_view'></span>
__访问显示模式配置：(`1.0版本后改配置被取消`)[[返回](#config)]__  
`BUFFER_TYPE` 使用缓存器类型（0:不适用缓存器,1:使用数据缓存,2:生成php缓存文件,3:生成静态文件）  
`BUFFER_TIME` 缓存器生命周期（0:不设限制,内容发生变化,缓存器执行更新,大于0时以秒为时间单位进行周期更新）  

<span id='config_route'></span>
__路由应用配置：(`1.0版本后改配置被取消`)[[返回](#config)]__  
>`ROUTE_CATALOGUE` 路由主目录  
`ROUTE_FILES` 路由文件