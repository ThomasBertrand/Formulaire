<?php

namespace ME\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ME\PlatformBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class CarnetController extends Controller
{
  public function showCarnetAction(Request $request)
  {
	$user = $request->getSession()->get('user');
	if (!$user)
		return new RedirectResponse($this->generateURL('me_platform_inscription'));
	$repo = $this->getDoctrine()->getManager()
			->getRepository('MEPlatformBundle:User');
	$userGet = $repo->findOneBy(array('userName' => $user->getUserName()));
	return $this->render('MEPlatformBundle:Default:carnet.html.twig',
		array('list' => $userGet->getContact(),
				'profil_url' => $this->generateURL('me_platform_viewProfil', array("user_name" => "USER")),
				'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
  public function addContactAction(Request $request)
  {
	$user = new User();
	$formBuilder = $this->get('form.factory')->createBuilder('form', $user);
	$formBuilder
		->add('userName', 'text')
		->add('Ajouter', 'submit')
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
		$session = $request->getSession();
		if (!$session->get("user"))
			return new RedirectResponse($this->generateURL('me_platform_inscription'));
		$currentUser = $repo->findOneBy(array('userName' => $session->get("user")->getUserName()));
		$currentUser->addContact($userGet);		
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return new RedirectResponse($this->generateURL('me_platform_accueil'));
	}
    return $this->render('MEPlatformBundle:Default:addContact.html.twig',
		array('form' => $form->createView(),
				'message' => '',
				'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
  
  public function check_contact($contacts, $contact_name)
  {
	foreach ($contacts as $contact)
	{
		if ($contact->getUserName() == $contact_name)
			return 1;
	}
	return 0;
  }
  
  public function delContactAction(Request $request)
  {
	$user = new User();
	$formBuilder = $this->get('form.factory')->createBuilder('form', $user);
	$formBuilder->add('userName', 'text')->add('Supprimer', 'submit');
	$form = $formBuilder->getForm();
	if ($form->handleRequest($request)->isValid())
	{
		$repo = $this->getDoctrine()->getManager()
				->getRepository('MEPlatformBundle:User');
		$userGet = $repo->findOneBy(array('userName' => $user->getUserName()));
		if (!$userGet)
			return $this->render('MEPlatformBundle:Default:addContact.html.twig',
					array('form' => $form->createView(),
							'message' => "L'utilisateur ".$user->getUserName()." est inconnu",
							'accueil_url' => $this->generateURL('me_platform_accueil')));
		$session = $request->getSession();
		if (!$session->get("user"))
			return new RedirectResponse($this->generateURL('me_platform_inscription'));
		$currentUser = $repo->findOneBy(array('userName' => $session->get("user")
						->getUserName()));
		$list = $currentUser->getContact();
		if (!$this->check_contact($currentUser->getContact(), $userGet->getUserName()))
			return $this->render('MEPlatformBundle:Default:addContact.html.twig',
					array('form' => $form->createView(),
							'message' => "L'utilisateur ".$userGet->getUserName()." n'est pas dans le carnet.",
							'accueil_url' => $this->generateURL('me_platform_accueil')));
		$currentUser->removeContact($userGet);		
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return new RedirectResponse($this->generateURL('me_platform_accueil'));
	}
    return $this->render('MEPlatformBundle:Default:addContact.html.twig',
		array('form' => $form->createView(),
				'message' => '',
				'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
}