# CV-Site-Symfony-4
Personal CV Site made with Symfony 4 as a blog.



Built with Symfony, this site is structured to include several basic functionalities.


- Connection module :

Registering and connecting to a site are common functionalities that allow secure access to certain parts or resources of the site. This functionality also allows you to retrieve information that helps to build user loyalty (email address, etc.).


- Forgot your password?

Essential functionality in the login module to prevent users from multiplying accounts or abandoning the use of the site.


- Display of articles :

An article management system via a database, which allows the creation of indexes in which articles are listed, making the user experience more pleasant and the addition of new pages easier.

Mainly developed in Php and using MySql, this site also uses CSS functionality for the Sidenav and Navbar.


CVSite functionality: identification module in php with symfony and mysql
(registration, login, forgotten password, and user memorization cookie)

The aim of the CVSite is to highlight the different skills I have acquired around game development, 3D creation and web development.

For this, the CVSite itself has been developed using Symfony in order to benefit from the power of this framework in Php. I also used a MySQL database in order to store the different articles and thus increase the number of pages without having to recode the site in the future. The connection module is an example of an advanced feature that I am able to implement.

1-Twig :
categoryBase.html.twig


	{% if not app.user %}
      	<li class="nav-item">
              <a href="{{path('security_login')}}" class="nav-link">
                        Connexion
              </a>
      	<li class="nav-item">
              <a href="{{path('security_registration')}}" class="nav-link">
                        Inscription
              </a>
      </li>
	{% else %}
      <li class="nav-item">
              <a href="{{path('security_logout')}}" class="nav-link">
                        Déconnexion
              </a>
      </li>
      <li class="nav-item">
              <a>{{app.user.username}}</a>
      </li>
                {% endif %}            
                

Symfony uses Twig, a template engine that allows to easily generate the client-side display via a clean syntax allowing the reuse of basic templates. Thus categoryBase.html.twig is a basic template that is reused by other templates. Thus we save the rewriting of source calls in Javascript and CSS, the rewriting of a navbar, a sidebar ...

In twig you can also use procedural programming functions such as conditions and loops.

Here we will test the boolean variable app.user. Saved at session level, and therefore accessible everywhere in the project, it returns "true" when the user is logged in. In this case a "disconnect" link is displayed, otherwise a "connect" link and a "register" link.

Each of these links returns a "path", a Symfony tool. Indeed, Symfony uses a routing system to redirect to a Php function. Here, by clicking on the registration link, one is redirected to the 'registration' function that can be reached by the path named 'security_registration'.

2-Registration functionality :
SecurityController.php


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
				  	

This function uses three parameters: the request, an instance of EditManagerInterface and finally an object of "type" UserPasswordEncoderInterface.

$user :

First, a User "type" object is created, which will be used to store the new registrant's information in the database. The user class (see source code) has each of these properties linked to a field in the user table in the database via the annotation system.

![alt text](https://github.com/DavidLiger/CV-Site-Symfony-4/blob/master/public/img/article-15-img-1.png)


Here the ORM annotations explicitly define the name of the designated table column, the type of data accepted, etc...

$form :

![alt text](https://github.com/DavidLiger/CV-Site-Symfony-4/blob/master/public/img/article-15-img-2.png)

The function creates a form based on the template present in the RegistrationType class (see source code). This form will receive the information of the $user object just created. Its handleRequest method allows it to retrieve the information from the request.

-Submit the form :

$token :

When you click on the 'register' button and if the form is valid (if all the required fields are filled in correctly), then you start by creating a token with the bin2hex function that produces a string of 50 random characters and store it in the token field of the user table.

This token will be used to produce a unique html link in the email address of the message sent to the user in the case of a password renewal request. In this case, this token will be automatically replaced in order to prohibit its reuse and thus secure the procedure.

Password :

We then use the encodePassword method of our $encoder object to encode the user's password. Thus even the site administrator cannot know the passwords of his subscribers! Security above all!

$manager :

The EntityManagerInterface is a Doctrine class, the ORM (Object Relational Mapping) used by Symfony. In other words the $manager object makes our life easier by managing for us the relationship to the DB. Here, the persist() method registers the user object then the flush() method updates the DB.

Then, it returns to the 'security_login' route which forces us to connect once we have registered. Outside this condition, the registration() function displays(render()) the twig (html page) registration.html.twig.

This twig needs a 'form' parameter: the $form form, which has a createView() function to display it.

Twig :
registration.html.twig


	{% extends 'base.html.twig' %}

	{% block body %}
    
        <h1 class="h4 text-center py-4 bg-dark ">Inscription</h1>

    {{ form_start(form)}}

         {{ form_row(form.nom,{'label':"Nom",'attr':{'placeholder':"Entrez votre nom",'class':"form-control mb-4" }}) }}
         {{ form_row(form.prenom,{'label':"Prenom",'attr':{'placeholder':"Entrez votre prénom", 'class':"form-control mb-4"}}) }}
         {{ form_row(form.username,{'label':"Pseudo",'attr':{'placeholder':"Entrez votre pseudo", 'class':"form-control mb-4"}}) }}
         {{ form_row(form.email,{'label':"Adresse email",'attr':{'placeholder':"Entrez votre e-mail", 'class':"form-control mb-4"}}) }}
         {{ form_row(form.password,{'label':"Mot de passe",'attr':{'placeholder':"Entrez votre mot de passe", 'class':"form-control mb-4"}}) }}

        <button class="btn btn-dark my-4 " type="submit">M'inscrire</button>

    {{ form_end(form) }}
	{% endblock %}
                

As you can see, this twig is relatively short. It inherits the base .html.twig. Only the block body is redefined. In this block we use five form rows and we specify which form field receives the user input. A label and attributes can be specified as for any HTML input. Finally, a simple button is used to submit the form.

3-The login function :
SecurityController.php


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
                

If the registration was successful we are redirected via the route system to the login function.

$error :

We start by retrieving in a variable the last identification error. If it is empty we can log in, otherwise we will display a message from the twig.

$lastUserName :

For the comfort of the user, this variable makes it possible to display a previously used nickname. This avoids having to retype it.

![alt text](https://github.com/DavidLiger/CV-Site-Symfony-4/blob/master/public/img/article-15-img-3.png)

This.addFlash(‘connect’,’Bienvenue’) ;

Flashes" are predefined messages "inherited" from the mother twig and used in the twig. Here, if a user is logged in, for "connect" flashes, "success" (green) messages are displayed with the message (here: welcome) and the user's nickname.

Finally, still in login, we display the twig login.html.twig with the variables last_username and error.

As can be checked here and in the code below, a connection error causes an alert to be displayed.
login.html.twig


	{% extends 'base.html.twig' %}

	{% block body %}
  	<h1>Connexion</h1>
  	{% if error %}
            <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                Vos identifiants ne sont pas reconnus</div>

        {% endif %}
  
  	<form action="{{path('security_login')}}" method="post">
    		<div class="form-group">
      	<input placeholder="Pseudo..." required name="_username"
      type="text" class="form-control">
    	</div>
    <div class="form-group">
      <input placeholder="Mot de passe..." required name="_password"
      type="password" class="form-control">
    </div>
    <div>
      <input type="checkbox" id="remember_me" name="_remember_me" checked/>
      <label for="remember_me">Se souvenir de moi</label>
    </div>
    <div class="form-group">
      <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate')}}">
      <button type="submit" class="btn btn-success">Connexion !</button>
    </div>
  	</form>

  	<form action="{{ path('oubli') }}" method="post">
    <button type="submit" id="forgotten_mdp" name="forgotten_mdp" class="btn btn-dark" style="float: right" />Mot de passe oublié ?</button>
  	</form>
  
	{% endblock %}
                

The form consists of four main elements. One input for the username, another for the password. A checkbox to record your session and a submit button. The remember_me checkbox is directly linked to a cookie setting in the security.yaml. The hidden input '_csrf_token' is used to create a token that we can use to secure the connection. The forgotten password button will take you back to the 'forget' function.

-remember_me :

We simply use a cookie that allows us to keep the logged-in user logged in after the browser is closed.

  security.yaml


  	security:
    
        	firewalls:
            
                	remember_me:
                    	secret:   '%kernel.secret%'
                    	lifetime: 604800 # 1 week in seconds
                    	path:     /           
			                

Secret :

is defined by the environment variable defined in APP_SECRET. This value is used to encrypt the content of the cookie.

Lifetime :

is the duration of the cookie's conservation.

Path :

is the address from which the cookie is active, by default on the whole site.

CSRF :

As mentioned above, CSRF (Cross Site Request Forgery) secures user authentication and prevents attempts to attack users by having them submit personal data without their knowledge and on their behalf. To do this, the CSRF protection adds a hidden field, the input, seen above, which contains a random value (token) known only to the site and its subscriber, ensuring that it is the user and not someone else who submits data.
security.yaml


	security:
    		encoders:
        		App\Entity\User:
            		algorithm: bcrypt
 
    	firewalls:
      
        	main:
            
            	form_login:
                	login_path: security_login
                	check_path: security_login
                	csrf_token_generator: security.csrf.token_manager
                	default_target_path: home        
										                

access_control :

In order to perfect the authentication, the security.yaml will specify from which part of the site and what role is given to the person who is not yet logged in.
security.yaml

		                
	access_control:
        	- { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
		                       
		                
		              





