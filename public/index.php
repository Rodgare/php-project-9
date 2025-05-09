<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use DI\Container;
use Hexlet\Code\Url;
use Hexlet\Code\UrlRepo;
use Hexlet\Code\UrlValidator;
use Hexlet\Code\UrlNormalize;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

$container->set(\PDO::class, function () {
    $iniFilePath = implode('/', [dirname(__DIR__), 'database.ini']);
    $params = parse_ini_file($iniFilePath);
    if ($params === false) {
        throw new \Exception("Error reading database configuration file");
    }
    $conStr = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
        $params['host'],
        $params['port'],
        $params['database'],
        $params['user'],
        $params['password']
    );

    $conn = new \PDO($conStr);
    $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $conn;
});


$initFilePath = implode('/', [dirname(__DIR__), 'database.sql']);
$initSql = file_get_contents($initFilePath);
$container->get(\PDO::class)->exec($initSql);

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($req, $res) {
    return $this->get('renderer')->render($res, 'index.phtml');
})->setName('urls.index');

$app->post('/urls', function ($req, $res) use ($router) {
    $urlRepo = $this->get(UrlRepo::class);
    $urlData = $req->getParsedBodyParam('url');
    $validator = new UrlValidator();
    $errors = $validator->validate($urlData);

    if (count($errors) === 0) {
        $normalize = new UrlNormalize();
        $urlData['name'] = $normalize->normalize($urlData['name']);
        $idIfNameExists = $urlRepo->isNameExists($urlData['name']);

        if ($idIfNameExists) {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
            return $res->withRedirect($router->urlFor('urls.show', ['id' => $idIfNameExists]));
        }

        $url = Url::fromArray([$urlData['name']]);
        $urlRepo->save($url);
        $id = $url->getId();
        $this->get('flash')->addMessage('success', 'Страница успешно добавлена');

        return $res->withRedirect($router->urlFor('urls.show', ['id' => $id]));
    }

    $params = [
        'urlData' => $urlData,
        'errors' => $errors
    ];

    return $this->get('renderer')->render($res->withStatus(422), 'index.phtml', $params);
});

$app->get('/urls', function ($req, $res) {
    $urlRepo = $this->get(UrlRepo::class);
    $urls = $urlRepo->getEntities();

    $params = ['urls' => $urls];

    return $this->get('renderer')->render($res, 'store.phtml', $params);
})->setName('urls.store');

$app->get('/urls/{id}', function ($req, $res, $args) {
    $urlRepo = $this->get(UrlRepo::class);
    $id = $args['id'];
    $url = $urlRepo->find($id);

    if (is_null($url)) {
        return $this->get('renderer')->render($res->withStatus(404), '404.phtml');
    }

    $flash = $this->get('flash')->getMessages();
    $params = ['url' => $url, 'flash' => $flash];

    return $this->get('renderer')->render($res, 'show.phtml', $params);
})->setName('urls.show');

$app->run();
