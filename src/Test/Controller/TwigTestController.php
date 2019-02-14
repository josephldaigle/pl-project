<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/2/19
 * Time: 3:00 PM
 */

namespace PapaLocal\Test\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * TwigTestPage.
 *
 * @package PapaLocal\Test\Controller
 */
class TwigTestController extends AbstractController
{
    /**
     * Loads a test page when app env = test.
     *
     * @Route("/twig/test", name="twig_test", methods={"GET"})
     *
     * @param Request               $request
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function index(Request $request, TokenStorageInterface $tokenStorage)
    {

        return $this->render('test/twig-test-page.html.twig');

//        throw $this->createNotFoundException();
    }
}