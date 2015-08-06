<?php

namespace ME\PlatformBundle\Controller;

use ME\PlatformBundle\Entity\User;
use ME\PlatformBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfilController extends Controller
{
  public function viewProfilAction($user_name, Request $request)
  {
	$repo = $this->getDoctrine()->getManager()
			->getRepository('MEPlatformBundle:User');
	$user = $repo->findOneBy(array('userName' => $user_name));
	if (!$user)
	{
		$response = new Response;
		$response->setContent("Erreur 404");
		$response->setStatusCode(Response::HTTP_NOT_FOUND);
		return ($response);
	}
	$edition = '';
	$user_cur = $request->getSession()->get('user');
	if ($user->getUserName() == $user_cur->getUserName())
		$edition = '<a href="'.$this->generateURL('me_platform_editProfil').'">Editer</a>';
    return $this->render('MEPlatformBundle:Default:view.html.twig',
		array(	'user_name' => $user->getUserName(),
				'mail' => $user->getMail(),
				'telephone' => $user->getTelephone(),
				'adresse' => $user->getAdresse(),
				'website' => $user->getWebSite(),
				'edit' => $edition,
				'accueil_url' => $this->generateURL('me_platform_accueil')));
	}
  public function editProfilAction(Request $request)
  {
	$session = $request->getSession();
	$user = $session->get('user');
    $response = new Response;
	if ($user)
	{
		$repo = $this->getDoctrine()->getManager()
				->getRepository('MEPlatformBundle:User');
		$user = $repo->findOneBy(array('userName' => $user->getUserName()));
		$form = $this->get('form.factory')->create(new UserType, $user);
		$form->remove('password');
		$form->remove('userName');
		if ($form->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();
				return new RedirectResponse($this->generateURL('me_platform_viewProfil', array('user_name' => $user->getUserName())));
		}
		return $this->render('MEPlatformBundle:Default:edit.html.twig',
							array('form' => $form->createView(),
									'type_page' => 'Editer le profil de '.$user->getUserName().':',
									'connecter' => 1,
									'accueil_url' => $this->generateURL('me_platform_accueil')));
	}
	else
		return $this->render('MEPlatformBundle:Default:inscription.html.twig',
							array('type_page' => 'Non connecté',
									'connecter' => 0,
									'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
  
  public function inscriptionAction(Request $request)
  {
	$user = new User();
	$form = $this->get('form.factory')->create(new UserType, $user);
	if ($form->handleRequest($request)->isValid())
	{
		$em = $this->getDoctrine()->getManager();
		if ($em->getRepository('MEPlatformBundle:User')->findOneBy(array('userName' => $user->getUserName())))
			return $this->render('MEPlatformBundle:Default:inscription.html.twig',
							array('form' => $form->createView(),
									'type_page' => 'Inscription: [Cette utilisateur existe déjà.]',
									'connecter' => 1,
									'accueil_url' => $this->generateURL('me_platform_accueil')));
		$em->persist($user);
		$em->flush();
		return new RedirectResponse($this->generateURL('me_platform_accueil'));
	}
	return $this->render('MEPlatformBundle:Default:inscription.html.twig',
							array('form' => $form->createView(),
									'type_page' => 'Inscription:',
									'connecter' => 1,
									'accueil_url' => $this->generateURL('me_platform_accueil')));
  }
}