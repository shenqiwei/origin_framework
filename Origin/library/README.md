<span id='origin_method'></span>
## Method 功能函数目录 [<a href="https://github.com/shenqiwei/origin_framework/tree/master/origin">返回</a>]

该目录中存放Origin预设功能函数以及集合型功能函数，所以函数的调用方法都存放在Function.php文件中
>函数列表

>> `Config(__item__)`  
>>> 配置信息：调用框架配置(Package/Config)及应用配置(Application/Config/Config.php)文件配置内容,暂不支持自定义配置栏目   

>> `Configuration(__item__)`
>>> 原始配置信息：调用原始配置文件(Package/Config)中的配置信息

>> #####`Cookie(__key__,__value__)`Cookie设置函数
>>> Cookie 设置操作说明：该函数操作流程和功能模块暂时不提供使用
>>> 

>> #####`Import(__guide__)`文件引用函数：（框架内核文件调用共用函数，暂不支持插件结构调用）
>>> 

>> #####`Write`文件写入函数：常规文件写入函数
>>> 
>>> `Write(__uri__,__msg__)`
>>> `__uri__`：文件访问地址，如果文件不存在，函数则自动创建一个新的文件（file write w），路径使用(/)符号链接
>>> `__msg__`：写入内容，string类型
>>> sLog,eLog,iLog函数皆为Write派生函数

>> #####`sLog(__msg__)`数据操作日志：
>>> 
>>> @sLog操作说明
>>> `sLog(__msg__)` 
>>> `__msg__`：数据连接语句日志，主要用于踪语句内容，和基本参数内容，该函数为数据默认记录日志函数，暂不对开发及用户开放
>>> 默认查看地址 /Log/Connect，日志根据日期时间分割日志内容

>> #####`eLog(__msg__)`错误日志：
>>>
>>> @eLog操作说明
>>> `ieog(__msg__)`
>>> `__msg__`：框架中用户及系统操作异常信息日志，用户及开发人员可自行设定日志使用状态和放置位置
>>> 默认查看地址 /Log/Error，日志根据日期时间分割日志内容 

>> #####`iLog`连接日志：
>>> 
>>> @iLog操作说明
>>> `iLog(__msg__)`
>>> `__msg__`：框架http请求连接记录信息，该函数信息指在服务请求加载时被使用
>>> 默认查看地址 /Log/Access，日志根据日期时间分割日志内容

>> #####`Page`翻页参数执行函数
>>> 
>>> @Page操作说明 
>>> `Page(__url__,__count__,__current__,__row__,__serach__)`   
>>> `__url__`：地址连接，使用相对地址表述 (/home/index/index.html)   
>>> `__count__`：数据总数（使用 mysql->count() 获取数据总数），整数类型   
>>> `__current__`：当前页页码，整数类型，默认值：1   
>>> `__row__`：单页显示行数 大于0的任意整型值，整数类型，默认值：10   
>>> `__serach__`：搜索条件使用（get结构变量请求）(&name=小明&age=12&sex=男)，默认值：null   

page函数功能实现格式：   
![page函数功能实现格式](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/mysql_page.png)   

>>> @Page函数返回值为一个数组集合,数组中各元素内容调用直接使用标签变量调用方式即可   
>>> 'url # 连接地址   
>>> 'count' # 数据总数    
>>> 'current' # 当前页码   
>>> 'begin' # 当前列表起始查询位置    
>>> 'limit' # 显示数量   
>>> 'page_begin' # 列表数据显示起始位置    
>>> 'page_count' # 列表数据显示结束位置    
>>> 'first_url # 翻页第一页连接    
>>> 'last_url' # 翻页上一页连接   
>>> 'next_url' # 翻页下一页连接   
>>> 'end_url'  # 翻页最后一页连接    

page函数内容html实现格式：  
page上一页样例：![page上一页样例](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/last.png)   
page下一页样例：![page下一页样例](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/next.png)   

>> #####`Number`页码翻页函数
>>> 
>>> @Number操作说明
>>> `Number(__page__,__cols__)`   
>>> `__page__`：page方法返回数组集合   
>>> `__cols__`：显示页脚，整数类型，默认值：5    

number函数功能实现格式：   
![number函数内容html实现格式](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/mysql_number_param.png)
>>> @Number函数返回值为一个数组集合，其内部元素的调用方法需使用for循环标签实现   
>>> 'page' # 当前页页码   
>>> 'url' # 翻页连接    

number函数内容html实现格式：   
![number函数内容html实现格式](https://github.com/shenqiwei/origin_framework/blob/master/Screenshot/mysql_number.png)

>> #####`Verify(__width__,__height__)`验证码调用方法
>>> 
>>> @Verify 操作说明
>>> `Verify(__width__,__height__)`   
>>> `__width__`：设置画布宽度，默认值 120   
>>> `__height__`：设置画布高度，默认值 50  

>> #####`Input`获取请求参数值
>>>   
>>> @Input 操作说明：   
>>> `Input(__key__,__default__)`   
>>> 调用方法：`$_input = Input("input_name");`    
>>> `__key__`：表单名称一致，推荐使用单词，或词组作为名称表达方式,约束请求类型是使用(post.|get.)开头   
>>> `__default__`：设置无效状态默认值，无基本约束，默认值为null    
>>> 该方法为Request方法的简化版本

>> #####`Request`请求操作函数
>>>   
>>> @Request 操作说明：  
>>> `Request(__key__,__default__,__type__,__delete__)`   
>>> `__key__`：表单名称一致，推荐使用单词，或词组作为名称表达方式,约束请求类型是使用(post.|get.)开头    
>>> `__default__`：设置无效状态默认值，无基本约束    
>>> `__type__`：约束请求变量类型（string、int、float、double、boolean）   
>>> `__delete__`：删除表单内容项，变量类型boolean（默认值false 不执行删除动作，true 执行删除动作）   
>>> @ 返回值内容由约束类型决定   

>> #####`Session` 会话操作函数 
>>>   
>>> @Session 设置说明：   
>>> `Session(__session_item__,__key__)`   
>>> `__session_item__`：会话操作项   
>>> 1.会话ID 操作选项 `session:id`   
>>> 2.注销全部会话内容 操作选项 `session:unset`   
>>> 3.清除全部会话内容 操作选项 `session:destroy`   
>>> 4.重置会话ID 操作选项 `session:regenerate`   
>>> 5.重置会话内容 操作选项 `session:reset`   
>>> 6.删除会话内容 操作选项 `session:delete` `@需设置操作对象键值`    
>>> 7.编码会话 操作选项 `session:encode`   
>>> 8.解码会话 操作选项 `session:decode` `@需设置操作对象键值`   
>>> `__key__`：操作session会话键值 
>>>
>>> @Session 操作语法：  
>>> `Session(__session_key__,__value__)`    
>>> `Session(__session_key__)`获取会话内容   
>>> `__session_key__`: 会话键值名，限制使用不以符号开头和结尾的字母数组（A-Za-z）符号(-_)的组合   
>>>` __value__`: 设置值 只能是不包非法结构的字符串，数字，true，false以及不超过两个维度的数组，不能存储对象和多为数组 
>>> @ 返回值内容由使用者设置会话时填入内容决定
   