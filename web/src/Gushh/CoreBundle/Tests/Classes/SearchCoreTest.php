<?php
/**
 * User: anibal
 * Date: 11/14/16
 * Time: 17:35
 */

use Gushh\CoreBundle\Entity\Agency;
use Gushh\CoreBundle\Entity\Date;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Entity\CancellationPolicyType;
use Gushh\CoreBundle\Entity\Hotel;
use Gushh\CoreBundle\Entity\HotelCurrency;
use Gushh\CoreBundle\Entity\ProfitType;
use Gushh\CoreBundle\Entity\Rate;
use Gushh\CoreBundle\Entity\Room;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Entity\User;

use Gushh\CoreBundle\Classes\SearchCore;

use Carbon\Carbon;

use Doctrine\Common\Persistence\ObjectManager;

class SearchCoreTest extends \PHPUnit_Framework_TestCase
{
    public function setProtectedProperty($object, $property, $value) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
    private function buildSearch($searchData) {
        $search = $this->createMock(Search::class);
        $search->expects($this->any())
            ->method('getNights')
            ->willReturn($searchData['nights']);
        $search->expects($this->any())
            ->method('getRoom1Adults')
            ->willReturn($searchData['adults']);
        $search->expects($this->any())
            ->method('getRoom1Children')
            ->willReturn($searchData['children']);
        $search->expects($this->any())
            ->method('getRooms')
            ->willReturn($searchData['rooms']);
        $search->expects($this->any())
            ->method('getCheckIn')
            ->willReturn($searchData['checkIn']);
        $search->expects($this->any()) // 10 days
            ->method('getCheckOut')
            ->willReturn($searchData['checkOut']);
        return $search;
    }
    private function buildProfitType($slug) {
        $profitType = new ProfitType;
        $this->setProtectedProperty($profitType, 'slug', $slug);
        return $profitType;
    }
    private function buildRate($roomRateData) {
        $rate = new Rate;
        $this->setProtectedProperty( $rate, 'price', $roomRateData['price']);
        $this->setProtectedProperty( $rate, 'tax', $roomRateData['tax']);
        $this->setProtectedProperty( $rate, 'profit', $roomRateData['profit']);
        $this->setProtectedProperty( $rate, 'occupancyTax', $roomRateData['occupancyTax']);
        $this->setProtectedProperty( $rate, 'profitType', $this->buildProfitType($roomRateData['profitType']));
        return $rate;
    }
    private function buildCancelationPolicyType() {
        $policyType = $this->createMock(CancellationPolicyType::class);
        $policyType->expects($this->any())
            ->method('getSlug')
            ->willReturn('nights');
        return $policyType;
    }
    private function buildCancelationPolicy() {
        $policy = $this->createMock(CancellationPolicy::class);
        $policy->expects($this->any())
            ->method('getCutOff')
            ->willReturn(0);
        $policy->expects($this->any())
            ->method('getPenalty')
            ->willReturn(0);
        $policy->expects($this->any())
            ->method('getCancellationPolicyType')
            ->willReturn($this->buildCancelationPolicyType());

        return $policy;

    }
    private function buildDate($theDate, $dateId, $dateData, $roomRateData) {
        $date = new Date;
        $this->setProtectedProperty( $date, 'date', $theDate);
        $this->setProtectedProperty( $date, 'id', $dateId);
        $this->setProtectedProperty( $date, 'cancellationPolicy', $this->buildCancelationPolicy());
        $this->setProtectedProperty( $date, 'stopSell', false);
        $this->setProtectedProperty( $date, 'premiumDate', false);
        $this->setProtectedProperty( $date, 'mondayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'tuesdayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'wednesdayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'thursdayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'fridayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'saturdayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'sundayRate', $this->buildRate($roomRateData));
        $this->setProtectedProperty( $date, 'cutOff', $dateData['cutOff']);
        $this->setProtectedProperty( $date, 'stock', $dateData['stock']);

        return $date;
    }
    private function buildDates($search, $dateData, $roomRateData) {
        $dates = [];
        $dateId = 1;

        $checkIn        = new \DateTime($search->getCheckIn());
        $checkOut       = new \DateTime($search->getCheckOut());
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($checkIn, $interval, $checkOut);
        foreach ($period as $dt) {
            $dates [] = $this->buildDate($dt, $dateId, $dateData, $roomRateData);
            $dateId = $dateId + 1;
        }

        return $dates;
    }
    private function buildCurrency() {
        $currency = $this->createMock(HotelCurrency::class);
        $currency->expects($this->any())
            ->method('getSymbol')
            ->willReturn('$');
        return $currency;
    }
    private function buildHotels() {
        $hotel = $this->createMock(Hotel::class);
        $hotel->expects($this->any())
            ->method('getCurrency')
            ->willReturn($this->buildCurrency());

        $hotel->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $hotel->expects($this->any())
            ->method('getMandatoryServicesTotal')
            ->willReturn(0);
        $hotel->expects($this->any())
            ->method('getMandatoryServices')
            ->willReturn(array());
        $hotel->expects($this->any())
            ->method('getName')
            ->willReturn('Hotel name');
        $hotel->expects($this->any())
            ->method('getAddress')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getCoords')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getSubtitle')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getStars')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getCity')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getState')
            ->willReturn('');
        $hotel->expects($this->any())
            ->method('getChildAge')
            ->willReturn(12);
        $hotel->expects($this->any())
            ->method('getImages')
            ->willReturn([]);

        $hotels = [];
        $hotels[] = $hotel;
        return $hotels;
    }
    private function buildRooms($hotel) {
        $room = $this->createMock(Room::class);
        $room->expects($this->any())
            ->method('getHotel')
            ->willReturn($hotel);
        $room->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $room->expects($this->any())
            ->method('getMandatoryServicesTotal')
            ->willReturn(0);
        $room->expects($this->any())
            ->method('getMandatoryServices')
            ->willReturn(array());
        $room->expects($this->any())
            ->method('getCapacity')
            ->willReturn(3);
        $room->expects($this->any())
            ->method('getCombinablePromotions')
            ->willReturn([]);
        $room->expects($this->any())
            ->method('getNonCombinablePromotions')
            ->willReturn([]);
        $room->expects($this->any())
            ->method('getName')
            ->willReturn('One Bedroom Apartment');

        $rooms = [];
        $rooms[] = $room;
        return $rooms;
    }
    private function buildUser($userData) {
        $agency = $this->createMock(Agency::class);
        $agency->expects($this->any())
            ->method('getCommission')
            ->will($this->returnValue($userData['commission']));

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('getAgency')
            ->will($this->returnValue($agency));

        return $user;
    }
    private function buildEntityManager($entityManagerData) {

        $roomsRepositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository\RoomRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByHotelAndCapacity'))
            ->getMock();
        $roomsRepositoryMock->expects( $this->any() )
            ->method( 'findByHotelAndCapacity' )
            ->willReturn($entityManagerData['rooms']);

        $hotelsRepositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository\HotelRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy'))
            ->getMock();
        $hotelsRepositoryMock->expects( $this->any() )
            ->method( 'findBy' )
            ->willReturn($entityManagerData['hotels']);

        $datesRepositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository\DateRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findDatesByRoomAndDays'))
            ->getMock();
        $datesRepositoryMock->expects( $this->any() )
            ->method( 'findDatesByRoomAndDays' )
            ->willReturn($entityManagerData['dates']);

        $entityManager = $this
            ->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->with($this->anything())
            ->will($this->returnCallback(
                function($entityName) use ($roomsRepositoryMock,$hotelsRepositoryMock,$datesRepositoryMock) {

                    if ($entityName === 'GushhCoreBundle:Room')
                        return $roomsRepositoryMock;
                    if ($entityName === 'GushhCoreBundle:Hotel')
                        return $hotelsRepositoryMock;
                    if ($entityName === 'GushhCoreBundle:Date')
                        return $datesRepositoryMock;
                }
            ));

        return $entityManager;
    }

    // ==================================
    // TESTS
    // ==================================

    // ----------------------------------
    // Will check room price, and available
    // Promotion: none
    // Stock: ok
    // Cutoff: ok
    // ----------------------------------
    public function testRoomPricesWithStockOk()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 5;
        $stock = 1;
        $start = 10;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = Carbon::today()->addDays($nights + $start);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $hotels = $this->buildHotels();
        $rooms = $this->buildRooms($hotels[0]);
        $dates = $this->buildDates($search, $testData['dateData'], $testData['rateData']);
        $testData['entityManagerData'] = ['pax' => $pax, 'rooms' => $rooms, 'dates' => $dates, 'hotels' => $hotels ];
        $em = $this->buildEntityManager($testData['entityManagerData']);
        $searchCore = new SearchCore($search, $user, $em);
        $hotelCOunt = 0;
        $returnHotels = $searchCore->findHotels($hotelCOunt);
        $this->assertTrue($returnHotels != []);
        foreach ($returnHotels as $hotel) {
            $this->assertTrue($hotel['rooms'] != []);
            foreach ($hotel['rooms'] as $room) {

                // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
                // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

                // Price (per night) is: RoomCost + ( profit/100 x RoomCost ) - taxes
                // Ends being: 351.75 + ( 25/100 x 351.75 )
                //           = 1.25 x 351.75 = 439.6875 => 439.69

                // Total (per stay) is: Price per night x nights
                // Ends being: 5 x 439.69 = 2198.44
                $this->assertEquals('2,198.45', number_format($room['total'], 2, '.', ','));

                // Taxes (per day ) is: taxes x roomPrice + occupancyTax
                // Ends being: 14.75/100 x 300 + 7.5 = 51.75

                // Taxes total: nights x taxes per night = 5 x 51.75 = 258.75
                $this->assertEquals('258.75', number_format($room['taxes'], 2, '.', ','));

                // Price: total - total taxes
                // Ends being: 2198.44 - 258.75 = 1,939.70
                $this->assertEquals('1,939.70', number_format($room['price'], 2, '.', ','));

                // Mandatory sesvices is 0 in this case
                $this->assertEquals('0', number_format($room['mandatoryServicesTotal'], 2, '.', ','));

                // Comission is: Total x comission/100
                // Ends being: 2198.44 x 10/100 = 219.85
                $this->assertEquals('219.85', number_format($room['commission'], 2, '.', ','));

                // Total with mandatory services is: total + mandatory services - $commission
                // Ends being: 2198.44 - 219.85 = 1978.61
                $this->assertEquals('1,978.60', number_format($room['totalWithMandatoryServices'], 2, '.', ','));

                $this->assertEquals(false, $room['isPromotion']);
                $this->assertEquals(false, $room['onRequest']);

                foreach ($room['stayDates'] as $date) {
                    $this->assertEquals('387.94', $date['price']);
                    $this->assertEquals('51.75', $date['tax']);
                    $this->assertEquals('439.69', $date['total']);
                    $this->assertEquals('439.69', $date['totalWithServices']);
                    $this->assertEquals('43.97', $date['commission']);
                    $this->assertEquals('395.72', $date['totalWithServicesAndCommission']);
                    $this->assertEquals(false, $date['onRequest']);
                    $this->assertEquals(false, $date['dateWithDiscount']);
                    $this->assertEquals(false, $date['isObject']);
                    $this->assertEquals(false, $date['isFree']);
                }
            }
        }
    }

    // ----------------------------------
    // Will check room availability, must say onRequest
    // Promotion: none
    // Stock: not available
    // Cutoff: ok
    // ----------------------------------
    public function testRoomAvailabilityWithNoStock()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 5;
        $stock = 0;
        $checkIn = Carbon::today()->addDays(1);
        $checkOut = Carbon::today()->addDays($nights + 1);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $hotels = $this->buildHotels();
        $rooms = $this->buildRooms($hotels[0]);
        $dates = $this->buildDates($search, $testData['dateData'], $testData['rateData']);
        $testData['entityManagerData'] = ['pax' => $pax, 'rooms' => $rooms, 'dates' => $dates, 'hotels' => $hotels ];
        $em = $this->buildEntityManager($testData['entityManagerData']);
        $searchCore = new SearchCore($search, $user, $em);

        $hotelCOunt = 0;
        $returnHotels = $searchCore->findHotels($hotelCOunt);
        $this->assertTrue($returnHotels != []);
        foreach ($returnHotels as $hotel) {
            $this->assertTrue($hotel['rooms'] != []);
            foreach ($hotel['rooms'] as $room) {

                // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
                // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

                // Price (per night) is: RoomCost + ( profit/100 x RoomCost ) - taxes
                // Ends being: 351.75 + ( 25/100 x 351.75 )
                //           = 1.25 x 351.75 = 439.6875 => 439.69

                // Total (per stay) is: Price per night x nights
                // Ends being: 5 x 439.69 = 2198.44
                $this->assertEquals('2,198.45', number_format($room['total'], 2, '.', ','));

                // Taxes (per day ) is: taxes x roomPrice + occupancyTax
                // Ends being: 14.75/100 x 300 + 7.5 = 51.75

                // Taxes total: nights x taxes per night = 5 x 51.75 = 258.75
                $this->assertEquals('258.75', number_format($room['taxes'], 2, '.', ','));

                // Price: total - total taxes
                // Ends being: 2198.44 - 258.75 = 1,939.70
                $this->assertEquals('1,939.70', number_format($room['price'], 2, '.', ','));

                // Mandatory sesvices is 0 in this case
                $this->assertEquals('0', number_format($room['mandatoryServicesTotal'], 2, '.', ','));

                // Comission is: Total x comission/100
                // Ends being: 2198.44 x 10/100 = 219.85
                $this->assertEquals('219.85', number_format($room['commission'], 2, '.', ','));

                // Total with mandatory services is: total + mandatory services - $commission
                // Ends being: 2198.44 - 219.85 = 1978.61
                $this->assertEquals('1,978.60', number_format($room['totalWithMandatoryServices'], 2, '.', ','));

                $this->assertEquals(false, $room['isPromotion']);
                $this->assertEquals(true, $room['onRequest']);

                foreach ($room['stayDates'] as $date) {
                    $this->assertEquals('387.94', $date['price']);
                    $this->assertEquals('51.75', $date['tax']);
                    $this->assertEquals('439.69', $date['total']);
                    $this->assertEquals('439.69', $date['totalWithServices']);
                    $this->assertEquals('43.97', $date['commission']);
                    $this->assertEquals('395.72', $date['totalWithServicesAndCommission']);
                    $this->assertEquals(true, $date['onRequest']);
                    $this->assertEquals(false, $date['dateWithDiscount']);
                    $this->assertEquals(false, $date['isObject']);
                    $this->assertEquals(false, $date['isFree']);
                }
            }
        }
    }

    // ----------------------------------
    // Will check romm price, onRequest for stay, and onRequest first 3 days
    // Promotion: none
    // Stock: stock available from 3rd day on
    // Cutoff: ok
    // ----------------------------------
    public function testRoomWithStockOkFromThirdDayOn()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 5;
        $stock = 1;
        $start = 10;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = Carbon::today()->addDays($nights + $start);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $hotels = $this->buildHotels();
        $rooms = $this->buildRooms($hotels[0]);
        $dates = $this->buildDates($search, $testData['dateData'], $testData['rateData']);

        // Fix stock to be 0 first 3 days
        $dates[0]->setStock(0);
        $dates[1]->setStock(0);
        $dates[2]->setStock(0);

        $testData['entityManagerData'] = ['pax' => $pax, 'rooms' => $rooms, 'dates' => $dates, 'hotels' => $hotels ];
        $em = $this->buildEntityManager($testData['entityManagerData']);
        $searchCore = new SearchCore($search, $user, $em);

        $hotelCOunt = 0;
        $returnHotels = $searchCore->findHotels($hotelCOunt);
        $this->assertTrue($returnHotels != []);
        foreach ($returnHotels as $hotel) {
            $this->assertTrue($hotel['rooms'] != []);
            foreach ($hotel['rooms'] as $room) {

                // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
                // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

                // Price (per night) is: RoomCost + ( profit/100 x RoomCost ) - taxes
                // Ends being: 351.75 + ( 25/100 x 351.75 )
                //           = 1.25 x 351.75 = 439.6875 => 439.69

                // Total (per stay) is: Price per night x nights
                // Ends being: 5 x 439.69 = 2198.44
                $this->assertEquals('2,198.45', number_format($room['total'], 2, '.', ','));

                // Taxes (per day ) is: taxes x roomPrice + occupancyTax
                // Ends being: 14.75/100 x 300 + 7.5 = 51.75

                // Taxes total: nights x taxes per night = 5 x 51.75 = 258.75
                $this->assertEquals('258.75', number_format($room['taxes'], 2, '.', ','));

                // Price: total - total taxes
                // Ends being: 2198.44 - 258.75 = 1,939.70
                $this->assertEquals('1,939.70', number_format($room['price'], 2, '.', ','));

                // Mandatory sesvices is 0 in this case
                $this->assertEquals('0', number_format($room['mandatoryServicesTotal'], 2, '.', ','));

                // Comission is: Total x comission/100
                // Ends being: 2198.44 x 10/100 = 219.85
                $this->assertEquals('219.85', number_format($room['commission'], 2, '.', ','));

                // Total with mandatory services is: total + mandatory services - $commission
                // Ends being: 2198.44 - 219.85 = 1978.60
                $this->assertEquals('1,978.60', number_format($room['totalWithMandatoryServices'], 2, '.', ','));

                $this->assertEquals(false, $room['isPromotion']);
                $this->assertEquals(true, $room['onRequest']);

                foreach ($room['stayDates'] as $date) {
                    $this->assertEquals('387.94', $date['price']);
                    $this->assertEquals('51.75', $date['tax']);
                    $this->assertEquals('439.69', $date['total']);
                    $this->assertEquals('439.69', $date['totalWithServices']);
                    $this->assertEquals('43.97', $date['commission']);
                    $this->assertEquals('395.72', $date['totalWithServicesAndCommission']);
                    $this->assertEquals($date['id'] < 4, $date['onRequest']);
                    $this->assertEquals(false, $date['dateWithDiscount']);
                    $this->assertEquals(false, $date['isObject']);
                    $this->assertEquals(false, $date['isFree']);
                }
            }
        }
    }

    // ----------------------------------
    // Will check romm price, must say onRequest
    // Promotion: none
    // Stock: Ok
    // Cutoff: inside
    // ----------------------------------
    public function testRoomAvailabilityWithStockOkButInsideCutoff()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 5;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = Carbon::today()->addDays($nights + $start);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $hotels = $this->buildHotels();
        $rooms = $this->buildRooms($hotels[0]);
        $dates = $this->buildDates($search, $testData['dateData'], $testData['rateData']);

        $testData['entityManagerData'] = ['pax' => $pax, 'rooms' => $rooms, 'dates' => $dates, 'hotels' => $hotels ];
        $em = $this->buildEntityManager($testData['entityManagerData']);
        $searchCore = new SearchCore($search, $user, $em);

        $hotelCOunt = 0;
        $returnHotels = $searchCore->findHotels($hotelCOunt);
        $this->assertTrue($returnHotels != []);
        foreach ($returnHotels as $hotel) {
            $this->assertTrue($hotel['rooms'] != []);
            foreach ($hotel['rooms'] as $room) {

                // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
                // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

                // Price (per night) is: RoomCost + ( profit/100 x RoomCost ) - taxes
                // Ends being: 351.75 + ( 25/100 x 351.75 )
                //           = 1.25 x 351.75 = 439.6875 => 439.69

                // Total (per stay) is: Price per night x nights
                // Ends being: 5 x 439.69 = 2198.44
                $this->assertEquals('2,198.45', number_format($room['total'], 2, '.', ','));

                // Taxes (per day ) is: taxes x roomPrice + occupancyTax
                // Ends being: 14.75/100 x 300 + 7.5 = 51.75

                // Taxes total: nights x taxes per night = 5 x 51.75 = 258.75
                $this->assertEquals('258.75', number_format($room['taxes'], 2, '.', ','));

                // Price: total - total taxes
                // Ends being: 2198.44 - 258.75 = 1,939.70
                $this->assertEquals('1,939.70', number_format($room['price'], 2, '.', ','));

                // Mandatory sesvices is 0 in this case
                $this->assertEquals('0', number_format($room['mandatoryServicesTotal'], 2, '.', ','));

                // Comission is: Total x comission/100
                // Ends being: 2198.44 x 10/100 = 219.85
                $this->assertEquals('219.85', number_format($room['commission'], 2, '.', ','));

                // Total with mandatory services is: total + mandatory services - $commission
                // Ends being: 2198.44 - 219.85 = 1978.60
                $this->assertEquals('1,978.60', number_format($room['totalWithMandatoryServices'], 2, '.', ','));

                $this->assertEquals(false, $room['isPromotion']);
                $this->assertEquals(true, $room['onRequest']);

                foreach ($room['stayDates'] as $date) {
                    $this->assertEquals('387.94', $date['price']);
                    $this->assertEquals('51.75', $date['tax']);
                    $this->assertEquals('439.69', $date['total']);
                    $this->assertEquals('439.69', $date['totalWithServices']);
                    $this->assertEquals('43.97', $date['commission']);
                    $this->assertEquals('395.72', $date['totalWithServicesAndCommission']);
                    $this->assertEquals($date['id'] < 3, $date['onRequest']);
                    $this->assertEquals(false, $date['dateWithDiscount']);
                    $this->assertEquals(false, $date['isObject']);
                    $this->assertEquals(false, $date['isFree']);
                }
            }
        }
    }
}