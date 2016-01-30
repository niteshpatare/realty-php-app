<?php 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Silex\Provider\FormServiceProvider;


use Symfony\Component\Validator\Constraints as Assert;


$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging home page.');
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
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 1, 'max' => 1)), new Assert\EqualTo(array('value' => 9)) ),            
			'attr' => array('class' => 'form-control', 'placeholder' => '2 + 7 = ?')            
		))
		->add('Enquire Now', 'submit', array(
			'attr' => array('class' => 'btn btn-default btn-primary wow animated swing')
		))
		->getForm();
 
	   $form->handleRequest($request);

       
            if($form->isValid()) {
                $data = $form->getData();    
                $exit = false;

                $name = strip_tags($data['name']);
                $subject = $app['mailSubject'].$name;
                $mailfrom =  strip_tags($data['email']);
                $emailTo = array($app['emailFrom']);
                $messagebody = strip_tags($data['message']);
                $verifyKey = strip_tags($data["verify"]);
    
                if(!$exit){
                    if($verifyKey == 9){

                            $message = \Swift_Message::newInstance()
                            ->setSubject($subject)
                            ->setFrom($mailfrom)
                            ->setTo($emailTo)
                            ->setBody($app['twig']->render('email.twig',   // email template
                                array('name'      => $name,
                                      'mailfrom'  => $mailfrom,
                                      'message'   => $messagebody,
                                )),'text/html');
                            //->setBody($messagebody);
                        
                            $app['mailer']->send($message);                
                            $sent = true;  
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

    $app->error(function (\Exception $e, $code) use ($app) {
        if ($app['debug']) {
            return;
        }
        switch ($code) {
            case 404:
                $message = 'The requested page could not be found.';
                break;
            default:
                $message = 'We are sorry, but something went terribly wrong.';
        }
        return new Response($message, $code);
    });