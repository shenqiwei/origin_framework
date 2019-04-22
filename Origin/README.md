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
`ROOT_APPLICATION`
`DEFAULT_APPLICATION`
`APPLICATION_BUFFER`
`APPLICATION_METHOD`
`APPLICATION_FUNCTION`
`APPLICATION_CONFIG`
`APPLICATION_CONTROLLER`
`APPLICATION_MODEL`
`APPLICATION_VIEW`
`ROOT_PLUGIN`
`ROOT_RESOURCE`
`ROOT_RESOURCE_JS`
`ROOT_RESOURCE_MEDIA`
`ROOT_RESOURCE_STYLE`
`ROOT_RESOURCE_TEMP`
`ROOT_RESOURCE_PLUGIN`
`ROOT_RESOURCE_PUBLIC`
`ROOT_RESOURCE_UPLOAD`
`ROOT_NAMESPACE`
`ROOT_LOG`
`LOG_ACCESS`
`LOG_CONNECT`
`LOG_EXCEPTION`
`LOG_INITIALIZE`
`LOG_OPERATE`
`DEFAULT_CONTROLLER`
`DEFAULT_METHOD`
`DEFAULT_VIEW`
`CLASS_SUFFIX`
`METHOD_SUFFIX`
`CONFIG_SUFFIX`
`MODEL_SUFFIX`
`VIEW_SUFFIX`
`IMPL_SUFFIX`
`LOG_SUFFIX`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`SESSION:`
`COOKIE:`
`COOKIE:`
`COOKIE:`
`COOKIE:`
`COOKIE:`
`COOKIE:`
`COOKIE:`
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