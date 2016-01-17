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
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return sprintf('%s/%s', trim($app['request']->getBasePath()), ltrim($asset, '/'));
    }));
    return $twig;
}));

$app->register(new Silex\Provider\SecurityServiceProvider());


$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new FormServiceProvider());


$app['swiftmailer.options'] = array(
	'host' => 'smtp.gmail.com',
	'port' => 465,
	'username' => 'nitesh.patare27@gmail.com',
	'password' => 'premiumgold7g',
	'encryption' => 'tls',
);

// Our web handlers
$app['security.firewalls'] = array(
    'login' => array(
        'anonymous' => true,
        'pattern' => '^.*$',
        'http' => true,
        'form' => array(
            'login_path' => '/contact', 
        ),
        
    ),
);


$app['security.access_rules'] = array(
    array('^/', 'IS_AUTHENTICATED_ANONYMOUSLY')
);

$app->before(function(Request $request) use ($app){
    $app['twig']->addGlobal('active', $request->get("_route"));
});

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('pages/index.twig', array(
    //'error' => $app['security.last_error']($request),
      'error' => 'Contact us using the form below and we\'ll get back in touch with you',
  ));
})->bind('home');

$app->match('/contact', function(Request $request) use ($app) {
    $sent = false;
    $default = array(
        'name' => '',
        'email' => '',
        'message' => '',
        'verify' => '',
    );
    $form = $app['form.factory']->createBuilder('form',$default)
        ->add('name', 'text', array(
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
			'attr' => array('class' => 'form-control', 'placeholder' => 'Your Name', 'error' => 'Name should be greater than 3 characters')
		))
		->add('email', 'email', array(
			'constraints' => new Assert\Email(),
			'attr' => array('class' => 'form-control', 'placeholder' => 'Your@email.com', 'error' => 'Please verify your email. Eg.Your@email.com ')
		))
		->add('message', 'textarea', array(
			'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 20))),
			'attr' => array('class' => 'form-control', 'placeholder' => 'Enter Your Message', 'error' => 'Please enter your query here.')
		))
        ->add('verify', 'text', array(
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 1))),
			'attr' => array('class' => 'form-control', 'placeholder' => '2 + 7 = ?', 'errorCapcha' => 'Please calculate the addition of capcha and validate you are a human.')            
		))
		->add('Enquire Now', 'submit', array(
			'attr' => array('class' => 'btn btn-default btn-primary')
		))
		->getForm();
 
	   $form->handleRequest($request);
    
        if($form->isValid()) {
            $data = $form->getData();    
            $exit = false;

            if(!$exit){
                if($data["capcha"] == 9){

                        $message = \Swift_Message::newInstance()
                        ->setSubject('Sai Prasar Nivara Feedback11')
                        ->setFrom(array($data['email'] => $data['name']))
                        ->setTo(array('nitesh.patare27@gmail.com'))
                        ->setBody($data['message']);
                        $app['mailer']->send($message);                
                        $sent = true;  
                    


$app['monolog']->addDebug('logging masg1.----'+$data['message']);



mail('nitesh.patare27@gmail.com', '[YourSite] Feedback', $message);

$app['monolog']->addDebug('logging masg2.----'); 
                }
                else{
                    //do something
                    $exit = true;
                    $sent = false;
                }
            }
        }

        return $app['twig']->render('pages/contact.twig', array('form' => $form->createView(), 'sent' => $sent));
    })->bind('contact');

$app->run();
