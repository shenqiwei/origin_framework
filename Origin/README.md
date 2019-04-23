## Origin 框架内核目录
在这里存放着Origin所有功能的基础封装文件，所有功能的调用和基本功能实现，都在这里进行
## 入口文件
Origin入口文件功能设计十分简单，起主要功能是用来对框架的应用开发提供基础设置支持：
1) 在入口文件中，Origin限定了PHP开发支持的最低语言版本（大等于5.5）
2) 预设了BUG，ERROR，html代码去结构功能常量
3) 预设错误提示结构常量
- 错误信息显示
- E_ALL = 11 所有的错误信息
- E_ERROR = 1 报致命错误
- E_WARNING = 2 报警告错误
- E_NOTICE = 8 报通知警告
- E_ALL& ~E_NOTICE = 3 不报NOTICE错误, 常量参数 TRUE

入口文件常量使用则是在index.php应用入口文件中进行设置了，在框架默认状态下，常量保持初始状态

## Application 应用主控制器目录
在该目录中，有两个控制文件:Controller.class.php和Initialization.class.php
- Controller.class.php: 应用主控制器文件  
主控制器文件主要负责框架12个基本功能和功能包的预加载封装  
- Initialization.class.php 框架初始化系统组件模块文件
该模块主要使用来为框架提供应用文件结构初始化搭建支持的封装模块（由于生成结构的效率问题，暂时停用，并清空程序代码）


#### Controller.class.php调用
>主控制器的调用方式与其他PHP文件的调用方式一致，省略include与require引用操作，直接使用命名空间调用，使用继承方式来实现应用控制器对Origin核心功能的调用： 
>>use Origin\Controller; 

>继承controller主控器方法与父类继承语法一致，假定现在继承的应用控制器文件为index.class.php,即控制器名称Index（class name）
>>`class Index extends Controller`  
>>`{`  
>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`省略构造函数等功能函数...`  
>>`}`

#### Controller.class.php函数说明
__welcome()__：
> Origin欢迎函数（鸡肋函数，其内容为Origin欢迎页，欢迎语大部分使用机翻 :p）  
>>`$this->welcome();` 方法调用后会显示Origin欢迎页，为了简单的演示出Origin视图模板以及数据交互内容，所以在实际的index中并未使用该方法  

__show()__：
> Origin信息打印函数单纯的echo输出语法，但内容输出后程序会强制停止，仅用于调试程序内容时使用  
>>`$this->show();` 方法只能显示字符串类型的信息，并且不对html结构进行转化，输出结果后，会终止系统运行，后期将会对show结构进行功能升级  
>>>`protected function show($message)`  
>>>`{`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo($message);`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`exit();`  
>>>`}`

__param()__：
> 参数预设函数，用于前后端数据内容传递的中间函数变量，可以存储除object（对象）外的任意类型变量内容  
>>`$this->param(variable_key,variable_value);` 方法调用时需要填写非空（null and ‘’）字符串作为参数的键值（variable_key）,相对于参数值（variable_value）得要求比较宽松，填入非对象变量值   
>>>例：使用默认访问函数进行param方法调用   
>>>`function index()`  
>>>`{`  
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$this->param('welcome', '欢迎使用Origin框架'); # 参数设置函数`  
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$this->view(); # 视图模板调用函数`  
>>> `}`  
>>> 在视图模板页（html页面）中设置对应变量 `{$welcome}`来获取函数中参数内容，在页面中显示内容为 `欢迎使用Origin框架`

__view()__：
> 视图模板函数，用于调用控制器对应函数视图模板，也可以根据实际需要设定其他模板内容  
>>`$this->view("html_name");` 方法调用时参数可以为空，方法参数是用于指定需调用视图模板页（html页），当前版本紧支持同应用目录下的所有模板文件，跨目录调用暂不支持。默认访问模视图模板页是根据`get_class()`和`get_function()`函数方法获取访问对象信息  

__get_class()__：
> 获取应用控制器名称函数  
>> `$this->get_class();` 该函数方法可以直接获取当前访问对象的控制器名称  

 __get_function()__：
>获取应用函数名称函数  
>> `$this->get_function();` 该函数方法可以直接获取当前访问对象的函数名臣  

__success()__：
> 操作成功信息返回函数  
>> `$this->success(message_info,skip_url,waiting_time);` 信息提醒函数，包含三个基本参数，message_info：信息内容，skip_url：跳转地址（默认值#，当填写空内容时，函数将不进行跳转操作，填写#则回退到前一个页面），waiting_time：等待周期时间（默认时间：5秒）   

__error()__：
> 操作异常信息返回函数  
>> `$this->error(message_info,skip_url,waiting_time);` 其函数方法应用方式一致，请参数success函数说明。

__failed()__：
> 操作失败信息返回函数（由于大部分时间error与failed功能相同，故推荐使用error函数，后期将对failed函数进行重新定义）  

__json()__：
> json格式转化并执行输出函数  
>>`$this->json($message_array);` 该函数会对填入参数数组，进行转化，并进行制定格式内容的输出  
>>> `header("Content-Type:application/json;charset=utf-8");`Json输出的文件格式

__xml()__：
> xml格式转化并执行输出函数  
>>`$this->xml($message_array);` 该函数会对填入参数数组，进行转化，并进行制定格式内容的输出    
>>> `header("Content-Type:text/xml;charset=utf-8");`XML输出的文件格式  

__html()__：
> html格式转化并执行输出函数
>>`$this->html(html_head,html_body);` 该函数会对填入参数为html页面的head结构代码和html页面的body结构代码，代码不会被框架进行html内容转化  

## Config 框架主配置目录
该目录中主要存储的是Origin框架各个功能配置设定内容项（Config.cfg.php）
通过该文件内容项的修改来为开发提供有利支持。配置选项，使用全字母大写，完整单词描述方式进行展现

#### Config配置项说明
__目录配置：__  
>`ROOT_APPLICATION` 应用控制器目录  默认值：`Apply/`   
`DEFAULT_APPLICATION` 默认访问文件根目录 默认值：`Home/`
`APPLICATION_BUFFER` 缓存文件目录（可以在配置文件中设置是否使用缓存）默认值：`Buffer/`  
`APPLICATION_METHOD` 公共方法文件目录，系统公共方法公共调用文件存储位置，内建应用 默认值：`Common/`  
`APPLICATION_FUNCTION` 公共方法文件目录，系统公共方法公共调用文件存储位置 默认值：`Common/`  
`APPLICATION_CONFIG` 开发者或用户自定义或改写系统配置文件存储位置 默认值：`Config/`  
`APPLICATION_CONTROLLER` 执行程序文件目录 默认值：`Controller/`  
`APPLICATION_MODEL` 数据操作语句文件目录 默认值：`Model/`  
`APPLICATION_VIEW` 模板（Template）文件目录 默认值：`View/`  
`ROOT_PLUGIN` 应用插件目录 默认值：`PlugIn/`  
`ROOT_RESOURCE` 资源主目录 默认值：`Resource/`  
`ROOT_RESOURCE_JS` javascript资源目录 默认值：`Jscript`  
`ROOT_RESOURCE_MEDIA` 多媒体资源目录  默认值：`Media`  
`ROOT_RESOURCE_STYLE` 样式表资源目录 默认值：`Style`  
`ROOT_RESOURCE_TEMP` 模板资源目录 默认值：`Template`  
`ROOT_RESOURCE_PLUGIN` 第三方插件 默认值：`Plug-In`  
`ROOT_RESOURCE_PUBLIC` 公共文件目录 默认值：`Public`  
`ROOT_RESOURCE_UPLOAD` 上传目录 默认值：`Upload`  
`ROOT_NAMESPACE` 根命名空间 默认值：`\\Apply`  
`ROOT_LOG` 日志主目录 默认值：`Logs/`  
`LOG_ACCESS` 服务请求链接日志 默认值：`Access/`  
`LOG_CONNECT` 数据库连接日志 默认值：`Connect/`   
`LOG_EXCEPTION` 系统异常信息日志 默认值：`Error/`  
`LOG_INITIALIZE` 框架初始化日志 默认值：`Initialize/`  
`LOG_OPERATE` 系统操作日志 默认值：`Action/`  

__文件访问配置：__
>`DEFAULT_CONTROLLER` 默认访问控制器对象 默认值：`index`  
`DEFAULT_METHOD` 默认访问函数方法 默认值：`index`  
`DEFAULT_VIEW`  默认访问视图模板 默认值：`index`  
`CLASS_SUFFIX` 类默认扩展名 默认值：`.class.php`  
`METHOD_SUFFIX` 方法默认扩展名，内建应用 默认值：`.func.php`  
`CONFIG_SUFFIX` 配置默认扩展名 默认值：`.cfg.php`  
`MODEL_SUFFIX` 数据模型扩展名 默认值：`.model.php`  
`VIEW_SUFFIX` 显示模板扩展名 默认值：`.html`  
`IMPL_SUFFIX` 接口类型扩展名 默认值：`.impl.php`  
`LOG_SUFFIX` 日志类型扩展名 默认值：`.log`  

__会话配置：__   
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

__数据源配置：__  
`DEFAULT_ENGINE_TYPE`
`DATA_USE_TRANSACTION`
`DATA_CONNECT_MAX`
`DATA_CONNECT_THREAD`
`DATA_USE_FACTORY`
`DATA_TYPE`
`DATA_HOST`
`DATA_USER`
`DATA_PWD`
`DATA_PORT`
`DATA_DB`
`DATA_USE_MEMCACHE`
`MEMCACHE_SET_ADDRESSL:ADDRESS`
`MEMCACHE_SET_ADDRESSL:CAPACITY`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:`
`DATA_MATRIX_CONFIG:MEMCACHE_SET_ADDRESSL:ADDRESS`
`DATA_MATRIX_CONFIG:MEMCACHE_SET_ADDRESSL:CAPACITY`
`LABEL_TYPE`
`BUFFER_TYPE`
`BUFFER_TIME`
`ROUTE_CATALOGUE`
`ROUTE_FILES`
`URL_TYPE`
`URL_LISTEN`
`URL_HOST`
`URL_HOST_ONLY`