<?php


use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ .'/vendor/autoload.php';

$app = new Silex\Application;

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider, [
    'twig.path' => __DIR__ . '/views'    
]);


$app->register(new Silex\Provider\DoctrineServiceProvider, [
    
  'db.options' => [
        'driver'    => 'pdo_mysql',
        'host'      => '142.4.12.88',
        'dbname'    => 'aled_imageUpload',
        'user'      => 'aled_imgload',
        'password'  => 'imagE@2017',
        'charset'   => 'utf8',
    ] 
]);

$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

$app->register(new AI\Providers\UploadcareProvider);

$app->get ('/', function () use ($app){
    
    $images = $app['db']->prepare("SELECT * FROM randomImage");
    $images->execute();
    $images = $images->fetchAll(\PDO::FETCH_CLASS, \AI\Models\Image::class);

    return $app['twig']->render('home.twig', [
        'images' => $images,    
    ]);
    
})->bind('home');


$app->post('/upload', function (Request $request) use ($app){
    if ($request->get('file_id')=== '') {
        return $app->redirect($app['url_generator']->generate('home'));
    }
    
    $file = $app['uploadcare']->getFile($request->get('file_id'));
    
    $store = $app['db']->prepare("
        INSERT INTO randomImage (hash, url, created_at)
        VALUES (:hash, :url, NOW())
    ");
    
    $store->execute([
        'hash' => bin2hex(rand()),
        'url'  => $file->getUrl(),
        
    ]);
    
    return $app->redirect($app['url_generator']->generate('home'));
    
})->bind('image.upload');


$app->get('/image/{hash}', function (Request $request) use ($app) {
    
    $image = $app['db']->prepare("
        SELECT url FROM randomImage WHERE hash = :hash
    ");
    
    $image->execute([
        'hash' => $request->get('hash'),    
    ]);
    
    $image->setFetchMode(\PDO::FETCH_CLASS, \AI\Models\Image::class);
    
    $image = $image->fetch();
    
    return $app['twig']->render('show.twig', [
        'image' => $image,
    ]);
})->bind('image.show');


$app->run();



//var_dump($app);

