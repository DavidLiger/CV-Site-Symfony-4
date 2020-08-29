<?php
namespace App\Service;

use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class MailerService {
    
    private $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * 
     * @param string $sujet
     * @param string $content
     * @param string $to
     * @param string $from
     */
    public function sendmail(string $sujet,string  $content,string  $to,string  $from){
        
        if($from == "unknown"){
           $from = $this->container->getParameter('admin_email');
        }
        
        $mailer = $this->container->get('swiftmailer.mailer');

        $message = (new Swift_Message($sujet))
            ->setFrom($from)
            ->setTo( $to)
            ->setBody($content,'text/html')
        ;

        $mailer->send($message);
    }
}
