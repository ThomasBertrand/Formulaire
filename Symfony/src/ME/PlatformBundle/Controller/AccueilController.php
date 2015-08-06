<?php

namespace ME\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ME\PlatformBundle\Entity\User;

class AccueilController extends Controller
{
  public function accueilAction(Request $request)
  {
	$session = $request->getSession();
	$user = $session->get('user');
	if (!$user)
	{
		$lien1 = $this->generateURL('me_platform_connexion');
		$name1 = "Connexion";
		$lien2 = $this->generateURL('me_platform_inscription');
		$name2 = "Inscription";
	}
	else
	{
		$lien1 = $this->generateURL('me_platform_viewProfil', array("user_name" => $user->getUserName()));
		$name1 = $user->getUserName();
		$lien2 = $this->generateURL('me_platform_deconnexion');
		$name2 = "Déconnexion";
	}
	return $this->render('MEPlatformBundle:Default:accueil.html.twig',
		array(	'lien1' => $lien1,
				'name1' => $name1,
				'lien2' => $lien2,
				'name2' => $name2,
				'add_url' => $this->generateURL('me_platform_addContact'),
				'remove_url' => $this->generateURL('me_platform_delContact'),
				'list_url' => $this->generateURL('me_platform_listeCarnet')));
  }
}