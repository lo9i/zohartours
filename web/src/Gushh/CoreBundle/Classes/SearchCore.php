<?php

namespace Gushh\CoreBundle\Classes;

use Doctrine\ORM\EntityManager;
use Gushh\CoreBundle\Entity\Date;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Entity\User;
use Gushh\CoreBundle\Entity\Hotel;
use Gushh\CoreBundle\Entity\Room;
use Gushh\CoreBundle\Entity\Rate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints\DateTime;

class SearchCore
{
    /**
     * @var array
     */
    protected $hotels;

    /**
     * @var Search
     */
    protected $search;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var datetime
     */
    protected $today;

    /**
     * @var datetime
     */
    protected $checkIn;

    /**
     * @var datetime
     */
    protected $checkOut;

    /**
     * @var integer
     */
    protected $nights;

    /**
     * @var integer
     */
    protected $daysUntilCheckIn;

    /**
     * @var string
     */
    protected $locale;

    /**
     * [__construct description]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @param    Search          $search   [description]
     * @param    User            $user     [description]
     * @param    EntityManager   $em       [description]
     */
    public function __construct(Search $search, $user, EntityManager $em, $locale = "en")
    {
        $this->em               = $em;
        $this->search           = $search;
        $this->user             = $user;
        $this->hotels           = $this->getEnabledHotelsThatMatchCriteria();
        $this->today            = Carbon::today();
        $this->checkIn          = new Carbon($this->search->getCheckIn());
        $this->checkOut         = new Carbon($this->search->getCheckOut());
        $this->nights           = $this->search->getNights();
        $this->daysUntilCheckIn = $this->getDaysUntilCheckIn($this->checkIn);
        $this->locale           = $locale;
    }

    public function getDaysUntilCheckIn(Carbon $date)
    {
        return $this->today->diffInDays($date);
    }

    /**
     * [getEnabledHotelsThatMatchCriteria]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>>
     *
     * @return   array   $hotels
     */
    public function getEnabledHotelsThatMatchCriteria() {

        return $this->em->getRepository('GushhCoreBundle:Hotel')->findEnabledHotelsBy($this->search->getContinent()
                            , $this->search->getCountry()
                            , $this->search->getState()
                            , $this->search->getCity()
                            , $this->search->getHotel()
                            , $this->user != null && $this->user->getVip());
    }

    public function findHotels(&$hotelCount) {
        if ($this->daysUntilCheckIn < 0 || $this->hotels == [])
            return false;

        $matchedHotels = [];
        $checkIn = $this->search->getCheckIn();
        $checkOut = $this->search->getCheckOut();
        $adults = $this->search->getRoom1Adults();
        $children = $this->search->getRoom1Children();
        $childrenAge = $this->search->getRoom1ChildrenAge(null, $children);
        $percentCommission = ($this->user != null && $this->user->getAgency() != null) ? $this->user->getAgency()->getCommission() : 0;
        $pax = $adults + $children;
        $days = Util::createRange($checkIn, $checkOut, false);
        $hotelCount = count($this->hotels);
        foreach ($this->hotels as $hotel) {

            $matchedHotel = $this->checkIfHotelApplies($hotel, $adults, $children, $childrenAge, $percentCommission, $pax, $days);

            if ($matchedHotel)
                $matchedHotels[] = $matchedHotel;
        }

        SearchCore::sortHotels($matchedHotels);

        return $matchedHotels;
    }

    private static function sortHotels(&$matchedHotels) {
        usort($matchedHotels, function ($hotelA, $hotelB) {
            if($hotelA['isBlackedOut'] == true && $hotelB['isBlackedOut'] == false)
                return 1;

            if($hotelA['isBlackedOut'] == false && $hotelB['isBlackedOut'] == true)
                return -1;

            if($hotelA['isBlackedOut'] == true && $hotelB['isBlackedOut'] == true)
                return 0;


            if ($hotelA['lowPrice'] == $hotelB['lowPrice'])
                return 0;

            return ($hotelA['lowPrice'] < $hotelB['lowPrice']) ? -1 : 1;
        });
    }

    // Make hotels and rooms for search
    public function checkIfHotelApplies($hotel, $adults, $children, $childrenAge, $percentCommission, $pax, $days) {

        $rooms = $this->em->getRepository('GushhCoreBundle:Room')->findByHotelAndCapacity($hotel, $pax);

        if($rooms == [])
            return;

        $matchedHotel = $this->buildMatchedHotel($hotel, $adults, $children, $childrenAge, $pax, $this->nights);
        foreach ($rooms as $room) {

            $matchedRoom = $this->checkIfRoomApplies($matchedHotel, $room, $percentCommission, $pax, $days);
            if (!$matchedRoom)
                continue;

            $matchedHotel['rooms'][] = $matchedRoom;

            $matchedPromoRooms = SearchCore::checkIfRoomHasPromotions($this->search, $this->user, $matchedHotel, $room, $matchedRoom['dates'], $percentCommission, $pax, $this->search->getCheckIn(), $this->nights, $this->locale);
            if (!$matchedPromoRooms || $matchedPromoRooms == [])
                continue;

            foreach ($matchedPromoRooms as $matchedPromoRoom)
                $matchedHotel['rooms'][] = $matchedPromoRoom;
        }

        if($matchedHotel['rooms'] == [])
            return;

        SearchCore::sortRooms($matchedHotel);

        $this->calculateTotalsForHotel($matchedHotel);
        return $matchedHotel;
    }

    private static function sortRooms(&$matchedHotel) {
        usort($matchedHotel['rooms'], function ($roomA, $roomB) {
            if($roomA['isBlackedOut'] == true && $roomB['isBlackedOut'] == false)
                return 1;

            if($roomA['isBlackedOut'] == false && $roomB['isBlackedOut'] == true)
                return -1;
            if($roomA['isBlackedOut'] == true && $roomB['isBlackedOut'] == true)
                return 0;
            if ($roomA['totalWithMandatoryServices'] == $roomB['totalWithMandatoryServices'])
                return 0;
            return ($roomA['totalWithMandatoryServices'] < $roomB['totalWithMandatoryServices']) ? -1 : 1;
        });

    }

    public function checkIfRoomApplies(&$matchedHotel, $room, $percentCommission, $pax, $days) {

        $datesEntities = $this->em->getRepository('GushhCoreBundle:Date')->findDatesByRoomAndDays($room->getId(), $days, $this->nights);

        if (!$datesEntities)
            return;

        $matchedRoom = SearchCore::buildMatchedRoom($matchedHotel, $room, $datesEntities, $pax, $percentCommission, $this->nights, $this->locale);
        SearchCore::calculateDatesAmounts($matchedRoom);
        SearchCore::calculateTotalsForRoom($matchedHotel, $matchedRoom, $this->search->getCheckIn(), $this->nights);
        return $matchedRoom;
    }

    public static function checkIfRoomHasPromotions($search, $user, &$matchedHotel, $room, $datesEntities, $percentCommission, $pax, $checkIn, $nights, $locale) {

        if (!$datesEntities)
            return;

        $combinablePromotions    = $room->getCombinablePromotions();
        $nonCombinablePromotions = $room->getNonCombinablePromotions();

        if ($combinablePromotions || $nonCombinablePromotions) {
            $matchedRoom = SearchCore::buildMatchedRoom($matchedHotel, $room, $datesEntities, $pax, $percentCommission, $nights, $locale);
            $roomWithPromotions = SearchCore::applyPromotions($search, $user, $matchedRoom, $combinablePromotions, $nonCombinablePromotions);

            if ($roomWithPromotions && $roomWithPromotions != []) {
                foreach ($roomWithPromotions as $key => $rommWithPromotion) {
                    SearchCore::calculateDatesAmounts($roomWithPromotions[$key]);
                    SearchCore::calculateTotalsForRoom($matchedHotel, $roomWithPromotions[$key], $checkIn, $nights);
                }
                return $roomWithPromotions;
            }
        }
        return;
    }

    // Build DTOs functions Start
    public static function buildMatchedHotel(Hotel $hotel, $adults, $children, $childrenAge, $pax, $nights)
    {
        if ($children) {
            $realAdults = Util::realAdults($hotel, $adults, $childrenAge);
            $realChildren = $pax - $realAdults;
        } else {
            $realAdults = $adults;
            $realChildren = 0;
        }

        return [
            'id' => $hotel->getId(),
            'hotel' => $hotel,
            'name' => $hotel->getName(),
            'address' => $hotel->getAddress(),
            'coords' => $hotel->getCoords(),
            'subtitle' => $hotel->getSubtitle(),
            'stars' => $hotel->getStars(),
            'city' => $hotel->getCity(),
            'state' => $hotel->getState(),
            'checkIn' => $hotel->getCheckIn(),
            'checkOut' => $hotel->getCheckOut(),
            'childAge' => $hotel->getChildAge(),
            'currency' => $hotel->getCurrency()->getSymbol(),
            'images' => $hotel->getImages(),
            'video' => $hotel->getVideo(),
            'mandatoryServicesPrice' => $hotel->getMandatoryServicesTotal($pax, $nights),
            'rooms' => [],
            'adults' => $realAdults,
            'children' => $realChildren,
            'lowPrice' => 0,
            'cancellableUntil' => 'NOT SET',
            'cancellationCutOff' => 0,
            'hasPromotion' => false,
            'isBlackedOut' => false,
        ];
    }

    public static function buildMatchedRoom($matchedHotel, Room $room, $datesEntities, $pax, $percentCommission, $nights, $locale = "en")
    {
        // Get room's mandatory services and add them to the hotel's
        $hotelPlusRoomMandatoryServicesPricePerStay = $matchedHotel['mandatoryServicesPrice'] + $room->getMandatoryServicesTotal($pax, $nights);
        $nightMandatoryServicesPrice = $hotelPlusRoomMandatoryServicesPricePerStay / $nights;

        return [
            'id' => $room->getId(),
            'name' => $room->getName(),
            'video' => $room->getVideo(),
            'capacity' => $room->getCapacity(),
            'currency' => $matchedHotel['currency'],
            'stayDates' => SearchCore::buildStayDates($datesEntities, $matchedHotel['adults'], $nights, $nightMandatoryServicesPrice, $percentCommission),
            'dates' => $datesEntities,
            'mandatoryServices' => array_merge($room->getHotel()->getMandatoryServices(), $room->getMandatoryServices()),
            'onRequest' => false,
            'isBlackedOut' => false,
            'isPromotion' => false,
            'isCancellable' => true,
            'cancellableUntil' => Carbon::today(),
            'adults' => $matchedHotel['adults'],
            'children' => $matchedHotel['children'],
            'description' => $locale == "es" ? $room->getEsDescription() :  $room->getEnDescription()
        ];
    }

    public static function buildStayDates($datesEntities, $pax, $nights, $nightMandatoryServices, $percentCommission) {

        $stayDates = [];
        foreach ($datesEntities as $dateEntity)
            $stayDates[] = SearchCore::buildStayDate($dateEntity, $pax, $nights, $nightMandatoryServices, $percentCommission);

        return $stayDates;
    }

    public static function buildStayDate(Date $date, $pax, $nights, $nightMandatoryServices, $percentCommission) {

        $rate = $date->getRate();
        $priceNet = $rate->getPrice($pax);
        $priceBase = $rate->getPrice();
        $extraPeople = $priceNet - $priceBase;
        return [
                'id'                => $date->getId(),
                'date'              => $date,
                'day'               => $date->getDay(),
                'nightTax'          => ($rate->getTax() / 100),
                'nightOccupancyTax' => $rate->getOccupancyTax(),
                'priceNetRemaining' => $priceBase,
                'priceNet'          => $priceNet,
                'priceExtraPeople'  => $extraPeople,
                'mandatoryServices' => $nightMandatoryServices,
                'mandatoryServicesRemaining' => $nightMandatoryServices,
                'profit'            => $rate->getProfit(),
                'profitType'        => $rate->getProfitType()->getSlug(),
                'agencyCommission'  => $percentCommission,
                'cutOff'            => $date->getCancellationPolicy()->getCutOff(),
                'cutOffType'        => $date->getCancellationPolicy()->getCancellationPolicyType()->getSlug(),
                'cancellationPenalty' => $date->getCancellationPolicy()->getPenalty(),
                'isBlackedOut'      => $rate->getPrice() == 0,
                'isPremium'         => $date->isPremiumDate(),
                'onRequest'         => SearchCore::checkIfDateIsOnRequest($date, $nights),
                'isPromotion'       => false,

                // Calculated values
                // Costs
                'totalNet'          => round(0, 2),
                // Tax is the same for both, us and agenecy
//                'totalTaxNet'   => round(0, 2),
                'totalWithServicesNet' => round(0, 2),

                // Prices
                'price'             => round(0, 2),
                'tax'               => round(0, 2),
                'commission'        => round(0, 2),
                'total'             => round(0, 2),
                'dateWithDiscount'  => false,
                'isObject'          => false,
                'isFree'            => false,
                'totalWithServices' => round(0, 2),
                'totalWithServicesAndCommission' => round(0, 2)
            ];
    }
    // Build DTOs functions End

    public static function applyPromotions($search, $user, $matchedRoom, $combinablePromotions, $nonCombinablePromotions)
    {
        $promoCore = new PromoCore($search, $matchedRoom, $user, $combinablePromotions, $nonCombinablePromotions);
        $promos = $promoCore->findPromotions();
        if ($promos == [])
            return [];


        $romsWithPromos = [];
        foreach ($promos as $promoRoom) {
            if ($promoRoom == [])
                continue;

           $promoRoom['promoIds'] = implode(",", $promoRoom['promoIds']);
           $romsWithPromos[] = $promoRoom;
       }

       return $romsWithPromos;
    }

    // Calculate amounts for day/stay
    public static function calculateDatesAmounts(&$matchedRoom)
    {
        foreach ($matchedRoom['stayDates'] as $key => $stayDate)
            SearchCore::calculateDateAmounts($matchedRoom['stayDates'][$key]);
    }

    public static function calculateDateAmounts(&$stayDate)
    {
        if($stayDate['isBlackedOut']) {
            $stayDate['price'] = round(0,2);
            $stayDate['tax'] = round(0,2);
            $stayDate['commission'] = round(0,2);
            $stayDate['total'] = round(0,2);
            $stayDate['totalWithServices'] = round(0,2);
            $stayDate['totalWithServicesAndCommission'] = round(0,2);
            return;
        }

        $nightRoomPrice = $stayDate['priceNet'];
        $nightTax = ($nightRoomPrice * $stayDate['nightTax']) + $stayDate['nightOccupancyTax'];
        $nightPriceAndTaxes = $nightRoomPrice + $nightTax;
        $profitTypeSlug = $stayDate['profitType'];
        $nightPriceWithTaxAndProfit = $nightPriceAndTaxes
            + (($profitTypeSlug == 'net')
                ? $stayDate['profit']
                : $nightPriceAndTaxes * ($stayDate['profit'] / 100)
            );

        $commission = $nightPriceWithTaxAndProfit * ($stayDate['agencyCommission'] / 100);
        $priceNightToShow = $nightPriceWithTaxAndProfit - $nightTax;
        $totalPriceWithServices = $nightPriceWithTaxAndProfit + $stayDate['mandatoryServices'];
        $totalPriceWithServicesAndCommission = $totalPriceWithServices - $commission;
        $totalPriceWithServicesNet = $nightPriceAndTaxes + $stayDate['mandatoryServices'];

        $stayDate['price'] = round($priceNightToShow,2);
        $stayDate['tax'] = round($nightTax,2);
        $stayDate['commission'] = round($commission,2);
        $stayDate['total'] = round($nightPriceWithTaxAndProfit,2);
        $stayDate['totalWithServices'] = round($totalPriceWithServices,2);
        $stayDate['totalWithServicesAndCommission'] = round($totalPriceWithServicesAndCommission,2);

        // Our Final cost
        $stayDate['taxNet'] = round($nightTax,2);
        $stayDate['totalWithServicesNet'] = $totalPriceWithServicesNet;

        if($stayDate['isPromotion'] == true) {
            $stayDate['priceWODiscount'] = $stayDate['price'];
            $stayDate['totalWODiscount'] = $stayDate['total'];
            $stayDate['taxWODiscount'] = $stayDate['tax'];

            if($stayDate['isFree'] == true) {
                // If it is free, no taxes
                $nightTax = 0;
                $priceNightToShow = 0;
                $commission = 0;
                $nightPriceWithTaxAndProfit = 0;
                $totalPriceWithServices = 0;
                $totalPriceWithServicesAndCommission = $stayDate['mandatoryServicesRemaining'];
                $totalPriceWithServicesNet = $stayDate['mandatoryServicesRemaining'];
            }
            else {
                $nightRoomPrice = $stayDate['priceNetRemaining'] + $stayDate['priceExtraPeople'];
                $nightTax = ($nightRoomPrice * $stayDate['nightTax']) + $stayDate['nightOccupancyTax'];
                $nightPriceAndTaxes = $nightRoomPrice + $nightTax;
                $profitTypeSlug = $stayDate['profitType'];
                $nightPriceWithTaxAndProfit = $nightPriceAndTaxes
                    + (($profitTypeSlug == 'net')
                        ? $stayDate['profit']
                        : $nightPriceAndTaxes * ($stayDate['profit'] / 100)
                    );

                $commission = $nightPriceWithTaxAndProfit * ($stayDate['agencyCommission'] / 100);
                $priceNightToShow = $nightPriceWithTaxAndProfit - $nightTax;
                $totalPriceWithServices = $nightPriceWithTaxAndProfit + $stayDate['mandatoryServicesRemaining'];
                $totalPriceWithServicesAndCommission = $totalPriceWithServices - $commission;
                $totalPriceWithServicesNet = $nightPriceAndTaxes + $stayDate['mandatoryServicesRemaining'];

            }
            $stayDate['tax'] = round($nightTax,2);
            $stayDate['price'] = round($priceNightToShow, 2);
            $stayDate['commission'] = round($commission,2);
            $stayDate['total'] = round($nightPriceWithTaxAndProfit,2);
            $stayDate['totalWithServices'] = round($totalPriceWithServices,2);
            $stayDate['totalWithServicesAndCommission'] = round($totalPriceWithServicesAndCommission, 2);
            // Our Final cost with disccounts applied
            $stayDate['taxNet'] = round($nightTax,2);
            $stayDate['totalWithServicesNet'] = $totalPriceWithServicesNet;
        }
    }

    public static function calculateTotalsForRoom($matchedHotel, &$matchedRoom, $checkIn, $nights, $useNetValues = false)
    {
        // Costs
        $stayTotalNet = round(0, 2);
        $stayTotalServicesNet = round(0, 2);
        $stayTotalWithServicesNet = round(0, 2);

        // If there are no discount they are the same.
        // If any discount applied they differ
        $stayTaxNet = round(0, 2);

        $roomCancellationPolicyType = '';
        $roomCancellationPolicyCutOff = 0;
        $roomCancellationPolicyPenaly = round(0,2);
        $roomOnRequest = false;
        $roomBlackedOut = false;
        $roomIsCancelable = $matchedRoom['isCancellable'];
        $stayTotal = round(0, 2);
        $stayPrice = round(0, 2);
        $stayTaxes = round(0, 2);
        $stayCommission = round(0, 2);
        $stayTotalWithMandatory = round(0, 2);
        $stayMandatory = round(0, 2);
        $roomMaxPrice = round(0, 2);
        $roomLowPrice = round(0, 2);
        $hotelCheckin = $matchedHotel['checkIn'];
        $roomDaysUntilCheckin = Util::daysUntilCheckIn($checkIn);
        $cancellableUntil = Carbon::today();
        $cancellableSet = false;
        foreach ($matchedRoom['stayDates'] as $stayDate) {

            if ($stayDate['onRequest'] == true)
                $roomOnRequest = true;

            if ($stayDate['isBlackedOut'] == true) {
                $roomBlackedOut = true;
                $roomOnRequest = true;
                continue;
            }
            $roomLowPrice = SearchCore::getLowerPrice($roomLowPrice, $stayDate['total']);
            $roomMaxPrice = $stayDate['totalWithServicesAndCommission'] > $roomMaxPrice ? $stayDate['totalWithServicesAndCommission'] : $roomMaxPrice;

            if ($roomCancellationPolicyCutOff == 0 && $stayDate['cutOff'] > 0) {
                $roomCancellationPolicyCutOff = $stayDate['cutOff'];
                $roomCancellationPolicyType = $stayDate['cutOffType'];
                $roomCancellationPolicyPenaly = $stayDate['cancellationPenalty'];
            }

            $isCancellable = $roomDaysUntilCheckin >= $stayDate['cutOff'];

            if ($roomIsCancelable == true && $isCancellable == true) {
                if ( $cancellableSet == false ) { // Only care about first day
                    $cancellableSet = true;
                    $cancellableUntil = new \DateTime($checkIn . ' ' . $hotelCheckin);
                    $cancellableUntil->sub(new \DateInterval('P' . $roomCancellationPolicyCutOff . 'D'));
                }
            } else
                $roomIsCancelable = false;

            $priceToAdd = $stayDate['price'];
            $taxToAdd = $stayDate['tax'];
            if($matchedRoom['isPromotion'] && isset($stayDate['dateWithDiscount']) && $stayDate['dateWithDiscount'] == true) {

                if($stayDate['totalWODiscount'] != $stayDate['total'])
                    $matchedRoom['promoDiscount'] = $matchedRoom['promoDiscount'] + ($stayDate['totalWODiscount'] - $stayDate['total']);

                $thePrice = $stayDate['priceNetRemaining'] + $stayDate['priceExtraPeople'];
                if($stayDate['priceNet'] != $thePrice)
                    $matchedRoom['promoDiscountNet'] = $matchedRoom['promoDiscountNet'] + ($stayDate['priceNet'] - $thePrice);

                if($stayDate['mandatoryServicesRemaining'] != $stayDate['mandatoryServices']) {
                    $matchedRoom['promoDiscount'] = $matchedRoom['promoDiscount'] + ($stayDate['mandatoryServices'] - $stayDate['mandatoryServicesRemaining']);
                    $matchedRoom['promoDiscountNet'] = $matchedRoom['promoDiscountNet'] + ($stayDate['mandatoryServices'] - $stayDate['mandatoryServicesRemaining']);
                }

                $priceToAdd = $stayDate['priceWODiscount'];
                $taxToAdd = $stayDate['taxWODiscount'];
            }

            // This is our room cost
                $stayTotalNet             += $stayDate['priceNet'];
            // This is the total of mandatory services
            $stayTotalServicesNet     += $stayDate['mandatoryServices'];
            // This is the total cost for the room
            $stayTotalWithServicesNet += $stayDate['totalWithServicesNet'];

            // This are the taxes we are charged by the hotel
            $stayTaxNet += $stayDate['taxNet'];

            $stayTaxes              += $taxToAdd;
            $stayPrice              += $priceToAdd;
            $stayTotal              += $stayDate['total'];
            $stayCommission         += $stayDate['commission'];
            $stayMandatory          += $stayDate['mandatoryServicesRemaining'];
            $stayTotalWithMandatory += $stayDate['totalWithServicesAndCommission'];
        }

        $matchedRoom['isBlackedOut'] = $roomBlackedOut;
        $matchedRoom['isCancellable'] = $roomIsCancelable;
        $matchedRoom['calendar'] = Util::makeCalendar($matchedRoom['stayDates'], $nights, $useNetValues);
        $matchedRoom['cancellableUntil'] = $cancellableUntil;
        $matchedRoom['onRequest'] = $roomOnRequest;

        if($roomBlackedOut) {
            $matchedRoom['taxes'] = round(0, 2);
            $matchedRoom['price'] = round(0, 2);
            $matchedRoom['commission'] = round(0, 2);
            $matchedRoom['total'] = round(0, 2);
            $matchedRoom['totalWithMandatoryServices'] = round(0, 2);
            $matchedRoom['mandatoryServicesTotal'] = round(0, 2);
            $matchedRoom['lowPrice'] = round(0, 2);
            $matchedRoom['cancellationPrice'] = round(0, 2);
        }
        else {
            if($matchedRoom['isCancellable'] == true) {
                switch ($roomCancellationPolicyType) {
                    case 'nights':
                        $stayCancellationPrice = ($roomCancellationPolicyPenaly <= $nights)
                            ? $roomMaxPrice * $roomCancellationPolicyPenaly
                            : $roomMaxPrice * $nights;
                        break;
                    case 'net':
                        $stayCancellationPrice = $roomCancellationPolicyPenaly;
                        break;
                    case 'percent':
                        $stayCancellationPrice = $stayTotal * ($roomCancellationPolicyPenaly / 100);
                        break;
                    default:
                        die('Invalid cancellation policy');
                }
            }
            else {
                $stayCancellationPrice = $stayTotal;
            }
            // Our costs
            $matchedRoom['priceNet'] = round($stayTotalNet, 2);
            $matchedRoom['taxesNet'] = round($stayTaxNet, 2);
            $matchedRoom['totalServicesNet'] = round($stayTotalServicesNet, 2);
            $matchedRoom['totalWithServicesNet'] = round($stayTotalWithServicesNet, 2);

            // Prices for agency
            $matchedRoom['price'] = round($stayPrice, 2);
            $matchedRoom['taxes'] = round($stayTaxes, 2);
            $matchedRoom['commission'] = round($stayCommission, 2);
            $matchedRoom['total'] = round($stayTotal, 2);
            $matchedRoom['totalWithMandatoryServices'] = round($stayTotalWithMandatory, 2);
            $matchedRoom['mandatoryServicesTotal'] = round($stayMandatory, 2);
            $matchedRoom['lowPrice'] = round($roomLowPrice, 2);
            $matchedRoom['cancellationPrice'] = round($stayCancellationPrice, 2);
        }
    }

    public function calculateTotalsForHotel(&$matchedHotel)
    {
        $hasPromotion = false;
        $lowerPrice = 0;
        $cancellableUntil = '';
        $cancellationPolicyCutOff = 0;
        $hotelIsBlackedOut = true;
        foreach ($matchedHotel['rooms'] as $room) {

            if ($room['isBlackedOut'] == true)
                continue;

            $hotelIsBlackedOut = false;
            if ($room['isPromotion'] == true)
                $hasPromotion = true;

            $roomPricePerNight = $room['totalWithMandatoryServices']/$this->nights;
            if($lowerPrice == 0 || $roomPricePerNight<$lowerPrice)
                $lowerPrice = $roomPricePerNight;

            if($room['isCancellable'] == true)
                $cancellableUntil = $room['cancellableUntil'];
        }

        $matchedHotel['isBlackedOut'] = $hotelIsBlackedOut;
        if($hotelIsBlackedOut) {
            $matchedHotel['lowPrice'] = round(0, 2);
        }
        else {
            $matchedHotel['lowPrice'] = round($lowerPrice, 2);
        }

        $matchedHotel['hasPromotion'] = $hasPromotion;
        $matchedHotel['cancellableUntil'] = $cancellableUntil;
        $matchedHotel['cancellationCutOff'] = $cancellationPolicyCutOff;
    }

    public static function getSearchData($search, $user, $em, $hotel, $room, $promo, $locale = "en") {
        $checkIn = $search->getCheckIn();
        $checkOut = $search->getCheckOut();
        $nights = $search->getNights();
        $adults = $search->getRoom1Adults();
        $children = $search->getRoom1Children();
        $childrenAge = $search->getRoom1ChildrenAge(null, $children);
        $percentCommission = $user->getAgency() != null ? $user->getAgency()->getCommission() : 0;
        $pax = $adults + $children;
        $days = Util::createRange($checkIn, $checkOut, false);

        $matchedHotel = SearchCore::buildMatchedHotel($hotel, $adults, $children, $childrenAge, $pax, $nights);

        $datesEntities = $em->getRepository('GushhCoreBundle:Date')->findDatesByRoomAndDays($room->getId(), $days, $nights);

        if (!$datesEntities)
            return;

        $matchedRoom = SearchCore::buildMatchedRoom($matchedHotel, $room, $datesEntities, $pax, $percentCommission, $nights, $locale);

        if($promo) {

            $combinablePromotions    = [];
            $nonCombinablePromotions = [];
            if($promo != '0') {
                $promosApplied = $em->getRepository('GushhCoreBundle:Promotion')->findById(explode(',', $promo));

                foreach( $promosApplied as $promoApplied ) {
                    if($promoApplied->getCombinable() == true)
                        $combinablePromotions[] = $promoApplied;
                    else
                        $nonCombinablePromotions[] = $promoApplied;
                }
            }

             if ($combinablePromotions != [] || $nonCombinablePromotions != [])
                $matchedRoom = SearchCore::applyPromotions($search, $user, $matchedRoom, $combinablePromotions, $nonCombinablePromotions)[0];
        }

        SearchCore::calculateDatesAmounts($matchedRoom);
        SearchCore::calculateTotalsForRoom($matchedHotel, $matchedRoom, $checkIn, $nights);

        return $matchedRoom;
    }


    // Util functions
    /**
     * [isInsideCutOff description]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>>
     *
     * @param    integer   $cutOff             [description]
     *
     * @return   bool
     */
    public static function isInsideCutOff($theDate)
    {
        return Util::daysUntilCheckIn($theDate->getDate()) < $theDate->getCutOff();
    }

    /**
     * [isOutsideCutOff description]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>>
     *
     * @param    integer   $cutOff             [description]
     *
     * @return   bool
     */
    public function isOutsideCutOff($cutOff)
    {
        return ($this->daysUntilCheckIn >= $cutOff);
    }

    /**
     * [isOnRequest description]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>>
     *
     * @param    array          $dates           [description]
     * @param    array          $availableDays   [description]
     * @param    Search         $search          [description]
     *
     * @return   [type]         [description]
     */
    public static function isOnRequest(array $dates, array $availableDays, Search $search)
    {
        if (self::hasStopSellDates($dates) == true)
            return true;

        foreach ($dates as $date) {
            $theDate = is_a($date, 'Gushh\CoreBundle\Entity\Date') ? $date : $date['date'];

            if (!in_array($theDate->getDay(), $availableDays))
                return false;

            $nights = $search->getNights();
            return (Util::daysUntilCheckIn($theDate->getDate()) < $theDate->getCutOff()
                || $nights < $theDate->getMinimumStay()
                || $nights > $theDate->getMaximumStay()
                || $theDate->getStock() < 1
            );
        }

        return false;
    }

    public static function validOnPremiumDates(Date $date, $validOnPremiumDates)
    {
        return ($validOnPremiumDates || !$date->isPremiumDate());
    }

    public static function hasStopSellDates(array $dates)
    {
        foreach ($dates as $date) {
            $theDate = is_a($date, 'Gushh\CoreBundle\Entity\Date') ? $date : $date['date'];
            if ($theDate->isStopSell())
                return true;
        }

        return false;
    }

    public static function hasPremiumDates(array $dates)
    {
        foreach ($dates as $date) {
            $theDate = is_a($date, 'Gushh\CoreBundle\Entity\Date') ? $date : $date['date'];
            if ($theDate->isPremiumDate())
                return true;
        }

        return false;
    }

    public static function validOnStopSellDates(Date $date, $validOnStopSellDates)
    {
        return ($date->isStopSell() && $validOnStopSellDates || !$date->isStopSell());
    }

    public static function checkIfDateIsOnRequest(Date $date, $nights)
    {
        return (
            SearchCore::isInsideCutOff($date)
            || $date->getStopSell()
            || $nights < $date->getMinimumStay()
            || $nights > $date->getMaximumStay()
            || $date->getStock() < 1
        );
    }

    public static function getLowerPrice($lowPrice, $nightPrice)
    {
        return ($lowPrice < $nightPrice && $lowPrice > 0)
            ? $lowPrice
            : $nightPrice;
    }
}
