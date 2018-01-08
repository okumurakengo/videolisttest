<?php
class getBaseUrl extends \Slim\Views\TwigExtension
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'getBaseUrl';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getBaseUrl', array($this, 'getBaseUrl'))
        ];
    }

    public function getBaseUrl()
    {
        return 'http://'.$_SERVER["HTTP_HOST"].str_replace('/index.php','',$_SERVER["PHP_SELF"]);
    }
}

class getPage extends \Slim\Views\TwigExtension
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'getPage';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getPage', array($this, 'getPage'))
        ];
    }

    public function getPage()
    {
        return strpos($_SERVER["REQUEST_URI"],'add');
    }
}

class getFileName extends \Slim\Views\TwigExtension
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'getFileName';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getFileName', array($this, 'getFileName'))
        ];
    }

    public function getFileName($str)
    {
        return explode('.',$str);
    }
}
