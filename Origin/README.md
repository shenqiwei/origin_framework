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


#####Controller.class.php调用
>主控制器的调用方式与其他PHP文件的调用方式一致，省略include与require引用操作，直接使用命名空间调用，使用继承方式来实现应用控制器对Origin核心功能的调用： 
>>use Origin\Controller; 

>继承controller主控器方法与父类继承语法一致，假定现在继承的应用控制器文件为index.class.php,即控制器名称Index（class name）
>>`class Index extends Controller`  
>>`{`  
>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`省略构造函数等功能函数...`  
>>`}`

#####Controller.class.php函数说明
> __welcome()__：Origin欢迎函数（鸡肋函数，其内容为Origin欢迎页，欢迎语大部分使用机翻 :p）  
>>`$this->welcome();` 方法调用后会显示Origin欢迎页，为了简单的演示出Origin视图模板以及数据交互内容，所以在实际的index中并未使用该方法  

> __show()__：Origin信息打印函数单纯的echo输出语法，但内容输出后程序会强制停止，仅用于调试程序内容时使用  
>>`$this->show()` 方法只能显示字符串类型的信息，并且不对html结构进行转化，输出结果后，会终止系统运行，后期将会对show结构进行功能升级  
>>>`protected function show($message)`  
>>>`{`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo($message);`  
>>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`exit();`  
>>>`}`

> __param()__：参数预设函数，用于前后端数据内容传递的中间函数变量，可以存储除object（对象）外的任意类型变量内容  
> __view()__：视图模板函数，用于调用控制器对应函数视图模板，也可以根据实际需要设定其他模板内容  
> __get_class()__：获取应用控制器名称函数  
> __get_function()__：获取应用函数名称函数  
> __success()__：操作成功信息返回函数  
> __error()__：操作异常信息返回函数  
> __failed()__：操作失败信息返回函数（由于大部分时间error与failed功能相同，故推荐使用error函数，后期将对failed函数进行重新定义）  
> __json()__：json格式转化并执行输出函数  
> __xml()__：xml格式转化并执行输出函数  
> __html()__：html格式转化并执行输出函数



