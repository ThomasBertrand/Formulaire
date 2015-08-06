<?php

namespace ME\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use ME\PlatformBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
  public function connexionAction(Request $request)
  {
	$user = new User();
	$formBuilder = $this->get('form.factory')->createBuilder('form', $user);
	$formBuilder
		->add('userName', 'text')
		->add('password', 'password')
		->add('Connexion', 'submit')
	;
	$form = $formBuilder->getForm();
	if ($form->handleRequest($request)->isValid())
	{
		$repo = $this->getDoctrine()->getManager()
			->getRepository('MEPlatformBundle:User');
		$userGet = $repo->findOneBy(array('userName' => $user->getUserName()));
		if (!$userGet)
			return $this->render('MEPlatformBundle:Default:connexion.html.twig',
				array('form' => $form->createView(),
					'message' => 'Utilisateur inconnu',
					'accueil_url' => $this->generateURL('me_platform_accueil')));
		if ($user->getPassword() != $userGet->getPassword())
			return $this->render('MEPlatformBundle:Default:connexion.html.twig',
				array('form' => $form->createView(),
						'message' => 'Mauvais mot de passe',
						'accueil_url' => $this->generateURL('me_platform_accueil')));
		$session = $request->getSession();
		$session->set("user", $userGet);
		return new RedirectResponse($this->generateURL('me_platform_accueil'));
	}
    return $this->render('MEPlatformBundle:Default:connexion.html.twig',
		array('form' => $form->createView(),
				'message' => '',
				'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
  
  public function deconnexionAction(Request $request)
  {
	$session = $request->getSession();
	$session->set("user", NULL);
    return new RedirectResponse($this->generateURL('me_platform_accueil'));
  }
}