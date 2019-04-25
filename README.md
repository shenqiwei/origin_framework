# Origin-Framework
Origin 架构主要是为解决入门级开发人员在PHP开发中的基础应用问题： 

![欢迎页](https://github.com/shenqiwei/Origin-Framework/blob/master/Screenshot/welcome.png)
## 基本说明：
1) Origin使用PHP7.1-7.3版本语言进行开发,但是勉强可以支持到5.4版本

2) Origin使用单一入口方式进行应用访问

3) Origin使用子命名规则进行文件类型分类 （class） Controller 控制器文件、（func）function 函数文件、（cfg）configuration 配置文件

4) Origin使用面向过程来实现一般开发需求调用，深度开发则使用面向对象

5) Origin使用单字母方法命名（后续版本将舍弃该结构方式）封装低效方法集合，标准调用函数则使用完整名词描述进行方法表述

6) Origin中重新定义了set、get结构，使用方法整合在plan_b封装中，有mapping映射结构来是实现其主要功能特性

7) Origin主要版本只支持mysql数据库，plan_b则增加了mongodb、redis数据库的支持

8) Origin第一个文件从2016年4月开始编写，历时6个月7天才编写出来

9) Origin第一个开发版于2017年9月在（金融）公司内部发布（由于保密协议要求不便标注公司名称）

10) Origin plan_b版本发布于2018年5月，其版本主要为实现2人进行复杂系统开发目的进行的工具化衍生开发（实际项目仍在应用，整体消耗170万元RMB），plan_b部分功能已从Origin中删除归属公司内部版权所有

## 目录说明：
> Root

>>Apply： 应用目录  
>>>Common：公共函数目录  
Config：自定义配置目录  
Home：应用访问目录，默认访问目录（开发者编辑文件目录）  
>>>>Common：公共函数目录（自定义编辑，框架默认引用文件：Public.class.php）  
Config：开发者自定义配置文件目录（自定义编辑，框架默认引用文件：Config.cfg.php）  
Controller：应用控制器文件目录（默认控制器：Index.class.php）  
Model：数据映射模板文件目录（暂不使用）  
View：`视图模板文件（html文件）`目录（默认结构包含：`控制器同名文件夹`Index(首字母大写)，以及与`函数同名`index.html（全小写））  

>>>Route：路由配置目录  

>>Origin：框架核心目录  
>>>Application：应用主控制封装目录  
Config：框架配置目录  
Font：框架字体文件目录  
Kernel框架功能内核目录  
Method框架方法封装目录

>>Resource：资源目录  
>>>Jscript：Javascript文件目录（预设）  
Media：多媒体文件目录（预设）  
Style：样式文件目录（预设）  

>>.htaccess：分布式配置文件  
>>index.php：入口文件  
