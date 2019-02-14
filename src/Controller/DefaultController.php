<?php
/**
 * Created by Joseph Daigle.
 * Date: 8/20/18
 * Time: 3:00 PM
 */

namespace PapaLocal\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * DefaultController.
 *
 * Provides access to the login page.
 *
 * @package PapaLocal\Controller
 */
class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="homepage", methods={"GET"})
	 */
	public function indexAction(Request $request)
	{
		//redirect to feed page

		return new RedirectResponse('feed');
	}

	/**
	 * @Route("/terms-of-service", name="terms_of_service", methods={"GET"})
	 */
	public function termsOfService(Request $request)
	{
		// render terms of service page
		return $this->render('pages/terms.html.twig');

	}
}
