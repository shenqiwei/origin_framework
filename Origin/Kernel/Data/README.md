<span id='origin_top'></span>
## Origin 框架数据功能目录

当前版本Origin支持Mysql，Redis，Mongodb（试验开发阶段），配置内容调用根据Config文件中的`DATA_TYPE`选项内容表述类型(mysql,redis,mongo)限定

当前版本数据库的引入不再使用对应方法调用，改用类静态方法`Origin\Kernel\Data\DB::DB_Func()`的方式进行调用

> Mysql说明
>> 当前版本的Origin框架Mysql封装结构使用PDO::Mysql进行开发，并取消mysqld内容支持，调用mysql对象时直接使用`Origin\Kernel\Data\DB::Mysql(__Resource_name__)`获取实例，`__resource_name__`为数据源名称，既Config中`DATA_NAME`选项内容
>
> Mysql语法说明
>> Mysql数据库使用串联结构语法，方法引用后默认返回对象，在调用执行语法钱，可以一直通过函数返回值状态实现函数调用：   
>> `function origin(){`    
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$_mysql = Origin\Kernel\Data\DB::Mysql("origin_mysql");` # 获取mysql实例   
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$_list = $_mysql->table("origin")->where("id <> 0")->select()` # 串联语法查询    
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$this->param("list",$_list);`   
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$this->view();`   
>> `}`
>
>#### 函数快速入口
>[`mysql操作函数说明`](#mysql_operation)|[`mysql执行函数说明`](#mysql_excute)
>
><span id='resource'></span>
> Mysql函数
>> 当前origin版本中，Mysql封装类提供了几乎完整mysql操作支持函数内容，个别重用语法，在版本中进行了拆分，已减少开发者记忆负担
>>
>> table函数：   
>>>`$_mysql->table(__table_name__,__as_name__);`      
>>> 用于设定操作主表信息，该函数包好两个参数项，第一个参数用于设置操作对象表名称，第二个参数用于设置表别名（在多表查询，级联查询时，更加实用）默认值为null   
>> 
>> field函数：   
>>> `$_mysql->field(__array__)` 
>>> 用于select操作下，限定查询的字段，参数类型为数组，设置字段时，使用Key/Value数组，key为字段名，value为字段别名
>>> 无别名样例：
>>> `$_mysql->field(array("id","time"))`
>>
>>> 有别名样例：
>>> `$_mysql->fetch(array("id"=>"id_as_name","time"=>"time_as_name"))`
>> 
>> data函数：   
>>> `$_mysql->data(__array__)`     
>>> 用于insert和update操作的数据接入，参数类型为Key/Value数组，Key为字段名，Value为插入(修改)值
>> 
>> where函数：   
>>> `$_mysql->where(__condition__)`   
>>> 该函数用于设定mysql语句执行条件，参数支持数组和字符串两种格式，数组基于多维数组的结构解析，字符串直接参数sql语句语言进行设置，需要注意的是where函数同时支持运算符号和运算符号代称   
>> 
>> limit函数：   
>>> `$_mysql->limit(__start__,__row__)`    
>>> 用于限制列表显示数量，参数类型都为整数，第一参数为列表起始位置（当第二参数不进行设置时），参数值大于了代表显示数量，参数小于等于零时，mysql默认不进行显示数量限制，第二参数设置显示数量，当参数值为0时，框架只对第一参数进行执行操作
>>> 显示数量样例：    
>>> `$_mysql->limit(6)` # 这里表示数据库查询显示5条信息   
>> 
>>> 区间显示样例：   
>>> `$_mysql->limit(2,10)`, # 本样例表示数据指针从2开始显示10条数据   
>>
>> order函数：   
>>> `$_mysql->order(__order__)`
>>> Mysql封装，查询语句排序函数，参数支持数组和字符串双类型:   
>>> 数组实例：  
>>> `$_mysql->order(array("id"=>"asc","time"=>"desc"))`   
>>
>>> 字符串实例：   
>>> `$_mysql->order("id asc,time desc")`   
>>
>> 两组表达出来内容同为 `order by id asc,time desc`    
>> 
>> fetch函数：   
>>> `$_mysql->fetch(__type__)`   
>>> 用于约束查询语句显示格式，默认值为 null 同时返回 number/value（数值数组） 和 key/value（关联数组） 结构列表  
>>> 默认结构样例：   
>>> `$_mysql->fetch()`    
>>
>>> key/value 关联数组样例：   
>>> `$_mysql->fetch("kv")` # 值为：kv 返回 key/value 结构   
>>
>>> number/value 数值数组样例：   
>>> `$_mysql->fetch("nv")` # 值为: nv 返回 number/value 结构    
>>
>
><span id='resource'></span>
> Mysql执行函数
>
> 在Mysql封装中，提供了count，select，insert，update，delete，query6个执行函数，函数无参数内容，调用函数既返回最终执行结果，结尾不再具备调用其他函数的功能：
>
>> count函数：   
>>> 为select衍生执行函数，仅负责执行查询数量的操作，该函数执行方式为在sql语句`select`内中默认增加`count(*)`,其返回类型为 等大于零（greater than or equal to zero）的整数   
>>> `$_mysql->count()` # 该方法与select查询中`$_mysql->total()`函数调用所有实现的效果相同
>
>> select函数：   
>>> 用于进行数据内容查询，其返回结果为二维数组，其结构表述方式（number/value：数值数组）和（key/value：关联数组）混合结构，该返回结构可以使用`$_mysql->fetch()`进行设置   
>>> `$_mysql->select()`   
>>> select查询单条数据是可以使用`$_mysql->limit(1)`约束其返回内容为只用一条数据的二维数组也可以使用PHP特性`$_mysql->select()[0]`获取返回值中首元素值，该值为一维数组   
>
>> insert函数：   
>>> 用于数据插入，函数调用必须与`$_mysql->data()`功能函数配合使用，函数返回值为 等大于零（greater than or equal to zero）等整数    
>>> `$_mysql->insert()` # sql语句表达结构 insert into .table (.field) value (.value)   
>
>> update函数：
>>> 用于数据的修改更新，该函数调用必须与`$_mysql->data()`和`$_mysql->where()`配合使用，函数返回值为 等大于零（greater than or equal to zero）等整数    
>>> `$_mysql->update()` # update .table set .date where .condition   
>
>> delete桉树：
>>> 用于数据的删除，函数调用必须与`$_mysql->where()`功能函数配合使用，函数返回值为 等大于零（greater than or equal to zero）等整数   
>>> `$_mysql->delete()` # sql语句表述结构 delete from .table where .condition    