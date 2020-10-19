# Origin PHP Framework
Origin PHP framework 主要是用于解决PHP开发过程中关于公共结构功能重复编写封装等繁杂的无效行为所以编写和开发的简单程序封装结构；

Origin PHP framework 单一入口方式实现各应用功能访问，并利用MVC特性将程序与界面内容完全分开；

Origin PHP framework 使用内封装结构创建一套简单的web标签工具包，以方便开发者在不使用PHP程序结构的前提下简单实现对数据内容的展示；

Origin PHP framework 2020.9.16 至2020.10.18更新后版本以将部分原有仿写（函数命名，封装逻辑表述）结构舍弃，完全新的定义结构和功能标记规则；    
    
<table>
    <tr>
        <th align="left">快速访问 -- menu</th>
    </tr>
    <tr>
        <td><a href="#welcome">欢迎</a> -- welcome</td>
    </tr>
    <tr>
        <td><a href="#tree">文件目录</a> -- tree</td>
    </tr>
    <tr>
        <td><a href="#basic">基础功能</a> -- basic function</td>
    </tr>
    <tr>
        <td><a href="#config">基础配置</a> -- configuration</td>
    </tr>
    <tr>
        <td><a href="#iif">web标签</a> -- include & if & for</td>
    </tr>
    <tr>
        <td><a href="#validate">对比函数</a> -- validate package</td>
    </tr>
    <tr>
        <td><a href="#dao">数据库</a> -- DAO</td>
    </tr>
    <tr>
        <td><a href="#history">历史版本</a> -- history</td>
    </tr>
</table>
    
<span id='welcome'></span>
##### 欢迎
   感谢您能进一步了解origin，虽然他并不是您所期望最好的选择，不过对于您做出决定，我们感到十分荣幸！
在大环境下，我们经历了足够多的嘲讽和质疑，经历了团队变故，一些人离开了，一些人放弃了，
2017年参与商业化项目中版权以及技术不扩散等合作协议导致我们失去初版origin全部控制权，但这些已经过去！
我们在新的设计原型的基础上，重新创立了origin，摒弃了原有的模仿和所谓的从众原则，并去除了快捷应用原则而设计的函数。
2020年9月15日起，origin ver 1.0正式上传完全替代原始origin，文件也将从1.0重新开始迭代，谢谢，我们将一如既往的努力下去！    

<span id='tree'></span>
##### 文件目录    
<table style="border:0;">
    <tr>
        <td>#</td>
        <td colspan="4">application</td>
        <td>应用主目录，该目录由系统初始加载时生成</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/application">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗common</td>
        <td colspan="2">功能函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗home</td>
        <td colspan="2">默认访问地址目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗classes</td>
        <td colspan="2">控制器目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗common</td>
        <td colspan="2">默认访问应用功能函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗template</td>
        <td colspan="2">应用视图模板目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>┗home</td>
        <td colspan="2">默认访问视图模板目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">common</td>
        <td>功能文件目录，该目录由系统初始加载时生成</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/common/config">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗config</td>
        <td colspan="2">系统配置文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗log</td>
        <td colspan="2">系统日志目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">origin</td>
        <td>origin framework功能封装目录</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/origin">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗library</td>
        <td colspan="2">实例函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗package</td>
        <td colspan="2">功能类封装目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗template</td>
        <td colspan="2">内核信息视图模板目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">resource</td>
        <td colspan="2">web文件资源目录，该目录由系统初始加载时生成</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗buffer</td>
        <td colspan="2">缓存文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗public</td>
        <td colspan="2">功能文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>┗font</td>
        <td colspan="2">字体目录，内核封装的验证码字体初始化位置</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>┗temp</td>
        <td colspan="2">自定义模板目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗upload</td>
        <td colspan="2">上传文件目录</td>
    </tr>
</table>

<span id='basic'></span>
##### 基础功能    

<span id='config'></span>
##### 基础配置    

<span id='iif'></span>
##### web标签    

<span id='validate'></span>
##### 对比函数    

<span id='dao'></span>
##### 数据库    

<span id='history'></span>
##### 历史版本    
