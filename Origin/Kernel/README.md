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
>>> 例：文件（夹）路径无效是，函数会记录断点内容，使用getBreakpoint函数就可以获得断点内容   
>>> `$_file = new File();` # 实例化对象   
>>> `$_receipt = $_file->resource("home/view/index.html")`   
>>> `if($_receipt){` # 返回值状态    
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("地址有效");`   
>>> `}else{`    
>>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo("文件断点：".$_file->getBreakpoint());` # 获取断点内容   
>>> `}`    
>>> resource函数中使用is_file和is_dir函数判断地址有效性，常规开发中，如不需要使用集合封装函数功能，推荐直接使用is_file和is_dir函数，如果仅用于判断当前地址路径是否有效可以直接使用is_folder函数   

manage():

write():
