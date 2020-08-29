# CV-Site-Symfony-4
Personal CV Site made with Symfony 4 as a blog.



Construit avec Symfony, ce site est structuré afin de comporter plusieurs fonctionnalités de base.


- Module de connexion :

L’inscription et la connexion à un site sont des fonctionnalités courantes qui permettent de sécuriser l’accès à certaines parties ou ressources du site. Cette fonctionnalité permet également de récupérer des informations qui permettent de fidéliser les utilisateurs (adresse mail…)


- Mot de passe oublié :

Fonctionnalité indispensable au module de connexion afin d’éviter que les utilisateurs multiplient les comptes ou abandonne l’usage du site.


- Affichage d’articles :

Un système de gestion d’article via une base de donnée, ce qui permet de réaliser des index dans lesquels les articles sont listés, rendant l’expérience utilisateur plus agréable et l’ajout de nouvelles pages plus facile.

Principalement développé en Php et utilisant MySql, ce site utilise également des fonctionnalités CSS pour la Sidenav et la Navbar.


Fonctionnalité du site CVSite : module d'identification en php avec symfony et mysql
(inscription, connection, mot de passe oublié, et cookie de mémorisation de l'utilisateur)

Le but du CVSite est de mettre en lumière les différentes compétences que j’ai acquis autour du développement de jeu, de la création 3D et du développement web.

Pour cela, le CVSite lui-même a été développé à l’aide de Symfony afin de bénéficier de la puissance de ce framework en Php. J’ai également utilisé une base de donnée MySQL afin de stocker les différents articles et ainsi augmenter le nombre de pages sans avoir à recoder le site à l’avenir. Le module de connexion est un exemple de fonctionnalité avancé que je suis en mesure d’implémenter.

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
                

Symfony utilise Twig, un moteur de template qui permet de générer facilement l’affichage coté client via une syntaxe propre autorisant la réutilisation de modèles de base. Ainsi categoryBase.html.twig est un modèle de base qui est réutilisé par les autres templates. Ainsi on économise la réécriture des appels de source en Javascript et CSS, la réécriture d’une navbar, d’une sidebar…

Dans twig on peut également utiliser des fonctions de programmation procédurales comme les conditions et les boucles.

Ici on va tester la variable booléenne app.user. Enregistré au niveau de la session, et donc accessible partout dans le projet elle renvoie « true » lorsque l’utilisateur est connecté. Dans ce cas on affiche un lien « déconnexion », dans le cas contraire un lien « connexion » et un lien « inscription ».

Chacun de ces liens renvoie vers un « path », un outil de Symfony. En effet, Symfony utilise un système de routing pour rediriger vers une fonction Php. Ici, en cliquant sur le lien inscription, on est renvoyé vers la fonction ‘registration’ atteignable par la route (path) nommé ‘security_registration’.

2-La fonction inscription :
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
				  	

Cette fonction utilise trois paramètres : la requête, une instance de EditManagerInterface et enfin un objet de « type » UserPasswordEncoderInterface.

$user :

On créé d’abord un objet de « type » User qui servira à stocker en BDD les infos du nouvel inscrit. La classe user (voir code-source) a chacune de ces propriétés reliée à un champ de la table user dans la BDD via le système d’annotation.


Ici les annotations ORM définisse explicitement le nom de la colonne de la table désignée, le type de données acceptés...etc..

$form :

La fonction créé un formulaire sur e modèle présent dans la classe RegistrationType (voir code-source). Ce formulaire recevra les informations de l’objet $user qu’on vient de créer. Sa méthode handleRequest lui permet de récupérer les informations depuis la requête.

-Soumettre le formulaire :

$token :

Lorsqu’on clique sur le bouton ‘s’inscrire ‘ et si le formulaire est valide (si tous les champs requis sont remplis correctement), alors on commence par créer un token avec la fonction bin2hex qui produit une chaîne de 50 caractères aléatoires et on la stocke dans le champ token de la table user.

Ce token sera utilisé pour produire un lien html unique dans l’adresse mail du message envoyé à l’utilisateur dans le cas d’une demande de renouvellement de mot de passe. Dans ce cas, ce token sera automatiquement remplacé afin de proscrire sa réutilisation et ainsi sécuriser la procédure.

Password :

On utilise ensuite la méthode encodePassword de nôtre objet $encoder afin de coder le mot de passe de l’utilisateur. Ainsi même l’administrateur du site ne peut connaître les mots de passe de ses abonnés ! Sécurité avant tout !

$manager :

L’EntityManagerInterface est une classe de Doctrine, l’ORM (Object Relationnal Mapping) utilisé par Symfony. En d’autres termes l’objet $manager nous facilite la vie en gérant pour nous la relation à la BDD. Ici, la méthode persist() enregistre l’objet user puis la méthode flush() remet à jour la BDD.

Ensuite, elle renvoie vers la route ‘security_login’ qui nous oblige à nous connecter une fois inscrit. En-dehors de cette condition, la fonction registration() affiche(render()) le twig (page html) registration.html.twig.

Ce twig a besoin d’un paramètre le ‘form’ : le formulaire $form, qui possède une fonction createView() permettant de l’afficher.

Le twig :
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
                

Comme on le voit, ce twig est relativement court. Il hérite du base.html.twig. On ne redéfini que le block body. Dans ce bloc on utilise cinq rangée de formulaire et on précise quel champ du formulaire reçoit l’entrée de l’utilisateur. On peut préciser un label et des attributs comme pour n’importe quel input HTML. Enfin, un simple bouton permet de soumettre ce formulaire.

3-La fonction connexion :
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
                

Si l’inscription s’est bien passé nous voilà redirigé, via le système de route vers la fonction login.

$error :

On commence par récupérer dans une variable la dernière erreur d’identification. Si elle est vide on peut se connecter sinon on affichera un message depuis le twig.

$lastUserName :

Pour le confort de l’utilisateur, cette variable permet d’afficher un pseudo précédemment utilisé. Cela évite d’avoir à le retaper.

This.addFlash(‘connect’,’Bienvenue’) ;

Les « flashs » sont des messages prédéfinis « hérités » du twig-mère et utilisés dans le twig. Ici, à condition d’avoir un utilisateur connecté, pour les flashs de type « connect » on affiche des messages de type « success » (vert) avec le message (ici : bienvenue) et le pseudo de l’utilisateur.

Enfin, toujours dans login, on affiche le twig login.html.twig avec les variables last_username et error.

Comme on peut le vérifier ici et dans le code ci-dessous, une erreur de connexion provoque l’affichage d’un alert.
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
                

Le formulaire se compose de quatre éléments principaux. Un input pour le pseudo, un autre pour le mot de passe. Une case à cocher pour enregistrer sa session et un bouton submit. Le checkbox remember_me est directement lié à un paramétrage de cookie dans le security.yaml. L’input caché ‘_csrf_token’ sert à créer un token que nous pourrons utiliser pour sécuriser la connexion. Le bouton mot de passe oublié renvoie vers la fonction ‘oubli’.

-remember_me :

On utilise simplement un cookie qui nous permet de garder ouverte la session de l’utilisateur connecté après la fermeture du navigateur.

  security.yaml


  	security:
    
        	firewalls:
            
                	remember_me:
                    	secret:   '%kernel.secret%'
                    	lifetime: 604800 # 1 week in seconds
                    	path:     /           
			                

Secret :

est définie par la variable d’environnement défini dans APP_SECRET. Cette valeur sert à crypter le contenu du cookie.

Lifetime :

est la durée de conservation du cookie.

Path :

est l’adresse à partir de laquelle le cookie est actif, pardéfaut sur l ‘ensemble du site.

CSRF :

Comme nous l’avons plus haut le CSRF (Cross Site Request Forgery) sécurise l’authentification des utilisateurs et prévient des tentatives d’attaque visant à faire soumettre aux utilisateurs des données personnelles sans qu’ils le sachent et à leur place. Pour ce faire, la protection CSRF ajoute un champ caché, l’input, vu précédemment, qui contient une valeur aléatoire (token) connu uniquement par le site et son abonné, cela garanti que c’est bien l’utilisateur et non quelqu’un d’autre qui soumet des données.
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

Afin de parfaire l’authentification, on précisera dans le security.yaml depuis quelle partie du site et quel rôle on donne à celui qui n’est pas encore connecté.
security.yaml

		                
	access_control:
        	- { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
		                       
		                
		              





