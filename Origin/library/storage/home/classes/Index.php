<?php
/**
 * @context （Default Visit） Application class file
 */
namespace Application\Home\Classes;

use Origin\Package\Unit;

class Index extends Unit
{
    function __construct()
    {
        parent::__construct();
        $this->param('title','Origin架构开发版');
        $Origin = array('title' => 'Origin', 'version' => 'Ver.1.0');
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
        $this->param('welcome', $welcomes);
        $this->param('author', 'ShenQiwei');
        $this->param('time', '2019/11/23');
        $this->view();
    }
}