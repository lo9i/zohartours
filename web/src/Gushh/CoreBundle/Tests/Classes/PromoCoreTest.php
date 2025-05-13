<?php
/**
 * Created by PhpStorm.
 * User: anibal
 * Date: 11/14/16
 * Time: 17:35
 */

namespace Tests\Classes;

use Gushh\CoreBundle\Classes\PromoCore;
use Gushh\CoreBundle\Classes\Util;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Entity\Agency;
use Gushh\CoreBundle\Entity\User;
use Gushh\CoreBundle\Entity\PromotionBenefitValueType;
use Gushh\CoreBundle\Entity\PromotionBenefitType;
use Gushh\CoreBundle\Entity\PromotionBenefit;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\PromotionCondition;
use Gushh\CoreBundle\Entity\PromotionConditionConditional;
use Gushh\CoreBundle\Entity\PromotionConditionExpression;
use Gushh\CoreBundle\Entity\PromotionExceptionPeriod;
use Gushh\CoreBundle\Entity\Rate;
use Gushh\CoreBundle\Entity\ProfitType;
use Gushh\CoreBundle\Entity\Date;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Entity\CancellationPolicyType;
use Carbon\Carbon;

use Gushh\CoreBundle\Classes\SearchCore;

class PromoCoreTest extends \PHPUnit_Framework_TestCase
{
    private function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
    private function buildAvailableDays()
    {
        $days = [];
        $days[] = 'Mon';
        $days[] = 'Tue';
        $days[] = 'Wed';
        $days[] = 'Thu';
        $days[] = 'Fri';
        $days[] = 'Sat';
        $days[] = 'Sun';
        return $days;
    }
    private function buildSearch($searchData)
    {
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
    private function buildProfitType($slug)
    {
        $profitType = new ProfitType;
        $this->setProtectedProperty($profitType, 'slug', $slug);
        return $profitType;
    }
    private function buildRate($roomRateData)
    {
        $rate = new Rate;
        $this->setProtectedProperty( $rate, 'price', $roomRateData['price']);
        $this->setProtectedProperty( $rate, 'tax', $roomRateData['tax']);
        $this->setProtectedProperty( $rate, 'profit', $roomRateData['profit']);
        $this->setProtectedProperty( $rate, 'occupancyTax', $roomRateData['occupancyTax']);

        $this->setProtectedProperty( $rate, 'profitType', $this->buildProfitType($roomRateData['profitType']));
        return $rate;
    }
    private function buildCancellationPolicyType($policyTypeData) {
        $cancellationPolicyType = $this->createMock(CancellationPolicyType::class);
        $cancellationPolicyType->expects($this->any())
            ->method('getSlug')
            ->willReturn($policyTypeData);
        return $cancellationPolicyType;
    }
    private function buildCancelationPolicy($policyData)
    {
        $cancellationPolicy = $this->createMock(CancellationPolicy::class);
        $cancellationPolicy->expects($this->any())
            ->method('getCutOff')
            ->willReturn($policyData['cutOff']);
        $cancellationPolicy->expects($this->any())
            ->method('getPenalty')
            ->willReturn(0);

        $cancellationPolicy->expects($this->any())
            ->method('getCancellationPolicyType')
            ->willReturn($this->buildCancellationPolicyType('net'));

        return $cancellationPolicy;
    }
    private function buildDate($theDate, $dateId, $dateData, $roomRateData)
    {
        $date = new Date;
        $this->setProtectedProperty( $date, 'date', $theDate);
        $this->setProtectedProperty( $date, 'id', $dateId);
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
        $this->setProtectedProperty( $date, 'cancellationPolicy', $this->buildCancelationPolicy($dateData));

        return $date;
    }
    private function buildDates($search, $dateData, $rateData) {
        $dates = [];
        $dateId = 1;

        $checkIn        = new \DateTime($search->getCheckIn());
        $checkOut       = new \DateTime($search->getCheckOut());
        $interval       = \DateInterval::createFromDateString('1 day');
        $period         = new \DatePeriod($checkIn, $interval, $checkOut);
        foreach ($period as $dt) {
            $dates [] = $this->buildDate($dt, $dateId, $dateData, $rateData);
            $dateId = $dateId + 1;
        }
        return $dates;
    }
    private function buildRooms($user, $search, $dates, $pax)
    {
        return [
            'id' => 1,
            'name' => 'One Bedroom Apartment',
            'capacity' => 4,
            'stayDates' => SearchCore::buildStayDates($dates, $pax, $search->getNights(), 0, $user->getAgency()->getCommission()),
            'dates' => $dates,
            'mandatoryServices' => '',
            'onRequest' => false,
            'isBlackedOut' => false,
            'isCancellable' => true,
            'isPromotion' => false,
            'adults' => 1,
            'children' => 0
            ];
    }
    private function buildAgency($agencyData) {
        $agency = $this->createMock(Agency::class);
        $agency->expects($this->any())
            ->method('getCommission')
            ->willReturn($agencyData['commission']);
        return $agency;
    }
    private function buildUser($userData)
    {
        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('getAgency')
            ->willReturn($this->buildAgency($userData));

        return $user;
    }
    private function buildBenefits($benefitsData)
    {
        $benefits             = [];

        $valueType = $this->createMock(PromotionBenefitValueType::class);
        $valueType->expects($this->any())
            ->method('getSlug')
            ->willReturn($benefitsData['benefitValueType']);

        $benefitType = $this->createMock(PromotionBenefitType::class);
        $benefitType->expects($this->any())
            ->method('getSlug')
            ->willReturn($benefitsData['promotionType']);

        $benefit = $this->createMock(PromotionBenefit::class);
        $benefit->expects($this->any())
            ->method('getValue')
            ->willReturn($benefitsData['benefitValue']);

        $benefit->expects($this->any())
            ->method('getEachValue')
            ->willReturn($benefitsData['nightsEach']);

        $benefit->expects($this->any())
            ->method('getValueType')
            ->willReturn($valueType);

        $benefit->expects($this->any())
            ->method('getType')
            ->willReturn($benefitType);

        $benefit->expects($this->any())
            ->method('isCumulative')
            ->willReturn($benefitsData['isCumulative']);

        $benefit->expects($this->any())
            ->method('hasLimit')
            ->willReturn($benefitsData['nightsLimit']>0);

        $benefit->expects($this->any())
            ->method('getLimitValue')
            ->willReturn($benefitsData['nightsLimit']);

        $benefits[] = $benefit;
        return $benefits;
    }
    private function buildConditions()
    {
        $conditionConditional = $this->createMock(PromotionConditionConditional::class);
        $conditionConditional->expects($this->any())
            ->method('getSlug')
            ->willReturn('nights');

        $conditionConditionalExpression = $this->createMock(PromotionConditionExpression::class);
        $conditionConditionalExpression->expects($this->any())
            ->method('getSlug')
            ->will($this->returnValue('greater-than-or-equal'));
        // Options are (so far):
        // - 'for-each'
        // - 'equal'
        // - 'not-equal'
        // - 'less-than'
        // - 'less-than-or-equal'
        // - 'greater-than'
        // - 'greater-than-or-equal'

        $condition = $this->createMock(PromotionCondition::class);
        $condition->expects($this->any())
            ->method('getConditional')
            ->will($this->returnValue($conditionConditional));

        $condition->expects($this->any())
            ->method('getExpression')
            ->will($this->returnValue($conditionConditionalExpression));

        $condition->expects($this->any())
            ->method('getValue')
            ->willReturn(4);

        $conditions = [];
        $conditions[] = $condition;
        return $conditions;
    }
    private function buildExceptionPeriod($exception) {

        $exceptionPeriod = new PromotionExceptionPeriod;
        $exceptionPeriod->setPeriodFrom($exception['periodFrom']);
        $exceptionPeriod->setPeriodTo($exception['periodTo']);
        return $exceptionPeriod;
    }
    private function buildExceptionPeriods($exceptions) {

        $exceptionPeriods = [];
        foreach ( $exceptions as $exception ) {
            $exceptionPeriods [] = $this->buildExceptionPeriod($exception);
        }
        return $exceptionPeriods;
    }
    private function buildPromotions($benefitsData)
    {
        $validOnPremiumDates = [];
        $validOnStopSellDates = [];
        $conditions = $this->buildConditions($benefitsData);
        $promotion = $this->createMock(Promotion::class);
        $promotion->expects($this->any())
            ->method('getBenefits')
            ->willReturn($this->buildBenefits($benefitsData));

        $promotion->expects($this->any())
            ->method('getAvailableDays')
            ->willReturn($this->buildAvailableDays());

        $promotion->expects($this->any())
            ->method('getAvailableInPremiumDates')
            ->willReturn($validOnPremiumDates);

        $promotion->expects($this->any())
            ->method('getAvailableInStopSellDates')
            ->willReturn($validOnStopSellDates);

        $promotion->expects($this->any())
            ->method('getConditions')
            ->willReturn($conditions);

        $promotion->expects($this->any())
            ->method('getComparationConditions')
            ->willReturn($conditions);

        $promotion->expects($this->any())
            ->method('getCombinable')
            ->willReturn($benefitsData['combinable']);

        $promotion->expects($this->any())
            ->method('getName')
            ->willReturn('4th night free');

        $promotion->expects($this->any())
            ->method('getValidFrom')
            ->willReturn('2015-01-01 00:00:00');

        $promotion->expects($this->any())
            ->method('getValidTo')
            ->willReturn('2018-01-01 00:00:00');

        $promotion->expects($this->any())
            ->method('getPeriodFrom')
            ->willReturn($benefitsData['from']);

        $promotion->expects($this->any())
            ->method('getPeriodTo')
            ->willReturn($benefitsData['to']);

        if (isset($benefitsData['exceptions'])) {
            $promotion->expects($this->any())
                ->method('getExceptions')
                ->willReturn($this->buildExceptionPeriods($benefitsData['exceptions']));
        }
        $promotions = [];
        $promotions[] = $promotion;
        return $promotions;
    }

    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercent()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = [ 'combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' => 'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 nights
            // Ends being 2 x 439.69 = 879.38
            $this->assertEquals('879.38', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 879.38 = 3,517.52
            $this->assertEquals('3,517.52', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + occupancyTax
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 4396.90 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being:  3,517.52 x 10/100 =  351.76
            $this->assertEquals('351.76', number_format($promoRoom['commission'], 2, '.', ','));


            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,517.52 - 351.76 = 3,165.76
            $this->assertEquals('3,165.76', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);

            foreach ($promoRoom['stayDates'] as $date) {
                if($date['id'] == 4 || $date['id'] == 8 ) {
                    $this->assertEquals('0.00', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(true   , $date['isFree']);
                    $this->assertEquals(true    , $date['dateWithDiscount']);
                }
                else {
                    $this->assertEquals('387.94', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('51.75', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('439.69', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(false   , $date['isFree']);
                    $this->assertEquals(false    , $date['dateWithDiscount']);
                }
//                $this->assertEquals(false, $date['onRequest']);
                $this->assertEquals(false   , $date['isObject']);
            }
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   Limit: 1 night only
    // ----------------------------------
    public function testOneNightOffAfter3LimitOneNightPercent()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];
        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.73
            $this->assertEquals('395.73', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.72 = 3,561.48
            $this->assertEquals('3,561.48', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);

            foreach ($promoRoom['stayDates'] as $date) {
                if($date['id'] == 4 ) {
                    $this->assertEquals('0.00', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(true   , $date['isFree']);
                    $this->assertEquals(true    , $date['dateWithDiscount']);
                }
                else {
                    $this->assertEquals('387.94', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('51.75', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('439.69', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(false   , $date['isFree']);
                    $this->assertEquals(false    , $date['dateWithDiscount']);
                }
//                $this->assertEquals(false, $date['onRequest']);
                $this->assertEquals(false   , $date['isObject']);
            }

        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   net
    //   No limit
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitNet()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'net', 'benefitValue'=> 50.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 x benefit net
            // Ends being 2 x [(300 - 50) x 1.1475 + 7.5] x 1.25 = 143.44
            $this->assertEquals('143.44', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - promo discount
            // Ends being: 10 x 439.69 - 143.44 = 4,253.46
            $this->assertEquals('4,253.46', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes per day (w/o promotion) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75
            // Taxes per day (w/ promotion) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 250 + 7.5 = 44.375

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: [Price per night w/o discount - (Taxes of price w/o discount)] x nights
            // Ends being: 4396.9 - 517.50 = 3982.90
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 4,253.46 x 10/100 = 425.36
            $this->assertEquals('425.36', number_format($promoRoom['commission'], 2, '.', ','));

            // Total is: Total - commission
            // Ends being: 4,253.46 - 425.36 = 3,828.114
            $this->assertEquals('3,828.10', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   net
    //   Limit: 1 night only
    // ----------------------------------
    public function testOneNightOffAfter3LimitOneNightNet()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'net', 'benefitValue'=> 50.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 x benefit net x taxes
            // Ends being (300 - 25) x taxes x profit = 71.72
            $this->assertEquals('71.72', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - promo discount
            // Ends being: 10 x 439.69 - 71.72 = 4325.18
            $this->assertEquals('4,325.18', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 4325.18 x 10/100 = 432.53
            $this->assertEquals('432.53', number_format($promoRoom['commission'], 2, '.', ','));

            // Total is: Total - commission
            // Ends being: 4325.18 - 432.52 = 3892.662
            $this->assertEquals('3,892.65', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Non Combinable
    //   percent
    //   No limit
    // ----------------------------------
    public function testNonCombOneNightOffAfter3NoLimitPercent()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['nonpromotionData'] = ['combinable' => false, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = $this->buildPromotions($testData['nonpromotionData']);
        $combinablePromotions = [];
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 nights
            // Ends being 2 x 439.69 = 879.38
            $this->assertEquals('879.38', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 879.38 = 3,517.52
            $this->assertEquals('3,517.52', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 4396.90 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,517.52 x 10/100 = 351.75
            $this->assertEquals('351.76', number_format($promoRoom['commission'], 2, '.', ','));


            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,517.52 - 351.75 = 3165.768 => 3,165.76
            $this->assertEquals('3,165.76', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Non Combinable
    //   percent
    //   Limit: 1 night only
    // ----------------------------------
    public function testNonCombOneNightOffAfter3LimitOneNightPercent()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['userData'] = ['commission' => 10 ];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['nonpromotionData'] = ['combinable' => false, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = $this->buildPromotions($testData['nonpromotionData']);
        $combinablePromotions = [];
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 4396.90 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.72
            $this->assertEquals('395.73', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.44 = 3561.49
            $this->assertEquals('3,561.48', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Non Combinable
    //   net
    //   No limit
    // ----------------------------------
    public function testNonCombOneNightOffAfter3NoLimitNet()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['nonpromotionData'] = ['combinable' => false, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'net', 'benefitValue'=> 50.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = $this->buildPromotions($testData['nonpromotionData']);
        $combinablePromotions = [];
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 x benefit net
            // Ends being 2 x 50 x tax x profit = 143.44
            $this->assertEquals('143.44', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - promo discount
            // Ends being: 10 x 439.69 - 143.44 = 4253.46
            $this->assertEquals('4,253.46', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 4253.46 x 10/100 = 425.36
            $this->assertEquals('425.36', number_format($promoRoom['commission'], 2, '.', ','));

            // Total is: Total - commission
            // Ends being: 4253.46 - 425.34 = 3828.114
            $this->assertEquals('3,828.10', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Non Combinable
    //   net
    //   Limit: 1 night only
    // ----------------------------------
    public function testNonCombOneNightOffAfter3LimitOneNightNet()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['nonpromotionData'] = ['combinable' => false, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'net', 'benefitValue'=> 50.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = $this->buildPromotions($testData['nonpromotionData']);
        $combinablePromotions = [];
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 x benefit net
            // Ends being 1 x 50 x tax x profit = 71.72
            $this->assertEquals('71.72', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - promo discount
            // Ends being: 10 x 439.69 - 71.72 = 4325.18
            $this->assertEquals('4,325.18', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 4325.18 x 10/100 = 432.53
            $this->assertEquals('432.53', number_format($promoRoom['commission'], 2, '.', ','));

            // Total is: Total - commission
            // Ends being: 4325.18 - 434.69 = 3892.662
            $this->assertEquals('3,892.65', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   Start of search is before promotion start but there is overlap enough
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentStart()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;

        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkIn->copy()->addDays(4);
        $promoEnd = $promoStart->copy()->addDays(20);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 4396.90 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.72
            $this->assertEquals('395.73', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.44 = 3561.49
            $this->assertEquals('3,561.48', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   End of search is after promotion ends but there is overlap enough
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentEnd()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 4;

        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(10);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.72
            $this->assertEquals('395.73', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.44 = 3561.49
            $this->assertEquals('3,561.48', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(false, $promoRoom['onRequest']);
        }
    }
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   Start of search is before promotion start and there is no overlap
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentOutside()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 4;

        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkOut->addDays(5);
        $promoEnd = $promoStart->copy()->addDays(40);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $promos     = $promoCore->findPromotions();

        $this->assertTrue($promos == []);
    }
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   Start of search is before promotion start and there is overlap but not enough days to apply promotion
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentOverlapNotEnoughDays()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 4;

        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkOut->copy()->addDays(-3);
        $promoEnd = $promoStart->copy()->addDays(10);

        $testData = [];
        $testData['searchData'] = ['nights' => 7, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => 1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $promos     = $promoCore->findPromotions();

        $this->assertTrue($promos == []);
    }
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   End of search is after promotion ends but there is just enough overlap to get one night off
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentEndJustEnough()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 4;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkOut->copy()->addDays(-14);
        $promoEnd = $promoStart->copy()->addDays(10);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.72
            $this->assertEquals('395.72', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.44 = 3561.49
            $this->assertEquals('3,561.49', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(false, $promoRoom['onRequest']);
        }
    }
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    //   Start of search is before promotion starts but there is just enough overlap to get one night off
    // ----------------------------------
    public function testOneNightOffAfter3NoLimitPercentStartJustEnough()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkOut->copy()->addDay(-4);
        $promoEnd = $promoStart->copy()->addDays(10);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' =>  'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 1 night
            // Ends being 439.6875 =>  439.69
            $this->assertEquals('439.69', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 439.69 = 3,957.21
            $this->assertEquals('3,957.21', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + ocuppancy
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 4396.90 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,957.21 x 10/100 = 395.72
            $this->assertEquals('395.73', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,957.21 - 395.44 = 3561.49
            $this->assertEquals('3,561.48', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);
        }
    }

    public function testStayDiscountPercent() {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;

        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = $checkIn->copy()->addDay(-4);
        $promoEnd = $checkOut->copy()->addDays(4);

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.5, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = ['combinable' => true, 'promotionType' => 'stay-discount', 'isCumulative' => true, 'nightsEach' => 1, 'nightsLimit' => -1, 'benefitValueType' =>  'percent', 'benefitValue'=> 10.00, 'from' => $promoStart, 'to' => $promoEnd];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions    = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {
            $this->assertTrue($promoRoom != []);

            // Promo discount x night s: Price Net x Promotion
            // Ends being: Room net x 0.1 = 300 X 0.1 = 30

            // Promo discount total = nights X promo discount per night
            // Ends being: 10 X 30  x tax x profit = 430.30
            $this->assertEquals('430.30', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Room with discount per night: Room Net - promo discount
            // Ends being: 300 - 30 = 270

            // Room tax (per night) is: (Room with discount per night) x tax + ocuppancyTax
            // Ends being: (270) x (14.75/100) + 7.5  = 270 x 0.1475 + 7.5 = 47.325

            // RoomCost per night = Room with discount + Room Tax
            // Ends being: 270 + 47.325 = 317.32

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 317.32 x ( 1 + 25/100 )
            //           = 1.25 x 317.32 = 396.66

            // Total (per stay) is: Price per night x nights
            // Ends being: 10 x 396.66 = 3,966.60
            $this->assertEquals('3,966.60', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being: 3,966.60 x 10/100 = 396.70
            $this->assertEquals('396.70', number_format($promoRoom['commission'], 2, '.', ','));

            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,966.60 - 396.70 = 3,569.90
            $this->assertEquals('3,569.90', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);

            foreach ($promoRoom['stayDates'] as $date) {
                $this->assertEquals('349.33', number_format($date['price'], 2, '.', ','));
                $this->assertEquals('47.33' , number_format($date['tax'], 2, '.', ','));
                $this->assertEquals('396.66', number_format($date['total'], 2, '.', ','));
//                $this->assertEquals(false, $date['onRequest']);
                $this->assertEquals(true    , $date['dateWithDiscount']);
                $this->assertEquals(false   , $date['isObject']);
                $this->assertEquals(false   , $date['isFree']);
            }
        }
    }
    // ----------------------------------
    // Will check room promotion:
    // Promotion: 1 night off
    //   Condition: after 3 nights
    //   Combinable
    //   percent
    //   No limit
    // ----------------------------------
    public function testExceptionPeriod()
    {
        $adults = 2;
        $children = 0;
        $pax = $adults + $children;
        $nights = 10;
        $stock = 1;
        $start = 1;
        $checkIn = Carbon::today()->addDays($start);
        $checkOut = $checkIn->copy()->addDays($nights);

        $promoStart = Carbon::today();
        $promoEnd = $promoStart->copy()->addDays(20);

        $exceptions = [];
        $periodFrom = $checkIn->copy()->addDays(1);
        $exceptions[] = ['periodFrom' => $periodFrom, 'periodTo' => $periodFrom];

        $testData = [];
        $testData['searchData'] = ['nights' => $nights, 'adults' => $adults, 'children' => $children, 'checkIn' => $checkIn, 'checkOut' => $checkOut, 'rooms' => 1];
        $testData['rateData'] = ['price' => 300.00, 'tax' => 14.75, 'occupancyTax' => 7.50, 'profit' => 25.00, 'profitType' => 'percent'];
        $testData['dateData'] = ['cutOff' => 3, 'stock' => $stock];
        $testData['userData'] = ['commission' => 10 ];
        $testData['promotionData'] = [ 'combinable' => true, 'promotionType' => 'night-discount', 'isCumulative' => true, 'nightsEach' => 4, 'nightsLimit' => -1, 'benefitValueType' => 'percent', 'benefitValue'=> 100.00, 'from' => $promoStart, 'to' => $promoEnd, 'exceptions' => $exceptions];
        $testData['hotel'] = [ 'checkIn' => $checkIn ];

        $search = $this->buildSearch($testData['searchData']);
        $user = $this->buildUser($testData['userData']);
        $dates = $this->buildDates($search, $testData['dateData'],$testData['rateData']);
        $room = $this->buildRooms($user, $search, $dates, $pax);

        $nonCombinablePromotions = [];
        $combinablePromotions = $this->buildPromotions($testData['promotionData']);
        $promoCore = new PromoCore($search, $room, $user, $combinablePromotions, $nonCombinablePromotions);

        $roomsWithPromotions = $promoCore->findPromotions();
        $this->assertTrue($roomsWithPromotions != []);

        SearchCore::calculateDatesAmounts($roomsWithPromotions[0]);
        SearchCore::calculateTotalsForRoom( $testData['hotel'], $roomsWithPromotions[0], $search->getCheckIn(), $search->getNights());

        foreach ($roomsWithPromotions as $promoRoom) {

            $this->assertTrue($promoRoom != []);
            // RoomCost (per night) is: roomPrice + taxes = roomPrice net + ( roomPrice net x tax/100) + occupancyTax + profit
            // Ends being: (300 + (300 x 14.75/100) + 7.5)  = 300 x 1.1475 + 7.5 = 351.75

            // Price (per night) is: RoomCost + ( profit/100 x RoomCost )
            // Ends being: 351.75 + ( 25/100 x 351.75 )
            //           = 1.25 x 351.75 = 439.6875 => 439.69

            // Promo discount is: 2 nights (this is not really the calculation:
            // Really the discount is $ 600 => 2 nights free
            // But we show this to the user since it is what he really saves.
            // Ends being 2 x 439.69 = 879.38
            $this->assertEquals('879.38', number_format($promoRoom['promoDiscount'], 2, '.', ','));

            // Total (per stay) is: Price per night x nights - Total discount
            // Ends being: 10 x 439.69 - 879.38 = 3,517.52
            $this->assertEquals('3,517.52', number_format($promoRoom['total'], 2, '.', ','));

            // Taxes (per day ) is: taxes x roomPrice + occupancyTax
            // Ends being: 14.75/100 x 300 + 7.5 = 51.75

            // Taxes total: nights x taxes per night = 10 x 51.75 = 517.50
            $this->assertEquals('517.50', number_format($promoRoom['taxes'], 2, '.', ','));

            // Price: Price (per night) x nights - Taxes
            // Ends being: 439.69 x 10 - 517.50 = 3,879.40
            $this->assertEquals('3,879.40', number_format($promoRoom['price'], 2, '.', ','));

            // Mandatory sesvices is 0 in this case
            $this->assertEquals('0', number_format($promoRoom['mandatoryServicesTotal'], 2, '.', ','));

            // Comission is: Total x comission/100
            // Ends being:  3,517.52 x 10/100 =  351.76
            $this->assertEquals('351.76', number_format($promoRoom['commission'], 2, '.', ','));


            // totalWithMandatoryServices is: Total - commission
            // Ends being: 3,517.52 - 351.76 = 3,165.76
            $this->assertEquals('3,165.76', number_format($promoRoom['totalWithMandatoryServices'], 2, '.', ','));

            $this->assertEquals(true, $promoRoom['isPromotion']);
            $this->assertEquals(true, $promoRoom['onRequest']);

            foreach ($promoRoom['stayDates'] as $date) {
                if($date['id'] == 5 || $date['id'] == 9 ) {
                    $this->assertEquals('0.00', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('0.00', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(true   , $date['isFree']);
                    $this->assertEquals(true    , $date['dateWithDiscount']);
                }
                else {
                    $this->assertEquals('387.94', number_format($date['price'], 2, '.', ','));
                    $this->assertEquals('51.75', number_format($date['tax'], 2, '.', ','));
                    $this->assertEquals('439.69', number_format($date['total'], 2, '.', ','));
                    $this->assertEquals(false   , $date['isFree']);
                    $this->assertEquals(false    , $date['dateWithDiscount']);
                }
//                $this->assertEquals(false, $date['onRequest']);
                $this->assertEquals(false   , $date['isObject']);
            }
        }
    }

}