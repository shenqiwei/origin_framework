# Origin Framework
Origin 架构主要是为解决入门级开发人员在PHP开发中的基础应用问题： 

![欢迎页](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/welcome.png)
## 基本说明：
1) Origin使用PHP7.3及以下版本进行开发，部分功能可支持到5.4版本，7.4及以上版本将作为独立版本支持

2) Origin使用单一入口方式进行应用访问（1.0版本后，自动加载结构，不再使用预设注册包的方式来进行包加载，而是使用命名空间限定来加载包内容）

3) Origin使用面向过程来实现一般开发需求调用，深度开发则使用面向对象（1.0版本后功能的调用趋向面向对象）

4) Origin使用基础单词命名方式封装低效方法集合，标准调用函数则使用完整名词描述进行方法表述（1.0版本后，部分函数内容删除，结构语法进行了优化）

5) Origin中重新定义了set、get结构，使用方法整合在plan_b(已暂停更新)封装中，有mapping映射结构来是实现其主要功能特性（该功能已废止），1.0版本后Origin增加了request请求结构的准入模板模块进行预验证

6) Origin当前版本支持Mysql，MariaDB，SQL server，PostgreSQL，Sqlite，Oracle，Mongodb（基础功能支持），Redis（部分结构支持）数据库   

## 目录说明：
> Root

>>application： 应用目录 <a href="https://github.com/shenqiwei/origin_framework/tree/master/application">查看文档</a>  
>>>common：公共函数目录  
config：自定义配置目录  
home：应用访问目录，默认访问目录（开发者编辑文件目录）  
>>>>common：公共函数目录（自定义编辑，框架默认引用文件：public.php）  
classes：应用控制类文件目录（默认控制器：Index.php）  
model：数据映射模板文件目录（暂不使用）  
template：`视图模板文件（html文件）`目录（默认结构包含：`控制器同名文件夹`index(首字母大写)，以及与`函数同名`index.html（全小写））   

>>origin：框架核心目录 <a href="https://github.com/shenqiwei/origin_framework/tree/master/origin">查看文档</a>  
>>>application：应用主控制封装目录（已取消该结构，该目录中文件全部转移至Package目录中）  
config：框架配置目录  
font：框架字体文件目录  
>library框架方法封装目录   
package框架功能内核目录  

>>resource：资源目录  （HTML资源常量：\_\_RESOURCE__）
>>>Jscript：Javascript文件目录（已取消该结构预设）  
Media：多媒体文件目录（已取消该结构预设）  
Style：样式文件目录（已取消该结构预设）  
public：公共文件目录（web资源文件，公共html）   
upload：上传文件目录（根据<天>时间进行分割存储上传文件，子文件夹自动创建）   
buffer：缓存文件目录   
`注：原始结构常量中取消该文件夹模板常量标记解析功能`

>>.htaccess：地址重定义配置  
>>index.php：入口文件  
