<?php
# echo __FILE__ . PHP_EOL;
# return false;

# print_r($GLOBALS);

define('HTML_EOL', '<br>');
define('TXT_EOL', "\r\n");

class HTTP
{
    private static $funcArgs = array(
        'srvInfo' => array(),
    );

    public function __construct($srvInfo = [])
    {
        self::$funcArgs['srvInfo'] = $srvInfo = $srvInfo ? : $_SERVER;
        # print_r($srvInfo);
    }

    public function __destruct()
    {

    }
}

class Request extends HTTP
{
    public static $query = array(
        'basename' => null,
        'uri-scheme' => null,
    );

    public static $request = array(
        'uri' => null,
    );

    public static $path = array(
        'dirname' => null,
        'basename' => null,
        'extension' => null,
        'filename' => null,
    );

    public static $vars = array(
        'request' => array(
            'uri' => null,
            'uriDecode' => null,
        ),
        'urlInfo' => array(
            'scheme' => null,
            'path' => null,
            'host' => null,
            'query' => null,
        ),
        'content' => array(
            'filename' => 'null',
            'title' => 'null',
            'description' => 'null',
        ),
    );

    public function __construct($srvInfo = [])
    {
        $srvInfo = $srvInfo ? : $_SERVER;
        parent::__construct($srvInfo);

        self::$request['uri'] = $requestUri = preg_replace('/^\/+/', '', $srvInfo['REQUEST_URI']);
        self::$vars['request']['uriDecode'] = urldecode($requestUri);

        $urlParse = parse_url($requestUri);
        self::$vars['urlInfo'] = $urlInfo = array_merge(self::$vars['urlInfo'], $urlParse);
        $pathDecode = urldecode(self::$vars['urlInfo']['path']);
        $pathDecode = preg_replace('/[\+]+\/n[\+]+/', ' \n ', $pathDecode);
        self::$vars['urlInfo']['path'] = urlencode($pathDecode);
        $urlInfo = self::$vars['urlInfo'];


        $pathInfo = pathinfo($urlInfo['path']);
        self::$path = array_merge(self::$path, $pathInfo);

        $baseName = $pathInfo['basename'];
        self::$query['basename'] = $queryOnce = urldecode($baseName);

        // 修正文件名、标题和描述

        $cmd_stdin = preg_split('/\s+\/n\s+/', $pathDecode);
        $cmd_count = count($cmd_stdin);
        self::$vars['content']['filename'] = self::$vars['content']['title'] = self::$query['basename'];
        self::$vars['content']['description'] = self::$vars['request']['uriDecode'];
        if (1 < $cmd_count) {
             self::$vars['content']['filename'] = $cmd_stdin[0];
             self::$vars['content']['title'] = $cmd_stdin[1];
             self::$vars['content']['description'] = $cmd_stdin[2];
        }
        # print_r($cmd_stdin);exit;

        $flags = array(
            'urlCode' => null,
        );

        // self::$vars['content'] = array_merge(self::$vars['content'], array('filename' => self::$query['basename'], 'title' => self::$query['basename'], 'description' => self::$vars['request']['uriDecode']));


        // URL 编码
        if (preg_match_all('/(%[a-z0-9]{2})/i', $baseName, $matches)) {
            $flags['$flags'] = true;
        }
        // 协议
        if (preg_match('/^([a-z0-9]+):(.*)/i', $requestUri, $matches)) {
            # $urlInfo['scheme']
            self::$query['uri-scheme'] = $matches[1];
        }

        // 系统应用
        /*
        foreach ($pathRules['system_appliaction'] as $sysApp => $sysAppInfo) {

        }*/
        $sysAppDef = explode(',', SNSearch::$pathRules['system_appliaction']['']);
        foreach ($sysAppDef as $sysAppName) {
            SNSearch::$pathRules['system_appliaction'][$sysAppName] = array();
        }
        # SNSearch::$pathRules['system_appliaction'] = array_merge(SNSearch::$pathRules['system_appliaction'], $sysAppDef);
        unset(SNSearch::$pathRules['system_appliaction']['']);

        // URI 协议
        $sysAppDef = explode(',', SNSearch::$pathRules['uri-scheme']['']);
        foreach ($sysAppDef as $sysAppName) {
            SNSearch::$pathRules['uri-scheme'][$sysAppName] = array();
        }
        unset(SNSearch::$pathRules['uri-scheme']['']);
        foreach (SNSearch::$pathRules['uri-scheme'] as $uriSchm => $uriSchmInfo) {
            if (is_numeric($uriSchm)) {
                unset(SNSearch::$pathRules['uri-scheme'][$uriSchm]);
                foreach ($uriSchmInfo as $uriSchmName) {
                    SNSearch::$pathRules['uri-scheme'][$uriSchmName] = array();
                }
            }
        }

        // 扩展名
        $sysAppDef = explode(',', SNSearch::$pathRules['filename-extension']['']);
        foreach ($sysAppDef as $sysAppName) {
            SNSearch::$pathRules['filename-extension'][$sysAppName] = array();
        }
        unset(SNSearch::$pathRules['filename-extension']['']);
        foreach (SNSearch::$pathRules['filename-extension'] as $uriSchm => $uriSchmInfo) {
            if (is_numeric($uriSchm)) {
                unset(SNSearch::$pathRules['filename-extension'][$uriSchm]);
                foreach ($uriSchmInfo as $uriSchmName) {
                    SNSearch::$pathRules['filename-extension'][$uriSchmName] = array();
                }
            }
        }

        // 域名
        SNSearch::_resetConf('domain');

        $SNSearch = SNSearch::$pathRules;
        $query = self::$query;
        $request = self::$request;
        $path = self::$path;
        $vars = self::$vars;
        $var = print_r(get_defined_vars(), true);
        if (isset($_GET['debug_var'])) {
            echo "<textarea style='width:100%;height:100%;min-height:600px;'>$var</textarea>";
        }
    }



    public function __destruct()
    {

    }
}

class SNSearch
{
    private static $funcArgs = array(
        'pathname' => null,
    );

    public static $pathRules = array(
        'system_appliaction' => array(
            '' => 'video,image,application,audio,text',
            'index' => array('index', 'archive', 'search'), 
            'user' => array('login', '/^\d+$/', '/^[a-z0-9\-]+$/', '/^[a-z0-9\-_]+$/', '/[\s]+/'),
            'search' => array('index', 'video', 'music', 'Pictures', 'Documents', '/^download(|s)$/i', 'Desktop', '3D Objects'),
        ),
        'uri-scheme' => array(
            '' => 'http,https',
            0 => array('kuwo', 'kugou'), // windows 应用
        ),
        'filename-extension' => array(
            '' => 'aac,md',
        ),
        'domain' => array(
            '' => 'us,ru,la,nz,kr,jp,co',
        ),
        'keyword' => array(
            '' => array('/%[a-z0-9]+/i'),
        ),
    );

    public function __construct($srvInfo = [])
    {
        # print_r(get_defined_vars());
        $this->__init();
    }

    public function __destruct()
    {
        $this->__run();
    }

    public function __init()
    {
        global $_VAR;
        $log_filename = date('Y-m-d');
        self::$funcArgs['pathname'] = $_VAR[F]['drive_letter'] . "/env/tmp/php/log/db/$log_filename";
        self::$funcArgs['dbname'] = $_VAR[F]['drive_letter'] . "/env/tmp/php/log/db";
        self::$funcArgs['db_uri'] = $_VAR[F]['drive_letter'] . "/env/tmp/php/log/db/uri";
        self::$funcArgs['db_keyword'] = $_VAR[F]['drive_letter'] . "/env/tmp/php/log/db/keyword";
        
        $dir = is_dir(self::$funcArgs['dbname']) ? : mkdir(self::$funcArgs['dbname']);
        $dir = is_dir(self::$funcArgs['db_uri']) ? : mkdir(self::$funcArgs['db_uri']);
        $dir = is_dir(self::$funcArgs['db_keyword']) ? : mkdir(self::$funcArgs['db_keyword']);
        $dir = is_dir(self::$funcArgs['pathname']) ? : mkdir(self::$funcArgs['pathname']);

        $req = new Request();
        $data = array();

        
        # print_r([Request::$query['basename'], $sysApp]);
        # echo false !== array_search(Request::$query['basename'], $sysApp);exit;
        // 命令行
        if (Request::$vars['urlInfo']['path']) {
            $pathDecode = urldecode(Request::$vars['urlInfo']['path']);
            $cmd_stdin = preg_split('/\s+\/n\s+/', $pathDecode);
            $cmd_stdin = array_merge(array_values(Request::$vars['content']), $cmd_stdin);
            # $filename = $title = $description = '';
            list($filename, $title, $description) = $cmd_stdin;
            # print_r($cmd_stdin);
            # Request::$query['basename'] = $cmd_stdin[0];
            Request::$vars['content'] = array_merge(Request::$vars['content'], array('filename' => $filename, 'title' => $title, 'description' => $description));
        }
        
        // 系统应用
        if (Request::$query['basename']) {
            $sysApp = self::_textDb(null, 'system_appliaction', true) ? : [];
            # var_dump(Request::$query['basename'], $sysApp, static::$pathRules['system_appliaction']);
            # print_r($sysApp);
            # exit;
            if (array_key_exists(Request::$query['basename'], static::$pathRules['system_appliaction']) || false !== array_search(Request::$query['basename'], $sysApp)) {
                echo 'system application' . HTML_EOL;
                # file_put_contents("$pathname/system_appliaction.txt", Request::$query['basename'] . PHP_EOL, FILE_APPEND);
                self::_textDb(Request::$query['basename'] . TXT_EOL, 'system_appliaction');
                return true;
            }

            // 记录未知的
            # self::_textDb(Request::$request['uri'] . TXT_EOL, '0_system_appliaction');
            # echo 'unknow' . HTML_EOL;
            # return true;
        }

        // URI 协议
        if (Request::$query['uri-scheme']) {
            $sysApp = self::_textDb(null, 'uri-scheme', true);
            echo 'uri-scheme' . HTML_EOL;
            echo Request::$request['uri'] . HTML_EOL;

            // 检测已经定义的
            if (array_key_exists(Request::$query['uri-scheme'], self::$pathRules['uri-scheme']) || false !== array_search(Request::$query['uri-scheme'], $sysApp)) {
                
                self::_textDb(Request::$query['uri-scheme'] . TXT_EOL, 'uri-scheme');
                return true;
            }

            // 记录未知的
            self::_textDb(Request::$request['uri'] . TXT_EOL, '0_uri-scheme');
            echo 'unknow' . HTML_EOL;
            return true;
        }

        // 扩展名和域名
        if (null !== Request::$path['extension']) {
            if ('.' == Request::$path['dirname']) {
                echo 'root path' . HTML_EOL;
            }

            if (array_key_exists(Request::$path['extension'], self::$pathRules['domain'])) {
                echo 'domain' . HTML_EOL;
                self::_textDb(Request::$path['extension'] . TXT_EOL, 'domain');

            } elseif (array_key_exists(Request::$path['extension'], self::$pathRules['filename-extension'])) {
                echo 'filename-extension' . HTML_EOL;
                self::_textDb(Request::$path['extension'] . TXT_EOL, 'filename-extension');
            }
            echo Request::$path['extension'];

            return true;
        }

        // 关键词
        $sysApp = self::_textDb(null, 'keyword', true);
        $md5 = md5(Request::$query['basename']);
        $filename = self::$funcArgs['dbname'] . "/keyword/$md5.txt";
        if (false !== array_search(Request::$query['basename'], $sysApp ? : [])) {
            # echo Request::$query['basename'] . HTML_EOL;         
            if (file_exists($filename)) {
                echo file_get_contents($filename);
            }
            exit;
        } else {
            //print_r(Request::$vars['content']);
            //exit;
            Request::$vars['content'] = array_merge(
                Request::$vars['content'], 
                array(
                    'filename' => Request::$vars['content']['filename'] ? : Request::$query['basename'], 
                    'title' => Request::$vars['content']['title'] ? : '$title', 
                    'description' => Request::$vars['content']['description'] ? : '$description',
                ),
            );
            # print_r(Request::$vars['content']);
            self::_textDb(Request::$query['basename'] . TXT_EOL, 'keyword');
            self::_textDb($md5 . ' ' . Request::$query['basename'] . TXT_EOL, '0_keyword');
            if (isset($_GET['touch'])) {
                file_put_contents($filename, implode(HTML_EOL, Request::$vars['content']) . HTML_EOL, FILE_APPEND);
            }
        }

        // URI
        $keyWord = self::_textDb(null, 'uri', true);
        $md5 = md5(Request::$vars['request']['uriDecode']);
        $filename = self::$funcArgs['dbname'] . "/uri/$md5.txt";
        if (false !== array_search(Request::$vars['request']['uriDecode'], $keyWord ? : [])) {
            echo Request::$vars['request']['uriDecode'];
            if (file_exists($filename)) {
                echo file_get_contents($filename);
            }
        } else {
            self::_textDb(Request::$vars['request']['uriDecode'] . TXT_EOL, 'uri');
            if (isset($_GET['touch'])) {
                file_put_contents($filename, Request::$vars['request']['uriDecode'] . HTML_EOL, FILE_APPEND);
            }     
        }
    }

    public function __run()
    {
        echo __FILE__;
    }

    public static function _resetConf($section = null)
    {
        $sysAppDef = explode(',', self::$pathRules[$section]['']);
        foreach ($sysAppDef as $sysAppName) {
            self::$pathRules[$section][$sysAppName] = array();
        }
        unset(self::$pathRules[$section]['']);
        foreach (self::$pathRules[$section] as $uriSchm => $uriSchmInfo) {
            if (is_numeric($uriSchm)) {
                unset(self::$pathRules[$section][$uriSchm]);
                foreach ($uriSchmInfo as $uriSchmName) {
                    self::$pathRules[$section][$uriSchmName] = array();
                }
            }
        }
        return $sysAppDef;
    }

    public static function _textDb($line = null, $filename = null, $return = null)
    {
        $file = self::$funcArgs['pathname'] . "/$filename.txt";
        if ($return && file_exists($file)) {
            $string = file_get_contents($file);
            return $array = explode("\r\n", trim($string));
            print_r($array);exit;
        }
        file_put_contents($file, $line, FILE_APPEND);
    }

    public function __call($name, $arguments)
    {
        print_r(get_defined_vars());
    }

    public static function _action()
    {
        print_r(get_defined_vars());
        # echo 'Hello';
        return true;
    }

    public static function index()
    {

    }
}

global $_VAR;
$_VAR[':db_json'] = null;

$snsrch = new SNSearch(__FILE__);
# $req = new Request();
$_VAR[__FILE__]['Request'] = get_class_vars(Request::class);
# include 'template.html';
# return true;
return $snsrch->_action();
echo 'Hello';
