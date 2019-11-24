<span id='origin_method'></span>
## Method 功能函数目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin">返回</a>]

该目录中存放Origin预设功能函数以及集合型功能函数，所以函数的调用方法都存放在Function.php文件中
>函数列表

>> `Config(__item__)`  
>>> 配置信息：调用框架配置(Kernel/Config)及应用配置(Application/Config/Config)文件配置内容,暂不支持自定义配置栏目   

>> `Configuration(__item__)`
>>> 原始配置信息：调用原始配置文件(Kernel/Config)中的配置信息

>> #####`Cookie(__key__,__value__)`Cookie设置函数
>>> Cookie 设置操作说明：
>>> 

>> #####`Import(__guide__)`文件引用函数：（框架内核文件调用共用函数，暂不支持插件结构调用）
>>> 

>> #####`Write(__uri__,__msg__)`文件写入函数：常规文件写入函数
>>> 

>> #####`sLog(__msg__)`数据操作日志：
>>> 

>> #####`eLog(__msg__)`错误日志：
>>> 

>> #####`iLog(__msg__)`异常日志：
>>> 

>> #####`Mysql(__resource__)`mysql数据库调用：
>>> 

>> #####`Redis(__resource__)`redis数据库调用
>>> 

>> #####`Mongodb(__resource__)`mongodb数据库调用
>>> 

>> #####`Number`页码翻页函数
>>> 
>>> @Number操作说明
>>> `Number(__page__,__serach__,__cols__)`   
>>>

>> #####`Page(__url__,__count__,__current__,__row__,__serach__)`翻页参数执行函数
>>> 
>>> @Page操作说明 
>>> `Page(__url__,__count__,__current__,__row__,__serach__)`   
>>>

>> #####`Verify(__width__,__height__)`验证码调用方法
>>> 
>>> @Verify 操作说明
>>> `Verify(__width__,__height__)`   
>>> `__width__`：设置画布宽度，默认值 120   
>>> `__height__`：设置画布高度，默认值 50  

>> #####`Input`获取请求参数值
>>>   
>>> @Input 操作说明：   
>>>`Input(__key__,__default__)`   
>>> `__key__`：表单名称一致，推荐使用单词，或词组作为名称表达方式,约束请求类型是使用(post.|get.)开头   
>>> `__default__`：设置无效状态默认值，无基本约束    
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
   