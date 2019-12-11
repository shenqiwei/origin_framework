<span id='application'></span>
## Application 应用主控制器目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin">返回</a>]
在该目录中，有两个控制文件:Controller.php和Initialization.php
- Controller.php: 应用主控制器文件  
主控制器文件主要负责框架12个基本功能和功能包的预加载封装  
- Initialization.php 框架初始化系统组件模块文件
该模块主要使用来为框架提供应用文件结构初始化搭建支持的封装模块（由于生成结构的效率问题，暂时停用，并清空程序代码）


#### Controller.php调用
>主控制器的调用方式与其他PHP文件的调用方式一致，省略include与require引用操作，直接使用命名空间调用，使用继承方式来实现应用控制器对Origin核心功能的调用： 
>>`use Origin\Controller;`

>继承controller主控器方法与父类继承语法一致，假定现在继承的应用控制器文件为index.class.php,即控制器名称Index（class name）
>>`class Index extends Controller`  
>>`{`  
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`省略构造函数等功能函数...`   
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`function index(){` # 每个新建控制，需创建一个index函数方法，框架会在访问该控制器时，默认方法该方法    
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$this->view();` # 模板调用方法，模板在不进行标注时，默认访问与该方法同名模板      
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`}`   
>>`}`   

#### Controller.php函数说明

#### 函数快速路口
[`param()`](#func_3) | [`view()`](#func_4) | [`get_class()`](#func_5) | [`get_function()`](#func_6) | [`success()`](#func_7) | [`error()`](#func_8) | [`json()`](#func_10) | [`xml()`](#func_11) | [`html()`](#func_12) 

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
>>`$this->view("html_name");` 方法调用时参数可以为空，方法参数是用于指定需调用视图模板页（html页），当前版本紧支持同应用目录下的所有模板文件，跨目录调用暂不支持。默认访问模视图模板页是根据`get_class()`和`get_function()`函数方法获取访问对象信息，创建模板时需在同主应用目录下的view文件中创建同控制器名文件夹，再在其中创建同方法名模板文件  

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
> xml格式转化并执行输出函数 (该方法已取消)  
>>`$this->xml($message_array);` 该函数会对填入参数数组，进行转化，并进行制定格式内容的输出    
>>> `header("Content-Type:text/xml;charset=utf-8");`XML输出的文件格式  

<span id='func_12'></span>
__html()__：
> html格式转化并执行输出函数 (该方法已取消)
>>`$this->html(html_head,html_body);` 该函数会对填入参数为html页面的head结构代码和html页面的body结构代码，代码不会被框架进行html内容转化  


`Controller部分功能调用样例：`  
![Controller部分功能样例](https://github.com/shenqiwei/Origin-Framework/blob/master/Screenshot/i_controller.png)

