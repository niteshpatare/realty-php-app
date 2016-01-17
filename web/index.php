<?php

require('../vendor/autoload.php');
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints as Assert;


$app = new Silex\Application();
$app['debug'] = true;
$app['base_url'] = 'http://localhost/sainivaraslim/web';

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());


$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

// Our web handlers
$app['security.firewalls'] = array(
    'login' => array(
        'anonymous' => true,
        'pattern' => '^.*$',
        'http' => true,
        'form' => array(
            'login_path' => '/', 
            'check_path' => '/login_check',
        ),
        
    ),
);


$app->get('/', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig', array(
    //'error' => $app['security.last_error']($request),
      'error' => 'Please all fill the details',
  ));
});




$app['security.access_rules'] = array(
    array('^/', 'IS_AUTHENTICATED_ANONYMOUSLY')
);




$app->get('/enquireunack', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('enquireack.php');
});

$app->get('/hello/{name}', function($name) use($app) { 
    return 'Hello '.$app->escape($name); 
}); 

$app->post('/enquireack', function ($request) use($app) {
	
   echo "1";
 
});


$app->run();
