<?php 

namespace Gushh\CoreBundle\Classes;

use Gushh\CoreBundle\Entity\Reservation;



/**
 * 
 */
class MailCore
{

    /**
     * @var   array
     */
    private $args;

    /**
     * @var   bool
     */
    private $debug;

    /**
     * @var   string
     */
    private $emailType;

//    private $emailTo = 'anibaldfernandez@hotmail.com';
    private $emailTo = 'info@zohartours.com';

    /**
     *
     * @method   __construct
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @param    Reservation   $reservation   Gushh\CoreBundle\Entity\Reservation Object
     * @param    array         $args          Metadata
     * @param    bool          $debug         If true adds 'DEBUG MODE' to the subject of the mail
     */
    public function __construct($mailer, $emailType, $args = [], $debug = false )
    {
        $this->args         = $args;
        $this->mailer       = $mailer;
        $this->emailType    = $emailType;
        $this->debug        = $debug;
    }

    /**
     * If true add "DEBUG MODE" to the subject
     *
     * @method   getDebug
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @return   string
     */
    public function getDebug() {
        return $this->debug ? 'DEBUG MODE - ' : '';
    }

    public function sendEmail() {
        switch ($this->emailType) {
            case 'requestHotel':
                $this->handleHotelReservation();
                break;
            case 'requestHotelCancelled':
                $this->handleHotelReservationCancelled();
                break;
            default:
                die('Invalid email type');
        }
    }

    // New Reservation
    private function handleHotelReservation() {
        $this->sendHotelReservationEmail();
        $this->sendAgencyReservationEmail();
    }

    private function sendHotelReservationEmail() {
        $reservation = $this->args['reservation'];
        $checkOut = $this->args['checkOut'];
        $hotel =  $reservation->getHotel();
        $occupancy = Util::getOccupancy($reservation->getAdults() + $reservation->getChildren());
        $hotelTemplate = $reservation->getIsBlackedOut() == true ? 'GushhCoreBundle:Emails:newRequestOnRequestForHotel.html.twig' : 'GushhCoreBundle:Emails:newRequestForHotel.html.twig';

        $html = $this->args['twig']->render($hotelTemplate,
            [
                'email'       => [ 'logo' => '', 'subtitle' => ''],
                'reservation' => $reservation,
                'checkout'    => $checkOut,
                'occupancy'   => $occupancy,
                'hotel'       => $hotel,
            ]);

        $this->sendSingleEmail(['no-reply@zohartours.com' => 'Reservation | ZoharTours']
            , [ $this->emailTo => 'Reserva']
            , 'Request Pax ' . $checkOut->getGuestFullName()
            . ' - ' . $reservation->getCode()
            . ' - ' . $hotel->getName()
            , $html
            , $this->args['pdf']->getOutputFromHtml($html)
            , 'Printer Friendly.pdf'
        );
    }

    private function sendAgencyReservationEmail() {
        $reservation = $this->args['reservation'];
        $checkOut = $this->args['checkOut'];
        $user =  $reservation->getOperator();
        $agency =  $user->getAgency();
        $hotel =  $reservation->getHotel();
        $occupancy = Util::getOccupancy($reservation->getAdults() + $reservation->getChildren());
        $agencyTemplate = $reservation->getIsBlackedOut() == true ? 'GushhCoreBundle:Emails:newRequestOnRequestForAgency.html.twig' : 'GushhCoreBundle:Emails:newRequestForAgency.html.twig';

        $html = $this->args['twig']->render($agencyTemplate,
            [
                'email'     => [ 'logo' => '', 'subtitle' => ''],
                'reservation' => $reservation,
                'checkout'  => $checkOut,
                'user'      => $user,
                'agency'    => $agency,
                'occupancy' => $occupancy,
                'hotel'     => $hotel,
            ]);


        $this->sendSingleEmail(['no-reply@zohartours.com' => 'Reservation | ZoharTours']
            , [ $this->emailTo, $user->getEmail() => 'Reserva']
            , 'Request Pax '  . $checkOut->getGuestFullName()
            . ' - [' . $agency->getName() . '] '. $hotel->getName()
            . ' - ' . $reservation->getCode()
            . ' - ' . $user->getLastname() . ' '. $user->getName()
            . ' - ' . $checkOut->getSearch()->getCheckIn()
            , $html
            , $this->args['pdf']->getOutputFromHtml($html)
            , 'Printer Friendly.pdf'
        );
    }

    // Cancel Reservation
    private function handleHotelReservationCancelled() {
        $this->sendHotelReservationCancelledEmail();
        $this->sendAgencyReservationCancelledEmail();
    }

    private function sendHotelReservationCancelledEmail() {
        $reservation = $this->args['reservation'];
        $checkOut = $this->args['checkOut'];
        $hotel =  $reservation->getHotel();
        $occupancy = Util::getOccupancy($reservation->getAdults() + $reservation->getChildren());

        $html = $this->args['twig']->render('GushhCoreBundle:Emails:cancelRequestForHotel.html.twig',
            [
                'email' => [ 'logo' => '', 'subtitle' => ''],
                'company' => [],
                'reservation' => $reservation,
                'checkout' => $checkOut,
                'occupancy'   => $occupancy,
            ]);

        $this->sendSingleEmail(['no-reply@zohartours.com' => 'Reservation | ZoharTours']
            , [ $this->emailTo => 'Reserva']
            , 'CANCELLED | Request Pax ' . $checkOut->getGuestFullName()
            . ' - ' . $reservation->getCode() . ' - ' . $hotel->getName()
            , $html
            , $this->args['pdf']->getOutputFromHtml($html)
            , 'Printer Friendly.pdf'
        );
    }

    private function sendAgencyReservationCancelledEmail() {
        $reservation = $this->args['reservation'];
        $checkOut = $this->args['checkOut'];
        $user =  $reservation->getOperator();
        $agency =  $user->getAgency();
        $hotel =  $reservation->getHotel();
        $occupancy = Util::getOccupancy($reservation->getAdults() + $reservation->getChildren());

        $html = $this->args['twig']->render('GushhCoreBundle:Emails:cancelRequestForAgency.html.twig',
            [
                'email'     => [ 'logo' => '', 'subtitle' => ''],
                'company'   => [],
                'reservation' => $reservation,
                'checkout'  => $checkOut,
                'user'      => $user,
                'agency'    => $agency,
                'occupancy'   => $occupancy,
            ]);

        $this->sendSingleEmail(['no-reply@zohartours.com' => 'Reservation | ZoharTours']
            , [ $this->emailTo, $user->getEmail() => 'Reserva']
            , 'CANCELLED | Request Pax ' . $checkOut->getGuestFullName()
            . ' - ' . $reservation->getCode()
            . ' - [' . $agency->getName() . '] '. $hotel->getName()
            . ' - ' . $checkOut->getSearch()->getCheckIn()
            , $html
            , $this->args['pdf']->getOutputFromHtml($html)
            , 'Printer Friendly.pdf'
        );
    }

    // Just send an email
    public function sendSingleEmail($from, $to, $subject, $body, $pdfContent = null, $pdfName = null)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html')
            ;

        if ($pdfContent)
            $message->attach(\Swift_Attachment::newInstance($pdfContent, $pdfName, 'application/pdf'));
        $this->mailer->send($message);
    }
}
