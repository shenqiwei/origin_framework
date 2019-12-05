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

<span id='manage'></span>
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

<span id='write'></span>
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

<span id='upload'></span>
## Upload文件上传封装类 [[返回TOP](#origin_top)]

Upload封装类用于实现Origin文件上传功能

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\File\Upload;`   

> 类的使用需要进行实例化，构造器函数取消了参数设置，故现有版本的实例化不需要标注表单名称
>> `$_upload = new Upload()`

> Upload封装类提供了基础参数设置函数
>> `$_upload->input(__input__)` # 设置表单名称 <from>表单需增加 type = 'multipart/form-data' 上传功能有效   
>> `$_upload->type(__type__)` # 设置文件类型限制，参数可以设置为字符串（单一类型限制），也可以设置为数组，默认值为空 不做任何限制   
>> `$_upload->size(__size__)` # 设置文件大小限制，参数为整数，默认值 0 不做任何限制    
>> `$_upload->store(__store__)` # 设置存储位置，默认值 null ，预设值为项目资源目录 /Resource/Upload/，在预设状态下上传文件，框架会自动生成一个以日期为标记的目录，用于拆分上传文件     

> Upload上传执行函数   
>> `$_upload->update()` # 执行上传操作，该函数会返回两种状态值，一种上传文件名称或文件列表（多文件上传），一种是返回布尔值 false 上传失败

> Upload 错误回执函数
>> `$_upload->getError()` # 上传函数返回false时，错误回执返回不为空（not null）内容信息

> 例：    
>> `$_upload = new Upload()` # 实例化类    
>> `$_upload->input("file")` # 设置上传表单名   
>> `$_upload->type(array("jpg","png"));` # 设置上传文件限制   
>> `$_upload->size(50000000);` # 设置上传大小限制   
>> `$_upload->store("Resource/user/upload")` # 设置文件存储位置    
>> `$_receipt = $_upload->update();` # 执行上传    
>> `if($_receipt){`    
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo($_receipt);` # 返回文件上传后信息     
>> `}else{`   
>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`echo($_upload->getError();` # 显示错误信息    
>> `}`   
>
> 上传多文件时，仅需要设置file表单名称既可以支持多文件上传,`<input type="file" name="file">`单一上传，`<input type="file" name="file[]">`多文件上传，Upload表单名设置时，仅标注名称（按照单一上传名称设置表单名）   

<span id='view'></span>
## View显示模板封装类 [[返回TOP](#origin_top)]

View主要用于Origin应用功能模板方法 `Origin\Application\Controller->view(__view__)` 的调取实现,不参与主要开发功能实现，故不进行功能说明

<span id='label'></span>
## Label模板语法解析封装类 [[返回TOP](#origin_top)]

Label主要用于Origin应用功能模板中功能语法标签的解析

Label输出变量方法 `{$variable_name}`即大括号，php变量符号，变量名的组合，该结构表达内容与`<?php echo($variable_name); ?>`一致,简单的概括就是带有大括号的变量即为可输出变量。

Label 还提供了一种引用标签，一种条件表达式标签，两种循环标签
> include 文件引用标签，主要用于调用公共html文件内容，资源应用地址起始位置 `Resource/Public`   
>> 语法：`<include href="include_file_url"/>`
>
> if 逻辑表达式标签   
> if 条件 由变量 运算符号 条件变量/值组成 ，条件内容变量由php变量符号和变量名组成   
>> 语法：   
>> if 起始标签 `<if condition = 'variable operative_symbol conditions_variable'>`   
>> elseif 标签 `<elseif condition = 'variable operative_symbol conditions_variable'/>`   
>> else 标签 `<else/>`   
>> if 结束标签 `</if>`   
>
> foreach 循环标签    
> 与if变量语法一致，但foreach语法带有别名结构，别名设置需标注出 as 关键词，别名不需要以PHP变量符号开头    
>> 语法：   
>> foreach 起始标签 `<foreach operation = 'variable (as mark_variable)'>`   
>> foreach 结束标签 `</foreach>`   
>
> for 循环标签
> 与if变量语法一致，for带有显示数量限制（1.0版本后限制结构语法暂时取消）
>>语法：   
>> for 起始标签 `<for operation = 'variable to circulation_count'>`   
>> for 结束标签 `</for>`   
   




