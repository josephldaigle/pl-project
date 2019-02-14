<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/13/18
 */


namespace PapaLocal\Controller;


use PapaLocal\Core\Logging\LogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController.
 *
 * @Route("admin")
 *
 * @package PapaLocal\Controller
 */
class AdminController extends AbstractController
{
    /**
     * Allows admin to view all routes.
     * @Route("/log/view/all", name="admin_log_view_all",  methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param LogRepository $logRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewLogs(LogRepository $logRepository)
    {
        $logs = $logRepository->load();

        return $this->render('pages/logs.html.twig', array('logs' => $logs));
    }
}