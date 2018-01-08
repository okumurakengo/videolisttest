<?php
use \Psr\Http\Message\ServerRequestInterface as Req;
use \Psr\Http\Message\ResponseInterface as Res;

ini_set('display_errors',1);

require_once __DIR__.'/myvendor/autoload.php';

$config = [
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
];

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/templates', [
        'cache' => __DIR__.'/cache',
        'debug' => true
    ]);
   
    $view->addExtension(new getBaseUrl());
    $view->addExtension(new getPage());
    $view->addExtension(new getFileName());

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};

$app->get('/', function (Req $req, Res $res) {
    (new DB())->createVideoList();
    return $res->withStatus(302)->withHeader('Location', './video');
});

$app->get('/video', function (Req $req, Res $res) {
    $dbVideo = new dbVideo;
    $getVideoList = $dbVideo->getVideoList();
    return $this->view->render($res,"video/index.html.twig",compact('getVideoList'));
});

$app->map(["GET","POST"],'/video/add', function (Req $req, Res $res, $args) {
    $dbVideo = new dbVideo;

    if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error'])) {

        // $_FILES['upfile']['error'] の値を確認
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK: // OK
                break;
            case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                throw new RuntimeException('ファイルが選択されていません', 400);
            case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
            case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                throw new RuntimeException('ファイルサイズが大きすぎます', 400);
            default:
                throw new RuntimeException('その他のエラーが発生しました', 500);
        }

        // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので
        // MIMEタイプを自前でチェックする
        // if (!$info = @getimagesize($_FILES['upfile']['tmp_name'])) {
        //     throw new RuntimeException('有効な画像ファイルを指定してください', 400);
        // }
        // if (!in_array($info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        //     throw new RuntimeException('未対応の画像形式です', 400);
        // }

        $file = $_SERVER['REQUEST_TIME'].".".pathinfo($_FILES['upfile']['name'], PATHINFO_EXTENSION);
        if(!rename($_FILES['upfile']['tmp_name'], __DIR__."/upload/$file")){
            throw new RuntimeException('アップロードに失敗しました', 500);
        }
        $dbVideo->addVideo($_FILES['upfile']);
        return $res->withStatus(302)->withHeader('Location', '../video');
    }

    header('Content-Type: application/xhtml+xml; charset=utf-8');
    return $this->view->render($res,"video/add.html.twig");
});

$app->get('/video/view/{id}', function (Req $req, Res $res, $args) {
    $dbVideo = new dbVideo;
    $getVideo = $dbVideo->getVideo($args['id']);
    return $this->view->render($res,"video/view.html.twig",compact('getVideo'));
});

$app->map(["GET","POST"],'/video/edit/{id}', function (Req $req, Res $res, $args) {
    $dbVideo = new dbVideo;
    $getVideo = $dbVideo->getVideo($args['id']);

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $videoname = $req->getParsedBody()['videoname'];
        $ext = explode('.',$getVideo['videoname'])[1];
        $dbVideo->editVideo($args['id'],$videoname,$ext);
        return $res->withStatus(302)->withHeader('Location', '../../video');
    }

    return $this->view->render($res,"video/edit.html.twig",compact('getVideo'));
});

$app->post('/video/del', function (Req $req, Res $res) {
    $dbVideo = new dbVideo;
    $dbVideo->delVideo($req->getParsedBody()['id']);
    return $res->withStatus(302)->withHeader('Location', '../video');
});

$app->run();

