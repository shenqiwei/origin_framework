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
> Mysql函数
>> 当前origin版本中，Mysql封装类提供了几乎完整mysql操作支持函数内容，个别重用语法，在版本中进行了拆分，已减少开发者记忆负担
>>
>> `mysql->table`函数：   
>> 

