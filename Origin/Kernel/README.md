<span id='origin_kernel'></span>
## Kernel 内核目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin">返回</a>]
该目录用于存放Origin主要功能封装类

#### 快速入口
[`File说明`](#file)|[`Upload说明`](#upload)|[`View说明`](#view)|[`Label说明`](#label)|[`request说明`](#Validate)|[`DB说明`](#db)|[`validate说明`](#validate)|[`Output说明`](#outeput)|[`Filter说明`](#filter)|[`Curl说明`](#curl)|[`Verify说明`](#verify)    

<span id='file'></span>
## File 文件操作封装类 [[返回TOP](#origin_top)]

File封装类中提供了三个操作函数（resource | manage | write），主要用于文件（夹）路径查询，文件（夹）创建、修改、移动、复制、删除、文件写入功能的实现

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\File\File;`   

> 类的使用需要进行实例化，构造器函数取消了参数设置，故现有版本的File实例化时，不再需要预设目录地址，File默认从项目根目录位置开始进行内容调用   
>> `$_file = new File()`

## File函数说明

#### 函数快速入口
[`resource说明`](#resource)|[`manage说明`](#manage)|[`write说明`](#write)

<span id='resource'></span>
resource():
> 文件（夹）路径断点识别函数，用于对文件（夹）地址进行路径有效验证，如文件路径无效则进行断点跟踪，并返回断点位置参数
>> `$_file->resource(__url__);`方法参数为非空(not null)字符串(string),路径连接符号使用（/）, 返回值为（ boolean类型）true or false
>>> 例：文件（夹）路径无效，函数会记录断点内容，使用getBreakpoint函数就可以获得断点内容   
>>> `$_file = new File();` # 实例化对象   
>>> `$_receipt = $_file->resource("home/view/index.html")`   
>>> `if($_receipt){` # 返回值状态    
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("地址有效");`   
>>> `}else{`    
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("文件断点：".$_file->getBreakpoint());` # 获取断点内容   
>>> `}`
>>    
>> resource函数中使用is_file和is_dir函数判断地址有效性，常规开发中，如不需要使用集合封装函数功能，推荐直接使用is_file和is_dir函数，如果仅用于判断当前地址路径是否有效可以直接使用is_folder函数   

manage():
> 文件（夹）管理函数，用于对文件（夹）进行创建、修改、复制、删除操作
>> `$_file->manage(__url__,__operate__,__name__,__throw__);`   
>> ####参数说明:   
>> `__url___`：文件（夹）路径参数，参数为非空(not null)字符串(string),路径连接符号使用（/）   
>> `__operate__`：操作类型，提供四种参数内容选择 create(创建)、full(补全创建)、rename(重命名)、remove(移除)，其他操作项暂不支持,使用full创建文件（夹）时，函数会调用resource函数进行断点查找，并完成剩余路径信息的创建，remove不支持非空文件夹移除   
>> `__name__`：重命名信息参数，该参数默认值为null（空），`__operate__`参数选择rename，参数需填入非空字符串
>> `__throw__`：异常状态默认值 false 不报异常信息， true报异常信息，并记录到异常日志中   
>>> 例：   
>>> `$_file = new File();`   
>>> `$_receipt = $_file->manage("/home/view/old.html","rename","new",true);` # 对文件名修改，操作对象路径是以项目根目录地址为起始的相对路径，修改名仅需要填入修改名称，不需要标明路径内容    
>>> `if($_receipt){`   
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("修改成功");`   
>>> `}else{`   
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("修改失败");`   
>>> `}`
>>   
>> 当throw 值设置为true后，File封装中提供了一个异常信息出口，`$_file ->getError();`可以获取异常信息   

write():
> 文件读写函数
>> `$_file->write(__url__,__operate__,__msg__);`
>> #### 参数说明:   
>> `__url__`：文件（夹）路径参数，参数为非空(not null)字符串(string),路径连接符号使用（/）    
>> `__opearte__`：操作类型，根据fopen函数内容提供了8中操作类型，并追加了两个操作类型   
>>> r:读取操作 操作方式：r   
>>> rw:读写操作 操作方式：r+   
>>> sr: 数据结构读取操作 操作对应函数file   
>>> w：写入操作 操作方式：w   
>>> lw：前写入 操作方式：w+   
>>> cw:缺失创建并写入 调用对应函数manage，操作方式：w+   
>>> bw：后写入 操作方式：a   
>>> fw：补充写入 操作方式：a+   
>>> rr: 读取全文 调用对应函数 file_get_contents   
>>> re：重写 调用对应函数 file_put_contents      
>>
>> `__msg__`：写入内容，该参数默认值为null（空），`__operate__`参数选择写入操作，参数需填入非空字符串    
>> 函数在返回操作回执时，一般会返回true or false，但在读取操作时，回返回读取内容 or false，这里在调用函数时需注意