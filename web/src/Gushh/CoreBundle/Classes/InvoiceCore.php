<?php 

namespace Gushh\CoreBundle\Classes;

use Gushh\CoreBundle\Entity\Reservation;



/**
 * 
 */
class InvoiceCore
{

    /**
     * @var   array
     */
    private $args;

    /**
     *
     * @method   __construct
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @param    Reservation   $reservation   Gushh\CoreBundle\Entity\Reservation Object
     * @param    array         $args          Metadata
     */
    public function __construct(Reservation $reservation, $args = [] )
    {
        $this->args         = $args;
        $this->reservation  = $reservation;
    }

    public function buildInvoice()
    {
        $checkOut = $this->args['checkOut'];
        $prices = $this->args['prices'];
        $user =  $this->args['user'];
        $agency =  $user->getAgency();
        $hotel =  $this->args['hotel'];
        $occupancy = Util::getOccupancy($prices['adults']);

        $html = $this->args['twig']->render('GushhCoreBundle:Emails:newRequestForHotel.html.twig',
            [
                'email' => $email,
                'company' => [],
                'reservation' => $reservation,
                'prices' => $prices,
                'checkOut' => $checkOut,
                'occupancy' => $occupancy,
            ]);

        $this->sendSingleEmail(['no-reply@zohartours.com' => 'Reservation | ZoharTours']
//            , ['anibaldfernandez@hotmail.com' => 'Reserva']
            , ['info@zohartours.com' => 'Reservas']
            , 'Request ' .  $hotel->getName() . ' - ' . $reservation->getCode()
            , $html
            , $this->args['pdf']->getOutputFromHtml($html)
            , 'Printer Friendly.pdf'
            );
    }

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
