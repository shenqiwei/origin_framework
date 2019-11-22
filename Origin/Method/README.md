<span id='origin_method'></span>
## Method 功能函数目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin">返回</a>]

该目录中存放Origin预设功能函数以及集合型功能函数，所以函数的调用方法都存放在Function.php文件中
>函数列表

>> `Config(__item__)`  
>>> 配置信息：调用框架配置(Kernel/Config)及应用配置(Application/Config/Config)文件配置内容,暂不支持自定义配置栏目

>> `Configuration(__item__)`
>>> 原始配置信息：调用原始配置文件(Kernel/Config)中的配置信息

>> `Cookie(__key__,__value__)`
>>> Cookie设置函数：

>> `Import(__guide__)`
>>> 文件引用函数：

>> `Write(__uri__,__msg__)`
>>> 文件写入函数：

>> `sLog(__msg__)`
>>> 数据操作日志：

>> `eLog(__msg__)`
>>> 错误日志：

>> `iLog(__msg__)`
>>> 异常日志：

>> `Mysql(__resource__)`
>>> mysql数据库调用：

>> `Redis(__resource__)`
>>> redis数据库调用

>> `Mongodb(__resource__)`
>>> mongodb数据库调用

>> `Input(__key__,__default__)`
>>> 获取请求参数值

>> `Number(__page__,__serach__,__cols__)`
>>> 页码翻页函数

>> `Page(__url__,__count__,__current__,__row__,__serach__)`
>>> 翻页参数执行函数

>> `Verify(__width__,__height__)`
>>> 验证码调用方法

>> `Request(__key__,__default__,__type__,__delete__)`
>>> 请求操作函数

>> `Session(__key__,__value__)`
>>> 会话操作函数