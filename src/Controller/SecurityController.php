<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\EmailResetType;
use App\Form\ReinitMdpType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManager;

class SecurityController extends AbstractController
{

	/**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager,
	UserPasswordEncoderInterface $encoder){
		$user = new User();
		
        $form = $this->createForm(RegistrationType::class, $user);
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			$token = bin2hex(random_bytes(50));
            $user->setToken($token);
			$hash=$encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($hash);
			$this->addFlash('success', 'Inscription réussie !');
			$manager->persist($user);
			$manager->flush();
			
			return $this->redirectToRoute('security_login');
		}
		
		return $this->render('security/registration.html.twig',[
			'form'=>$form->createView()
		]);
    }
	
	/**
	* @Route("/connexion", name="security_login")
	*/
	public function login(AuthenticationUtils $authenticationUtils){
		// get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();
	    $this->addFlash('connect', 'Bienvenue ');
	    return $this->render('security/login.html.twig', [
	        'last_username' => $lastUsername,
	        'error'         => $error,
	    ]);
	}
	
	/**
	* @Route("/deconnexion", name="security_logout")
	*/
	public function logout(){
	}

	/**
     * Permit to reinitialize user's password.
     * @Route("/oubli", name="oubli")
     */
    public function oubli( Request $request, \Swift_Mailer $mailer, UserRepository $repo, EntityManagerInterface $em){

    	//Access uniquement à l'utilisateur anonyme
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ||
              $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')  ) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette page");
            return $this->redirect($this->generateUrl('home'));
        }

        $entityManager = $this->getDoctrine()->getManager();

        //Instancier formulaire de reinitialisation (EmailResetType)
        $emailValue = ['email' => ''];
        $form = $this->createForm(ChangePasswordType::class, $emailValue);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
        	$email = $form->get('email')->getData();
            $user = $repo
                    ->loadUserByUsername($email);
            if($user != null){

            	$dateRenew = new \DateTime();
                //Ajoute 10h de validité
                $dateRenew->modify('+ 10 hour');
                
                $user->setDateRenewPassword($dateRenew);

                $em->persist($user);
                $em->flush();

                // Create a message
                $message = (new \Swift_Message('[David Liger Website] Réinitialisation de votre mot de passe .'))
                    ->setFrom('dumbocracy.games@gmail.com')
                    ->setTo($form["email"]->getData())
                    ->setBody(
                        $this->renderView(
                            'security/mailChangePassword.html.twig',[
                                'user' => $user
                            ]
                        ),
                        'text/html'
                    );

                // Send the message

                $mailer->send($message);

                $this->addFlash('success', 'Un lien vous permettant de réinitialiser votre mot de passe a été envoyé vers votre boite mail !');
                //$mailer->disconnect();

                return $this->render('security/oubli.html.twig', ["form" => $form->createView()]);
            }else{
            	$this->addFlash('failure', 'Votre email n\'apparait pas dans notre base de donnée !');
            }
        }
        //Affichage a la suite du reset :
        return $this->render('security/oubli.html.twig', ["form" => $form->createView()]);
    }

    /**
     * Nous sommes amenés sur cette page de reinitialisation du mot de passe suite à clic sur lien dans mail de reinitialisation
     * @Route("/newpassword/{token}", name="security_change_password")
     */
    public function newMdp($token, UserRepository $userRepo, EntityManagerInterface $objectManager , Request $request , UserPasswordEncoderInterface $encoder){

    	//Access uniquement à l'utilisateur anonyme
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ||
              $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')  ) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette page");
            return $this->redirect($this->generateUrl('home'));
        }

        $user = $userRepo->loadTimeUserByToken($token);

        if($user == null){
            return $this->render('/security/error_token.html.twig');
        }else{
            //Garde en mémoire l'utilisateur
            $request->getSession()->set('user_temp', $user);
            
            $form = $this->createForm(ReinitMdpType::class, $user,
                    [ 'action' => $this->generateUrl('security_form_post_password'),]);
            
            return $this->render('/security/formChangePassword.html.twig',
            [
                'form'   => $form->createView()
            ]);
           
        }
    }

    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     * @Route("/password/change", name="security_form_post_password")
     */
    public function valideAction(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        
        //Access uniquement à l'utilisateur anonyme
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ||
              $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')  ) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette page");
            return $this->redirect($this->generateUrl('home'));
        }
        
        //$em = $this->getDoctrine()->getManager();
        $user = null;
        if($request->getSession()->has('user_temp')){
            $user = $request->getSession()->get('user_temp');
            //Reassocie 'utilisateur avec Doctrine
            //Car vient de la session
            $user = $em->find(User::class, $user);
        }
        
        if(!is_object($user)){
            throw $this->createNotFoundException();  
        }
        
        $form = $this->createForm(ReinitMdpType::class, $user,
                    [ 'action' => $this->generateUrl('security_form_post_password'),]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $token = bin2hex(random_bytes(50));
            $user->setToken($token);
            $user->setDateRenewPassword(null);
            
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            //Update
            $em->persist($user);
            $em->flush();

            $this->addFlash("success","Votre mot de passe a été modifié");
            
            return $this->redirectToRoute("security_login");
            
        }
            
        return $this->render('/security/formChangePassword.html.twig',
        [
            'form'   => $form->createView()
        ]);
    }
}
