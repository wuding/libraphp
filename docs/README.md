# LibraPhp API 文档



## class HTTP







## class Request



### Propertites

#### $vars

用一个变量名来存储 request，urlInfo，path，query，content，flags



### Methods

#### __construct($srvInfo = [])

输入服务器变量

构建请求、地址、路径、查询

修正文件名、标题和描述

URL 编码、协议

系统应用、URI 协议、扩展名、域名



#### request($srvInfo = array())

输入服务器变量

修剪左边的斜杠

URL 解码



#### urlInfo()

解析、合并 URL 信息

解码路径，修正换行符号 \n

编码路径



#### pathInfo()

分析、合并路径信息



#### queryInfo()

URL 解码带有扩展名的基本完整文件名



#### contentInfo($pathDecode)

分隔换行符

赋值文件名、标题和描述







## class SNSearch



### Properties

#### $funcArgs

函数参数 pathname，dbname，db_uri，db_keyword



#### $pathRules

路径规则 system_application，uri-scheme，filename-extension，domain，keyword



### Methods

#### __construct()

转到执行初始化



#### __init()

检测并创建日志目录

新建请求对象

命令行、系统应用、URI 协议、扩展名和域名、关键词、URI



#### _cli()

解码地址信息的路径，分隔换行符

合并文件名、标题和描述



#### _sysApplication()

检测（带扩展名的）文件名是否是系统应用（模块）



#### _uriScheme()

检测已经定义的、记录未知的



#### _dot()

区别是目录、扩展名还是域名



#### _keyword()

查找基本名称是否存在，检测到则输出

没有则写入索引，手动触发写入数据



#### _uri()

同上，区别在于完整



#### _textDb($line = null, $filename = null, $return = null)

文本数据读写



#### _resetConf($section = 'system_application')

字符串分隔为数组键名

提取数字项为数组键名

