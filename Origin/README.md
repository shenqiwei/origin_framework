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
在该目录中，有两个控制文件:Controller.php和Initialization.php(初始化模块改为使用函数直接实现，其功能主体也作出相应弱化)
- Controller.php: 应用主控制器文件  
主控制器文件主要负责框架12个基本功能和功能包的预加载封装  
- Initialization.php 框架初始化系统组件模块文件
该模块主要使用来为框架提供应用文件结构初始化搭建支持的封装模块（由于生成结构的效率问题，暂时停用，并清空程序代码）   
<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin/Application"><--查看详细内容--></a>   

<span id='config'></span>
## Config 框架主配置目录 [[返回TOP](#origin_top)]
该目录中主要存储的是Origin框架各个功能配置设定内容项（Config.php）
通过该文件内容项的修改来为开发提供有利支持。配置选项，使用全字母大写，完整单词描述方式进行展现   
`在不进行任何设置的情况下，框架可以进行基础的开发操作，满足网站及一般应用需求的情况，只需要在应用配置目录下的Config.cfg.php文件中，编写自己数据库配置内容既可以进行开发和功能实现编写`   
<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin/Config"><--查看详细内容--></a>   

<span id='origin_font'></span>
## Font 字体目录 [[返回TOP](#origin_top)]
这里主要是放置框架中使用的字体，字体主要用于Origin内部功能封装的字体设置支持    

<span id='origin_kernel'></span>
## Kernel 内核目录 [[返回TOP](#origin_top)]
这里主要是放置origin框架主体功能函数封装，其中包含了数据库访问，模板解码，变量请求监听，http的远程访问   
<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin/Kernel"><--查看详细内容--></a>   

<span id='origin_method'></span>
## Method 功能函数目录 [[返回TOP](#origin_top)]
该目录中存放Origin预设功能函数以及集合型功能函数，所以函数的调用方法都存放在Function.php文件中   
<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin/Method"><--查看详细内容--></a>   

<span id='origin_template'></span>
## Template 公共应用视图模板（html页） [[返回TOP](#origin_top)]

该目录中存放Origin内容提示所使用视图模板页，模板名称不可以修改，页面内容可以自定义