<?php

namespace Gushh\BackendBundle\Controller;

// use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Gushh\CoreBundle\Classes\SearchCore;

/**
 * Dashboard controller.
 *
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
  /**
   * @Route("/", name="dashboard")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {

    $em = $this->getDoctrine()->getManager();

    $usaHotels    = 0;//count($em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', null, $this->getUser()->getVip()));
    $europeHotels = 0;//count($em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, null, $this->getUser()->getVip()));
    $rooms        = 0;//count($em->getRepository('GushhCoreBundle:Room')->findAll());
    $reservations = 0;//count($em->getRepository('GushhCoreBundle:Reservation')->findAll());
    $promotions   = 0;//count($em->getRepository('GushhCoreBundle:Promotion')->findAll());
    $searches     = 0;//count($em->getRepository('GushhCoreBundle:Search')->findAll());
    $agencies     = 0;//count($em->getRepository('GushhCoreBundle:Agency')->findAll());
    $operators    = 0;//count($em->getRepository('GushhCoreBundle:User')->findByRole('ROLE_AGENCY'));
    $viewers      = 0;//count($em->getRepository('GushhCoreBundle:User')->findByRole('ROLE_VIEWER'));

    
    $stats = [
        'usaHotels'     => $usaHotels,
        'europeHotels'  => $europeHotels,
        'searches'      => $searches,
        'rooms'         => $rooms,
        'reservations'  => $reservations,
        'promotions'    => $promotions,
        'agencies'      => $agencies,
        'vouchers'      => 12,
        'operators'     => $operators,
        'viewers'       => $viewers,
      // 'administrators'=> $administrators,
    ];


    return [
      'stats' => $stats
    ];

  }

  /**
   * @Route("/maintenance/", name="backend_maintenance")
   * @Method("GET")
   * @Template()
   */
  public function maintenanceAction()
  {

    return [];

  }

  /**
   * @Route("/mailito/", name="test_mail")
   * @Method("GET")
   */
  public function mailAction()
  {

    $mailLogger = new \Swift_Plugins_Loggers_ArrayLogger();
    // $this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mailLogger));
         
    $toEmail = 'gushh.io@gmail.com';
    $message = \Swift_Message::newInstance()
                ->setSubject('Hello')
                ->setFrom('anibaldfernandez@hotmail.com.com')
                ->setTo($toEmail)
                ->setBody('This is a test email.');
    
    if ($this->get('mailer')->send($message)) {
        echo '[SWIFTMAILER] sent email to ' . $toEmail;
    } else {
        echo '[SWIFTMAILER] not sending email: ' . $mailLogger->dump();
    } 
    // $message = \Swift_Message::newInstance()
    //     ->setSubject('Hello Email')
    //     ->setFrom('gustavo@trescraneos.com')
    //     ->setTo('gushh.io@gmail.com')
    //     ->setBody(
    //         "<2>asd</h2>",
    //         'text/html'
    //     )
        
    //      * If you also want to include a plaintext version of the message
    //     ->addPart(
    //         $this->renderView(
    //             'Emails/registration.txt.twig',
    //             array('name' => $name)
    //         ),
    //         'text/plain'
    //     )
        
    // ;
    // $this->get('mailer')->send($message);

    // return $this->redirect($this->generateUrl('dashboard'));
    
  }

}
