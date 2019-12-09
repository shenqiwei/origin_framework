<span id='origin_kernel'></span>
## Kernel 内核目录 [<a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin">返回</a>]
该目录用于存放Origin主要功能封装类

#### 快速入口
[`File说明`](#file)|[`Upload说明`](#upload)|[`View说明`](#view)|[`Label说明`](#label)|[`request说明`](#request)|[`DB说明`](#db)|[`Validate说明`](#validate)|[`Output说明`](#output)|[`Curl说明`](#curl)|[`Verify说明`](#verify)    

<span id='file'></span>
## File 文件操作封装类 [[返回TOP](#origin_kernel)]

File封装类中提供了三个操作函数（resource | manage | write），主要用于文件（夹）路径查询，文件（夹）创建、修改、移动、复制、删除、文件写入功能的实现

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\File\File;`   
>
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
## Upload文件上传封装类 [[返回TOP](#origin_kernel)]

Upload封装类用于实现Origin文件上传功能

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\File\Upload;`   
>
> 类的使用需要进行实例化，构造器函数取消了参数设置，故现有版本的实例化不需要标注表单名称
>> `$_upload = new Upload()`
>
> Upload封装类提供了基础参数设置函数
>> `$_upload->input(__input__)` # 设置表单名称 <from>表单需增加 type = 'multipart/form-data' 上传功能有效   
>> `$_upload->type(__type__)` # 设置文件类型限制，参数可以设置为字符串（单一类型限制），也可以设置为数组，默认值为空 不做任何限制   
>> `$_upload->size(__size__)` # 设置文件大小限制，参数为整数，默认值 0 不做任何限制    
>> `$_upload->store(__store__)` # 设置存储位置，默认值 null ，预设值为项目资源目录 /Resource/Upload/，在预设状态下上传文件，框架会自动生成一个以日期为标记的目录，用于拆分上传文件     
>
> Upload上传执行函数   
>> `$_upload->update()` # 执行上传操作，该函数会返回两种状态值，一种上传文件名称或文件列表（多文件上传），一种是返回布尔值 false 上传失败
>
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
## View显示模板封装类 [[返回TOP](#origin_kernel)]

View主要用于Origin应用功能模板方法 `Origin\Application\Controller->view(__view__)` 的调取实现,不参与主要开发功能实现，故不进行功能说明

<span id='label'></span>
## Label模板语法解析封装类 [[返回TOP](#origin_kernel)]

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
   
<span id='request'></span>
## Request请求器封装类 [[返回TOP](#origin_kernel)]

Request现阶段版本仅支持get/post请求

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\Parameter\Request;`
>
> Request声明对象时注入三个基本参数：请求对象名称、默认值、请求类型   
> `$_request = new Request(__variable__,__default__,__method__)`   
>> `__veriable__`：表单名称，不能以符号或数字开头的字符串   
>> `__default__`：请求器默认，用于在请求内容无效时，进行默认回复   
>> `__method__`： 请求器请求方法，默认值 request(post优先的表单请求，在无post请求时直接返回get请求，如有post 请求同键名条件下返回post请求内容)   
>
> Request类中获取请求内容时需调用`$_request->main()`来实现请求内容的获取，该方法无参数信息，方法返回值为字符串
>
> Request类还有一个移除函数`$_request->delete()`，当使用该函数时，请求器会直接注销当前表单名下所有内容

<span id='db'></span>
## DB数据库访问封装类 <a href="https://github.com/shenqiwei/Origin-Framework/tree/master/Origin/Kernel/Data">[查看详情]</a>[[返回TOP](#origin_kernel)]

<span id='validate'></span>
## Validate验证封装类 [[返回TOP](#origin_kernel)]

Validate主要服务于Origin基础验证结构，Validate提供了内容空状态验证（not null），值域范围验证（min < strlen(variable) or number < max），格式验证 (preg_match).

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\Parameter\Validate;`
>
> Validate声明对象时需注入验证对象变量内容,验证结构提供是三个基本函数（_empty,_size,_type），两个独立功能验证函数(_ipv4,_ipv6),一个错误信息返回函数（getError）
>> `$_validate = new Validate(__variable);`
>> `__veriable__`：表单名称，不能以符号或数字开头的字符串,也可以验证数组，但仅限于_empty方法函数
>
> Validate主体验证函数：
> `$_validate->_empty()`：用于验证验证对象是否为空（null），并对is_null和empty特例结构进行验证，返回值为boolean类型，返回值为：true 值不为空，false 值为空   
>
> `$_validate->_size(__min__,__max__)`：用于验证值得有效范围（比对数字时，则验证值得大小是否符合值域要求），函数返回值为boolean类型，返回值为：true 符合要求 ，false 不符合    
>> `__min__`：值限制域最小值，值必须为等大于0的数字
>> `__max__`：值限制域最大值，值必须为等大于0的数字，在最小值大于最大值时，类函数会将最大值和最小值内容互换   
>
> `$_validate->_type(__format__)`：用于验证验证对象值格式是否符合格式限制（正则表达式）要求，函数返回值为boolean类型，返回值为：true 符合要求 ，false 不符合     
>> `__format__`：正则表达式
>
> Validate的独立函数只负责检验ip结构内容,所以就不做单独的说明    
>
> 在实例中Validate延伸创建了一组函数用于进行基本环境内容验证,所有函数主要使用`$_validate->_type()`来实现功能验证    
>
> 在不使用validate类的情况下可以使用`is_true`验证函数来实现内容验证     
> `is_true(__format__,__variable__,__min__,__max__)` 可以进行完整的内容结构验证，并实现值域结构的限制验证    
>> `__format__`：验证变量结构内容的正则表达式，先设定结构语言主要是参照了preg_match|preg_match_all正则匹配语法   
>> `__variable__`：被验证对象变量内容   
>> `__min__`：最小值域限制   
>> `__max__`：最大值域限制
>
> 一般应用函数列表：
>> 中文名验证方法：`cn_name`, 英文名验证方法：`en_name`，固定电话验证：`is_tel`，400|800电话验证：`is_tel2`，移动电话验证：`is_mobile`，电子邮箱验证：`is_email`，Ipv4验证：`ipv4`，Ipv6验证：`ipv6`，中文验证：`is_cn`，英文验证：`is_en`,网络地址验证：`is_host`
>
> 通用验证函数列表:
>> 用户名验证：`is_username`, 轻密码验证：`weak_password`，一般密码验证：`strong_password`，安全密码验证：`safe_password`   

<span id='output'></span>
## Output输出封装类 [[返回TOP](#origin_kernel)]

Output封装类负责基本内容的输出和展示，其中包括Json格式输出，提示信息的展示，异常错误的提醒

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\Parameter\Output;`
>
> Output实例化
>> `$_output = new Output();`
>
> JSON格式输出函数：   
>> `$_output->json(__array__);`：该函数主要是讲数组类型数据转化为Json字符串输出，其参数类型必须为数组，若使用非数组参数，则直接字符串类型进行输出
>
> 提示信息展示函数：   
>> `$_output->output(__time__,__message__,__redirect__,__setting__);`：该函数主要用于用户操作提示内容的展示，模板和参数一一对应，该函数主要是为Origin/Application/Controller内函数`$this->success()`和`$this->error()`提供功能支持，暂不对开发者和一般用开发实际功能   
>
> PHP层异常提示函数：   
>> `$_output->base()`: 用于支持php底层异常捕捉函数`error_get_last()`返回的信息内容输出，该函数不对开发者和一般用户开放    
>
> 框架封装类异常提示函数：   
>> `$_output->error()`：用于Origin/Kernel目录下所有封装类进行功能操作异常信息的捕捉和展示，该函数暂不对开发者和一般用户开发   
>
> 框架封装类内部封装功能支持异常提示函数：
>> `$_output->exception()`：该函数与`$_output->error()`函数在功能和实现上完全一致，不同在于该函数中假如异常日志记录的功能，该函数暂不对开发者和一般用户开发   

<span id='curl'></span>
## Curl在线访问封装类 [[返回TOP](#origin_kernel)]

Curl用于访问在线资源模拟GET/POST请求的功能封装，该封装提供两个基本功能函数，既GET、POST，封装类功能特别表达与curl模块完全，语法与相应规则沿用curl

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\Protocol\Curl;`
>
> Curl实例化
>> `$_curl = new Curl();`声明函数对象时，可以设置UTF-8编码形态，构造器参数类型为boolean，默认是值false 不开启UTF-8编码
>
> get函数说明：   
>> `$_curl->get(__url__,__param__,__peer__,__host__)`   
>> `__url__`：访问连接地址   
>> `__param__`：参数数组   
>> `__peer__`：是否验证证书   
>> `__host__`：是否验证访问地址   
>
> post函数说明：   
>> `$_curl->get(__url__,__param__,__type__,__peer__,__host__)`   
>> `__url__`：访问连接地址   
>> `__param__`：参数数组   
>> `__type__`：参数传输类型，默认是以表单形式进行提交，参数提供了json和xml格式选项   
>> `__peer__`：是否验证证书    
>> `__host__`：是否验证访问地址    
>
> curl访问后方法会返回访问内容信息，在这curl封装提供了`$_curl->get_curl_receipt()`函数用于在访问异常时，提供curl异常信息内容    

<span id='verify'></span>
## Verify验证码功能封装类 [[返回TOP](#origin_kernel)]

Verify封装是基于GD模块开发的一组验证码功能封装结构，实际使用中仅需要设置画布大小，然后调用对应函数方式既可完成功能调用

> 类的调用，省略include和require函数，直接使用命名空间调用   
>> `use Origin\Kernel\Export\Verify;`
>
> Verify实例化
>> `$_v = new Verify(__width__,__height__); # 画布的大小设置在声明对象时进行，然后根据开发需要调用对应验证码函数，当前版本提供了五种基本验证码
>
>> `$_v->number()`：全数字验证码函数   
>> `$_v->letter()`： 英文字母与数字混合验证码函数   
>> `$_v->han()`：汉字验证码函数   
>> `$_v->math()`：三元运算验证码函数   