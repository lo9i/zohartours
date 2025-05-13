<?php

namespace Gushh\CoreBundle\Classes;

use Gushh\CoreBundle\Classes\SearchCore;
use Gushh\CoreBundle\Classes\Util;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\PromotionExceptionPeriod;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Entity\User;
use Gushh\CoreBundle\Entity\Date;
use Carbon\Carbon;

/**
 * PromoCore
 */
class PromoCore
{
    protected $today;

    protected $combinablePromotions;

    protected $nonCombinablePromotions;

    protected $search;

    protected $room;

    protected $user;

    protected $adults;

    protected $children;

    protected $pax;

    protected $nights;

    protected $checkIn;

    protected $daysUntilCheckIn;

    public function __construct(Search $search, array $room, $user, array $combinablePromotions = [], array $nonCombinablePromotions = [])
    {
        $this->today                   = Carbon::today();
        $this->combinablePromotions    = $combinablePromotions;
        $this->nonCombinablePromotions = $nonCombinablePromotions;
        $this->search                  = $search;
        $this->room                    = $room;
        $this->user                    = $user;

        $this->adults                  = $this->room['adults'];
        $this->children                = $this->room['children'];
        $this->pax                     = $this->adults + $this->children;

        $this->checkIn                 = new Carbon($this->search->getCheckIn());
        $this->daysUntilCheckIn        = $this->getDaysUntilCheckIn($this->checkIn);
        $this->nights                  = $search->getNights();
    }

    public function getDaysUntilCheckIn(Carbon $date)
    {
        return $this->today->diffInDays($date);
    }

    /**
    * Get search
    * @return Search
    */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Find Promotions
     *
     * @method   findPromotions
     *
     * @author   Anibal Fernáez   <anibaldfernandez@hotmail.com>
     *
     * @return   array   array_merge($room, $promoRoom);
     */
    public function findPromotions()
    {
        $roomsWithPromotions = [];
        $combinableRoom      = $this->room;

        foreach ($this->combinablePromotions as $combinablePromotion) {
            $appliesPeriod = $this->checkAppliesPeriod($combinablePromotion);
            $validPeriod = $this->checkValidityPeriod($combinablePromotion);
            $conditionsOk = $this->checkConditions($combinablePromotion);
            $cutOffOk = $this->checkCutOff($combinablePromotion);
            if ($validPeriod == true && $appliesPeriod == true && $conditionsOk == true && $cutOffOk == true) {
                $modifiedCombinableRoom = $this->applyBenefits($combinableRoom, $combinablePromotion);
                if($modifiedCombinableRoom)
                    $combinableRoom = $modifiedCombinableRoom;
            }
        }

        if ($combinableRoom != [] && $combinableRoom !== $this->room)
            $roomsWithPromotions[] = $combinableRoom;

        foreach ($this->nonCombinablePromotions as $nonCombinablePromotion) {
            $appliesPeriod = $this->checkAppliesPeriod($nonCombinablePromotion);
            $validPeriod = $this->checkValidityPeriod($nonCombinablePromotion);
            $conditionsOk = $this->checkConditions($nonCombinablePromotion);
            $cutOffOk = $this->checkCutOff($nonCombinablePromotion);

            if ($validPeriod == true && $appliesPeriod == true && $conditionsOk == true  && $cutOffOk == true)
                $roomsWithPromotions[] = $this->applyBenefits($this->room, $nonCombinablePromotion);
        }

        return $roomsWithPromotions;
    }

    /**
     * Check if promotion
     *
     * @method   checkConditions
     *
     * @author   Anibal Fernáez   <anibaldfernandez@hotmail.com>
     *
     * @param    TODO array                  $conditions
     *
     * @return   bool                   $satisfiesConditions = $this->checkConditions(Promotion)
     */
    public function checkConditions($promotion)
    {
        $conditions = $promotion->getComparationConditions();
        if (!$conditions)
            return true;

        $start        = new Carbon($this->search->getCheckIn());
        $end          = new Carbon($this->search->getCheckOut());

        $appliesFrom  = new Carbon($promotion->getPeriodFrom());
        $appliesTo    = new Carbon($promotion->getPeriodTo());

        if( $start < $appliesFrom )
            $start = $appliesFrom;

        if ( $end > $appliesTo )
            $end = $appliesTo;

        $nights = $end->diffInDays($start);

        $conditional['nights']   = $nights; //$this->search->getNights();
        $conditional['adults']   = $this->search->getRoom1Adults();
        $conditional['children'] = $this->search->getRoom1Children();
        $conditional['rooms']    = $this->search->getRooms();
        $conditional['pax']      = $conditional['children'] + $conditional['adults'];

        // CHECK CONDITIONS! OK
        foreach ($conditions as $condition) {
            $conditionConditional = $condition->getConditional()->getSlug();
            $conditionExpression = $condition->getExpression()->getSlug();
            $conditionValue = $condition->getValue();

            if ($conditionExpression == 'for-each')
                continue;

            $ifCondition = [
                'equal'                 => $conditional[$conditionConditional] == $conditionValue,
                'not-equal'             => $conditional[$conditionConditional] != $conditionValue,
                'less-than'             => $conditional[$conditionConditional] <  $conditionValue,
                'less-than-or-equal'    => $conditional[$conditionConditional] <= $conditionValue,
                'greater-than'          => $conditional[$conditionConditional] >  $conditionValue,
                'greater-than-or-equal' => $conditional[$conditionConditional] >= $conditionValue
            ];

            if (!$ifCondition[$conditionExpression])
                return false;
        }

        return true;
    }

    /**
     * Check if promotion
     *
     * @method   checkCutOff
     *
     * @author   Anibal Fernáez   <anibaldfernandez@hotmail.com>
     *
     * @param    Promotion             $promotion   [description]
     *
     * @return   [boolean]
     */
    public function checkCutOff(Promotion $promotion)
    {
        $days = $promotion->getCutOff();
        if($days <= 0)
            return true;

        return $this->daysUntilCheckIn >= $days;
    }

    /**
     * Check if promotion
     *
     * @method   checkValidityPeriod
     *
     * @author   Anibal Fernáez   <anibaldfernandez@hotmail.com>
     *
     * @param    Promotion             $promotion   [description]
     *
     * @return   [type]                             [description]
     */
    public function checkValidityPeriod(Promotion $promotion)
    {
        $validFrom      = new \DateTime($promotion->getValidFrom());
        $validTo        = new \DateTime($promotion->getValidTo());

        return ($this->today >= $validFrom && $this->today <= $validTo);
    }

    /**
     * [checkAppliesPeriod description]
     *
     * @method   checkAppliesPeriod
     *
     * @author   Anibal Fernáez   <anibaldfernandez@hotmail.com>
     *
     * @param    Promotion   $promotion   [description]
     *
     * @return   bool
     */
    public function checkAppliesPeriod(Promotion $promotion)
    {
        $checkIn        = new \DateTime($this->search->getCheckIn());
        $checkOut       = new \DateTime($this->search->getCheckOut());

        $appliesFrom    = new \DateTime($promotion->getPeriodFrom());
        $appliesTo      = new \DateTime($promotion->getPeriodTo());

        return ($checkIn <= $appliesTo && $checkOut >= $appliesFrom);
    }

    public function applyBenefits($room, Promotion $promotion)
    {
        $stayDates            = $room['stayDates'];
        $promoBenefits        = $promotion->getBenefits();
        $promoAvailableDays   = $promotion->getAvailableDays();
        $promoExceptions      = $promotion->getExceptions();
        $availableDatesKeys   = $this->availableDatesKeys($stayDates, $promoAvailableDays, $promotion);
        $availableDatesCount  = count($availableDatesKeys);

        // If count <= 0 or no benefits, don't try to do anything else.
        if ($availableDatesCount <= 0 || !$promoBenefits)
            return [];

        $promotionCode        = isset($room['promotion']) ? $room['promotion'] . ' + ' . $promotion->getName() : $promotion->getName();
        $benefitsData         = [
                                 'promoCode'         => $promotionCode,
                                 'daysCount'         => $availableDatesCount,
                                 'dates'             => $room['dates'],
                                 'stayDates'         => $stayDates,
                                 'datesKeys'         => $availableDatesKeys,
                                 'anyDiscountApplied'=> false
                                ];

        foreach ($promoBenefits as $benefit) {
            switch ($benefit->getType()->getSlug()) {
                case 'stay-discount':
                    $this->applyStaydiscount($benefitsData, $benefit, $promoExceptions);
                    break;
                case 'night-discount':
                    $this->applyNightDiscount($benefitsData, $benefit, $promoExceptions);
                    break;
                case 'room-service-discount':
                case 'hotel-service-discount':
                    $this->applyServiceDiscount($benefitsData, $benefit, $promoExceptions);
                break;
                default:
                    die('Unknown discount type');
            }
        }

        if ($benefitsData['anyDiscountApplied'] == false)
            return [];

        $promoIds = isset($room['promoIds']) == true ? $room['promoIds'] : [];
        $promoIds[] = $promotion->getId();
        $roomPromotionPush = [
            'stayDates'      => $benefitsData['stayDates'],
            'promotion'      => $promotionCode,
            'isPromotion'    => true,
            'onRequest'      => SearchCore::isOnRequest($stayDates, $promoAvailableDays, $this->search),
            'promoDiscount'  => 0,
            'promoDiscountNet' => 0,
            'promoIds'       => $promoIds,
            'isCancellable'  => ($promotion->getNonRefundable() == true) ? false : $room['isCancellable']
        ];

        return array_merge($room, $roomPromotionPush);
    }

    private function promotionAppliesToDate(Date &$date, &$exceptions, &$dateKeys) {
        if (!in_array($date->getId(), $dateKeys))
            return false;

        if(!$exceptions)
            return true;

        foreach ($exceptions as $exception) {
            $periodFrom = new Carbon($exception->getPeriodFrom());
            $periodTo = new Carbon($exception->getPeriodTo());

            $theDate = new Carbon($date);
            if($theDate->between($periodFrom, $periodTo))
                return false;
        }

        return true;
    }

    public function applyStayDiscount(&$benefitsData, $benefit, $promoExceptions) {

        $benefitValue = $benefit->getValue();
        $calculateDiscount    = $benefit->getValueType()->getSlug() != 'net';

        if ($calculateDiscount == false)
            $dayDiscount = number_format((float)$benefitValue / $benefitsData['daysCount'], 4);

        foreach ($benefitsData['dates'] as $key => $dateObject) {

            if($this->promotionAppliesToDate($dateObject, $promoExceptions, $benefitsData['datesKeys']) == false)
                continue;

            if ($calculateDiscount == true)
                $dayDiscount = number_format((float)$benefitsData['stayDates'][$key]['priceNetRemaining'] * ($benefitValue / 100), 4);

            $this->applyPriceDiscountToDate($benefitsData, $key, $dayDiscount);
        }
    }
    public function applyNightDiscount(&$benefitsData, $benefit, $promoExceptions) {

        $benefitValue      = $benefit->getValue();
        $benefitEachValue  = $benefit->getEachValue();
        $calculateDiscount = $benefit->getValueType()->getSlug() != 'net';
        $eachCounter = 1;
        $limitCounter = 0;
        $freeNight = false;

        if ($calculateDiscount == false)
            $promoDiscount = $dayDiscount = $benefitValue;
        else // This is because free night is set as 'percent' and value = 100
            $freeNight = $benefitValue == 100;

        $benefitLimitValue = $benefit->getLimitValue();

        foreach ($benefitsData['dates'] as $key => $dateObject) {

            if($this->promotionAppliesToDate($dateObject, $promoExceptions, $benefitsData['datesKeys']) == false)
                continue;

            if ( ($benefitEachValue == null || $benefitEachValue == 0 || ($eachCounter % $benefitEachValue) == 0)) {

                $eachCounter = 0;
                $limitCounter++;

                $benefitsData['stayDates'][$key]['isFree'] = $freeNight;

                if($calculateDiscount == true)
                    $dayDiscount = $benefitsData['stayDates'][$key]['priceNetRemaining'] * ($benefitValue / 100);

                if ($benefitsData['stayDates'][$key]['priceNetRemaining'] >= $dayDiscount) {
                    $this->applyPriceDiscountToDate($benefitsData, $key, $dayDiscount);
                } else {

                    // If room net is < than the discount then, calculate how much we apply.
                    $toApply = $benefitsData['stayDates'][$key]['priceNetRemaining'];
                    $this->applyPriceDiscountToDate($benefitsData, $key, $toApply);
                    if ($benefit->isCumulative() && $calculateDiscount == false) {
                        $promoDiscount = $promoDiscount - $toApply;
                        if ($promoDiscount <= 0)
                            break;
                    }
                }

                if (($benefit->hasLimit() && $limitCounter >= $benefitLimitValue) || $benefit->isCumulative() == false)
                    break;
            }
            $eachCounter++;
        }
    }
    public function applyServiceDiscount(&$benefitsData, $benefit, $promoExceptions) {

        $benefitValue      = $benefit->getValue();
        $benefitEachValue  = $benefit->getEachValue();
        $calculateDiscount = $benefit->getValueType()->getSlug() != 'net';
        $eachCounter = 1;
        $limitCounter = 0;

        if ($calculateDiscount == false)
            $promoDiscount = $dayDiscount = $benefitValue;

        $benefitLimitValue = $benefit->getLimitValue();

        foreach ($benefitsData['dates'] as $key => $dateObject) {

            if($this->promotionAppliesToDate($dateObject, $promoExceptions, $benefitsData['datesKeys']) == false)
                continue;

            if ( ($benefitEachValue == null || $benefitEachValue == 0 || ($eachCounter % $benefitEachValue) == 0)) {

                $eachCounter = 0;
                $limitCounter++;

                if($calculateDiscount == true)
                    $dayDiscount = $benefitsData['stayDates'][$key]['mandatoryServicesRemaining'] * ($benefitValue / 100);

                if ($benefitsData['stayDates'][$key]['mandatoryServicesRemaining'] >= $dayDiscount) {
                    $this->applyServiceDiscountToDate($benefitsData, $key, $dayDiscount);
                } else {

                    // If room net is < than the discount then, calculate how much we apply.
                    $toApply = $benefitsData['stayDates'][$key]['mandatoryServicesRemaining'];
                    $this->applyServiceDiscountToDate($benefitsData, $key, $toApply);
                    if ($benefit->isCumulative() && $calculateDiscount == false) {
                        $promoDiscount = $promoDiscount - $toApply;
                        if ($promoDiscount <= 0)
                            break;
                    }
                }

                if ($benefit->hasLimit() && $limitCounter >= $benefitLimitValue)
                    break;
            }
            $eachCounter++;
        }
    }

    public function applyPriceDiscountToDate(&$benefitsData, $key, $discount)
    {
        $this->applyDiscountToDate($benefitsData, $key, $discount, 'priceNetRemaining');
    }

    public function applyServiceDiscountToDate(&$benefitsData, $key, $discount)
    {
        $this->applyDiscountToDate($benefitsData, $key, $discount, 'mandatoryServicesRemaining');
    }

    public function applyDiscountToDate(&$benefitsData, $key, $discount, $discountType)
    {
        $benefitsData['anyDiscountApplied'] = true;
        $roomNetWithDiscount = $benefitsData['stayDates'][$key][$discountType] - $discount;

        if($roomNetWithDiscount <=0) {
            $benefitsData['stayDates'][$key][$discountType] = round(0, 2);
        }
        else {
            $benefitsData['stayDates'][$key][$discountType] = $roomNetWithDiscount;
        }

        $benefitsData['stayDates'][$key]['isPromotion']      = true;
        $benefitsData['stayDates'][$key]['dateWithDiscount'] = true;
        $benefitsData['stayDates'][$key]['promotionCode']    = $benefitsData['promoCode'];
    }

    public function availableDates($dates, $availableDays, Promotion $promotion = null)
    {
        $finalArray = [];

        foreach ($dates as $key => $date) {
            // Check if day is any of Mon, Tue, Wed....
            $day = (is_a($date, 'Gushh\CoreBundle\Entity\Date')) ? $date->getDay() : $date['day'];
            if (!in_array($day, $availableDays))
                continue;

            if ($promotion->getAvailableInStopSellDates() || !$day->isStopSell())
                $finalArray[] = $key;
        }

        return $finalArray;
    }

    public function availableDatesKeys($stayDates, $availableDays, Promotion $promotion = null)
    {
        $finalArray = [];
        $promotionStart       = new \DateTime($promotion->getPeriodFrom());
        $promotionEnd         = new \DateTime($promotion->getPeriodTo());
        $validOnPremiumDates  = $promotion->getAvailableInPremiumDates();
        $validOnStopSellDates = $promotion->getAvailableInStopSellDates();

        foreach ($stayDates as $key => $date) {
            if (!in_array($date['date']->getDay(), $availableDays))
                continue;

            if (   (!$date['date']->isStopSell()      || $validOnStopSellDates)
                && (!$date['date']->isPremiumDate()   || $validOnPremiumDates)
                && ( $date['date']->getInternalDate() >= $promotionStart )
                && ( $date['date']->getInternalDate() <= $promotionEnd )
                ) {
                $finalArray[] = $date['date']->getId();
            }
        }

        return $finalArray;
    }

    public function getPromoStayPrice(array $dates, array $availableDatesKeys, $pax)
    {
        $total = 0;

        foreach ($dates as $date) {

            $dateObject = is_a($date, 'Gushh\CoreBundle\Entity\Date') ? $date : $date['date'];

            if (in_array($dateObject->getId(), $availableDatesKeys))
                $total = $total + $dateObject->getSalePrice($pax);
        }

        return $total;
    }
}

