<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/16
 * Time: 18:00
 */

namespace Apply\Home\Controller;

use Origin\Controller;

class Index extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->param('title','Origin架构开发版');
        $Origin = array('title' => 'Origin', 'version'=> 'Ver.0.1');
        $this->param('o', $Origin);
    }

    function index()
    {
        $welcomes = array(
            '0'=>array('statement' => '你好！欢迎使用Origin框架'),
            '1'=>array('statement' => 'Hello! Welcome to use Origin framework'),
            '2'=>array('statement' => 'こんにちは！使用を歓迎しOriginフレーム'),
            '3'=>array('statement' => '안녕하세요.오신 것을 환영합니다 Origin 틀'),
            '4'=>array('statement' => 'Hallo!Willkommen in origin.'),
            '5'=>array('statement' => 'hej!velkommen til oprindelse ramme'),
            '6'=>array('statement' => ' مرحبا!  مرحبا بكم في  الأصل  في إطار '),
            '7'=>array('statement' => 'Olá!BEM - vindo Ao Quadro de Origem'),
            '8'=>array('statement' => 'Xin chào!Chào mừng Origin sử dụng khung'),
            '9'=>array('statement' => 'szia!üdvözlöm Origin keret alkalmazása'),
        );
        $this->param('wel', $welcomes);
        $this->param('author', 'ShenQiwei');
        $this->param('time', '2017/03/31');
        $this->view();
    }

    function history()
    {
        $this->view();
    }
}