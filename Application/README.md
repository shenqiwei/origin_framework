<span id='origin_top'></span>
## Origin 应用目录

#### 快速通道
[`Common目录说明`](#common)|[`Config目录说明`](#config)|[`Home目录说明`](#home)|

## Common目录
该目录主要用于存放自定义公共函数及其他第三方组件文件包，目录中默认保留一个Public.php文件用于与各应用目录内容进行连接，Public.php文件可以作为公共引用文件或自定义函数封装使用

## Config目录
本目录仅用于存放Config.php文件，该配置文件功能与Origin\Config\Config.php中配置功能一致，仅用于方便开发者自定义自己需要配置内容

## Home目录(默认访问应用目录)
Home目录中包含5个基本功能目录：    

1.Common 公共函数目录：   
> 与Application\Config功能表述一致，该目录内函数作用仅在当前应用目录下有效   

2.Controller 应用控制器目录：    
> 该目录中仅存放可访问应用控制器文件（类封装文件），框架默认提供Index.php默认访问控制器文件

3.Model 映射模板文件目录(测试开发中，暂不开放)      
4.Route 路由配置文件目录(暂不开放路由功能)    
5.View 视图模板文件目录：   
> 该目录仅用于存放与应用控制器对应名称目录的模板文件，目录结构`控制器同名文件目录/方法同名.html`    s