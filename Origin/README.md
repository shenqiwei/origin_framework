<span id='origin_top'></span>
## Origin 框架内核目录
在这里存放着Origin所有功能的基础封装文件，所有功能的调用和基本功能实现，都在这里进行
#### 快速入口
[`Application说明`](#application)|[`Config说明`](#config)|[`Font说明`](#origin_font)|[`Kernel说明`](#origin_kernel)|[`Method说明`](#origin_method)|[`Template说明`](#origin_template)

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

<span id='application'></span>
## Application 应用主控制器目录 [[返回TOP](#origin_top)]
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

#### 函数快速路口
[`welcome()`](#func_1) | [`show()`](#func_2) | [`param()`](#func_3) | [`view()`](#func_4) | [`get_class()`](#func_5) | [`get_function()`](#func_6) | [`success()`](#func_7) | [`error()`](#func_8) | [`json()`](#func_10) | [`xml()`](#func_11) | [`html()`](#func_12) 

<span id='func_1'></span>
__welcome()__：
> Origin欢迎函数（鸡肋函数，其内容为Origin欢迎页，欢迎语大部分使用机翻 :p）  
>>`$this->welcome();` 方法调用后会显示Origin欢迎页，为了简单的演示出Origin视图模板以及数据交互内容，所以在实际的index中并未使用该方法  

<span id='func_2'></span>
__show()__：
> Origin信息打印函数单纯的echo输出语法，但内容输出后程序会强制停止，仅用于调试程序内容时使用  
>>`$this->show();` 方法只能显示字符串类型的信息，并且不对html结构进行转化，输出结果后，会终止系统运行，后期将会对show结构进行功能升级  
>>>`protected function show($message)`  
>>>`{`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo($message);`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`exit();`  
>>>`}`

<span id='func_3'></span>
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

<span id='func_4'></span>
__view()__：
> 视图模板函数，用于调用控制器对应函数视图模板，也可以根据实际需要设定其他模板内容  
>>`$this->view("html_name");` 方法调用时参数可以为空，方法参数是用于指定需调用视图模板页（html页），当前版本紧支持同应用目录下的所有模板文件，跨目录调用暂不支持。默认访问模视图模板页是根据`get_class()`和`get_function()`函数方法获取访问对象信息  

<span id='func_5'></span>
__get_class()__：
> 获取应用控制器名称函数  
>> `$this->get_class();` 该函数方法可以直接获取当前访问对象的控制器名称  

<span id='func_6'></span>
 __get_function()__：
>获取应用函数名称函数  
>> `$this->get_function();` 该函数方法可以直接获取当前访问对象的函数名臣  

<span id='func_7'></span>
__success()__：
> 操作成功信息返回函数  
>> `$this->success(message_info,skip_url,waiting_time);` 信息提醒函数，包含三个基本参数，message_info：信息内容，skip_url：跳转地址（默认值#，当填写空内容时，函数将不进行跳转操作，填写#则回退到前一个页面），waiting_time：等待周期时间（默认时间：5秒）   

<span id='func_8'></span>
__error()__：
> 操作异常信息返回函数  
>> `$this->error(message_info,skip_url,waiting_time);` 其函数方法应用方式一致，请参数success函数说明。 

<span id='func_10'></span>
__json()__：
> json格式转化并执行输出函数  
>>`$this->json($message_array);` 该函数会对填入参数数组，进行转化，并进行制定格式内容的输出  
>>> `header("Content-Type:application/json;charset=utf-8");`Json输出的文件格式

<span id='func_11'></span>
__xml()__：
> xml格式转化并执行输出函数  
>>`$this->xml($message_array);` 该函数会对填入参数数组，进行转化，并进行制定格式内容的输出    
>>> `header("Content-Type:text/xml;charset=utf-8");`XML输出的文件格式  

<span id='func_12'></span>
__html()__：
> html格式转化并执行输出函数
>>`$this->html(html_head,html_body);` 该函数会对填入参数为html页面的head结构代码和html页面的body结构代码，代码不会被框架进行html内容转化  


`Controller部分功能调用样例：`  
![Controller部分功能样例](https://github.com/shenqiwei/Origin-Framework/blob/master/Screenshot/i_controller.png)


<span id='config'></span>
## Config 框架主配置目录 [[返回TOP](#origin_top)]
该目录中主要存储的是Origin框架各个功能配置设定内容项（Config.cfg.php）
通过该文件内容项的修改来为开发提供有利支持。配置选项，使用全字母大写，完整单词描述方式进行展现
`在不进行任何设置的情况下，框架可以进行基础的开发操作，满足网站及一般应用需求的情况，只需要在应用配置目录下的Config.cfg.php文件中，编写自己数据库配置内容既可以进行开发和功能实现编写`

#### Config 快速入口
[`目录配置`](#config_dir) | [`文件访问配置`](#config_file) | [`会话配置`](#config_session) | [`数据源配置`](#config_db) | [`访问显示模式配置`](#config_view) | [`路由应用配置`](#config_route) | [`web辅助配置`](#config_web)

#### Config配置项说明
<span id='config_dir'></span>
__目录配置：[[返回](#config)]__  
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

<span id='config_file'></span>
__文件访问配置：[[返回](#config)]__
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
`DATA_TYPE` 选择数据库类型,当前版本只支持mysql （现阶段版本更新中，对redis和mongodb进行了支持，功能测试中，完成测试后会将支持结构上传）  
`DATA_HOST`  mysql服务访问地址  
`DATA_USER`  mysql登录用户  
`DATA_PWD` mysql登录密码  
`DATA_PORT`  mysql默认访问端口  
`DATA_DB` mysql访问数据库  
`DATA_USE_MEMCACHE` mysql是否使用memcache进行数据缓冲,默认值是0（不启用）,启用memcache需要在部署服务器上搭建memcache环境(暂时取消该功能支持)  
`MEMCACHE_SET_ADDRESSL:ADDRESS`  
`MEMCACHE_SET_ADDRESSL:CAPACITY`  
`DATA_MATRIX_CONFIG:DATA_NAME` 当前数据源名称(多数据结构支持，亦支持分布式数据结构)  
`DATA_MATRIX_CONFIG:DATA_HOST` mysql服务访问地址  
`DATA_MATRIX_CONFIG:DATA_USER` mysql登录用户  
`DATA_MATRIX_CONFIG:DATA_PWD` mysql登录密码  
`DATA_MATRIX_CONFIG:DATA_PORT` mysql默认访问端口  
`DATA_MATRIX_CONFIG:DATA_DB` mysql访问数据库  

<span id='config_view'></span>
__访问显示模式配置：(`功能完善中`)[[返回](#config)]__  
>`LABEL_TYPE` 系统支持标签格式（0:默认格式(Origin格式),1:html格式,2:自然语句格式)  
`BUFFER_TYPE` 使用缓存器类型（0:不适用缓存器,1:使用数据缓存,2:生成php缓存文件,3:生成静态文件）  
`BUFFER_TIME` 缓存器生命周期（0:不设限制,内容发生变化,缓存器执行更新,大于0时以秒为时间单位进行周期更新）  

<span id='config_route'></span>
__路由应用配置：(`功能测试修改中`)[[返回](#config)]__  
>`ROUTE_CATALOGUE` 路由主目录  
`ROUTE_FILES` 路由文件

<span id='config_web'></span>
__web辅助配置：[[返回](#config)]__  
>`URL_TYPE`  设置web访问的超文本传输协议模式(0:http/https,1:http,2:https),可以使用数字设置也可以直接使用描述设置  
`URL_LISTEN`  是否启用地址监听(0:false,1:true) 默认 0：不监听  
`URL_HOST` web默认地址(默认可以使用localhost或127.0.0.1)不添加传输协议头  
`URL_HOST_ONLY` 是否固定web域名信息(0:false,1:true) 默认 0：不固定域名信息  

<span id='origin_font'></span>
## Font 字体目录 [[返回TOP](#origin_top)]
这里主要是放置框架中使用的字体，字体主要用于Origin内部功能封装的字体设置支持

<span id='origin_kernel'></span>
## Kernel 内核目录 [[返回TOP](#origin_top)]

<span id='origin_method'></span>
## Method 功能函数目录 [[返回TOP](#origin_top)]

该目录中存放Origin预设功能函数以及集合型功能函数，所以函数的调用方法都存放在Function.func.php文件中
>函数列表

>> `Common.func.php`  
>>> 公共函数包，主要包含：字符转移，格式列表，文件分类用于对被访问文件内容进行限定和提供有效条件的内容函数  

>> `Config.func.php`  
>>> 配置函数包，提供分段函数，对不同结构层的配置信息进行调用管理，并针对配置内容进行简单的优先级排列执行

>> `Cookie.func.php`  
>>> 浏览器会话函数包，只用于对PHP Cookie会话的常规使用进行支持，和简单设置

>> `Entrance.func.php`  
>>> 框架入口函数包，Origin ver1.0的框架请求入口

>> `File.func.php`  
>>> 文件管理函数包，包含对文件及文件夹的添加、删除、修改等操作函数

>> `Filter.func.php`  
>>> 框架内容强制转化函数（该模块已经被validate.class.php的封装内容覆盖，更新Kernel时，会逐步取消该函数包的作用）

>> `Hook.func.php`  
>>> 文件引入函数包，由于功能重定义后，将钩子结构放入到入口函数中，所以Hook钩子模块的实际功能被取消，相应的插件引入规则，将重新定义

>> `Log.func.php`  
>>> 日志函数，包含4个基本日志类型，并提供函数自定义内容入口，方便开发者，定义同类型日志内容，并进行有效记录

>> `Public.func.php`  
>>> 公共应用函数，使用单字母命名方式的执行函数，该函数包中函数，可以帮助开发者，完成大多数开发需求

>> `Request.func.php`  
>>> 请求器函数包，用于对访问请求的GET及POST，UPLOAD行为进行监听捕获，并返回对象内容

>> `Session.func.php`  
>>> 会话操作函数包，只用于对PHP Session会话的常规使用进行支持，和简单设置

>> `Validate.func.php`  
>>> 内容验证函数包，包含12基础类型验证函数以及一个公共定义验证函数，分别用于对输入值得常用类型进行单一验证

<span id='origin_template'></span>
## Template 公共应用视图模板（html页） [[返回TOP](#origin_top)]

该目录中存放Origin内容提示所使用视图模板页，模板名称不可以修改，页面内容可以自定义