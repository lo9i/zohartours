<?php

namespace Gushh\CoreBundle\Classes;

use Gushh\CoreBundle\Entity\Date;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Entity\Hotel;
use Carbon\Carbon;

class Util
{
    public static function createRange($dateFrom, $dateTo, $modify = true)
    {
        $end = new \DateTime($dateTo);

        // $modify: Suma un día al rango.
        // false para calcular el numero de noches.
        if ($modify)
            $end = $end->modify('+1 day');

        $dateRange = new \DatePeriod(new \DateTime($dateFrom), new \DateInterval('P1D'), $end);

        $finalDateRange = array();

        foreach ($dateRange as $date)
            $finalDateRange[] = $date->format('Y-m-d');

        return $finalDateRange;
    }

    public static function roundNumber($number, $mode = 'ceil', $denominator = 4)
    {
        $total = (($mode == 'ceil') ? ceil($number * $denominator) : floor($number * $denominator)) / $denominator;
        return number_format($total, 2);
    }

    public static function getContinent($country)
    {
        $europe = [
                    'Albania',
                    'Andorra',
                    'Austria',
                    'Belarus',
                    'Belgium',
                    'Bosnia and Herzegovina',
                    'Bulgaria',
                    'Croatia',
                    'Cyprus',
                    'Czech Republic',
                    'Denmark',
                    'East Germany',
                    'Estonia',
                    'Faroe Islands',
                    'Finland',
                    'France',
                    'Germany',
                    'Gibraltar',
                    'Greece',
                    'Guernsey',
                    'Hungary',
                    'Iceland',
                    'Ireland',
                    'Isle of Man',
                    'Italy',
                    'Jersey',
                    'Latvia',
                    'Liechtenstein',
                    'Lithuania',
                    'Luxembourg',
                    'Macedonia',
                    'Malta',
                    'Metropolitan France',
                    'Moldova',
                    'Monaco',
                    'Montenegro',
                    'Netherlands',
                    'Norway',
                    'Poland',
                    'Portugal',
                    'Romania',
                    'Russia',
                    'San Marino',
                    'Serbia',
                    'Serbia and Montenegro',
                    'Slovakia',
                    'Slovenia',
                    'Spain',
                    'Svalbard and Jan Mayen',
                    'Sweden',
                    'Switzerland',
                    'Ukraine',
                    'Union of Soviet Socialist Republics',
                    'United Kingdom',
                    'Vatican City',
                    'Åland Islands'
                ];

        if (in_array($country, $europe))
            return 'Europe';

        $america = [
            'Anguilla',
            'Antigua and Barbuda',
            'Argentina',
            'Aruba',
            'Bahamas',
            'Barbados',
            'Belize',
            'Bermuda',
            'Bolivia',
            'Brazil',
            'British Virgin Islands',
            'Canada',
            'Cayman Islands',
            'Chile',
            'Colombia',
            'Costa Rica',
            'Cuba',
            'Dominica',
            'Dominican Republic',
            'Ecuador',
            'El Salvador',
            'Falkland Islands',
            'French Guiana',
            'Greenland',
            'Grenada',
            'Guadeloupe',
            'Guatemala',
            'Guyana',
            'Haiti',
            'Honduras',
            'Jamaica',
            'Martinique',
            'Mexico',
            'México',
            'Montserrat',
            'Netherlands Antilles',
            'Nicaragua',
            'Panama',
            'Paraguay',
            'Peru',
            'Perú',
            'Puerto Rico',
            'Saint Barthélemy',
            'Saint Kitts and Nevis',
            'Saint Lucia',
            'Saint Martin',
            'Saint Pierre and Miquelon',
            'Saint Vincent and the Grenadines',
            'Suriname',
            'Trinidad and Tobago',
            'Turks and Caicos Islands',
            'U.S. Virgin Islands',
            'United States',
            'Uruguay',
            'Venezuela',
        ];
        if (in_array($country, $america))
            return 'America';

        return 'Unknow';
    }

    public static function addDays(\DateTime $date, $days = 0)
    {
        date_add($date, date_interval_create_from_date_string("$days days"));

        return $date;
    }

    public static function subDays(\DateTime $date, $days = 0)
    {
        date_sub($date, date_interval_create_from_date_string("$days days"));

        return $date;
    }

    /**
     * Make a ID --
     *
     * @method   makeCode
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @param    string     $val        [description]
     * @param    string     $concat     [description]
     * @param    string     $mode       [description]
     * @param    int        $complete   [description]
     * @param    int        $len        [description]
     *
     * @return   string
     */
    public static function makeCode($val = '', $concat = '', $mode = 'str_pad', $complete = 0, $len = 8)
    {
        if ($mode != 'str_pad')
            return substr(md5(uniqid(mt_rand(), true)), 0, $len);

        if ($concat)
            return $concat . str_pad($val, $len, $complete, STR_PAD_LEFT);

        return str_pad($val, $len, $complete, STR_PAD_LEFT);
    }

    public static function makeCalendar(array $dates, $nights, $useNetValues)
    {
        $table = <<<'EOT'
<table class="table table-condensed ResultHotelRoom-priceTableDetails">
    <thead>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wed</th>
        <th>Thu</th>
        <th>Fri</th>
        <th>Sat</th>
        <th>Sun</th>
    </thead>
    <tbody>
EOT;

        $firstRow = true;
        if(count($dates) > 0) {
            $lastDateId = end($dates)['date']->getId();

            foreach ($dates as $date) {
                $day = $date['day'];

                if ($day == 'Mon')
                    $table .= "<tr>";

                if ($firstRow == true) {
                    $table .= self::getCallendarRowPrefix($day);
                    $firstRow = false;
                }

                $table .= "<td>" . self::printPrice($date, $nights, $useNetValues) . "</td>";

                if ($date['date']->getId() == $lastDateId)
                    $table .= self::getCallendarRowSuffix($day);

                if ($day == 'Sun')
                    $table .= "</tr>";
            }
        }

        $table .= "</tbody></table>";
        return $table;
    }

    public static function getCallendarRowPrefix($day)
    {
        switch ($day) {
// Monday doesn't need any padding since it is first day
//            case 'Mon':
//                return "";
            case 'Tue':
                return "<td></td>";
            case 'Wed':
                return "<td></td><td></td>";
            case 'Thu':
                return "<td></td><td></td><td></td>";
            case 'Fri':
                return "<td></td><td></td><td></td><td></td>";
            case 'Sat':
                return "<td></td><td></td><td></td><td></td><td></td>";
            case 'Sun':
                return "<td></td><td></td><td></td><td></td><td></td><td></td>";
        }
        return "";
    }

    public static function getCallendarRowSuffix($day)
    {
        switch ($day) {
            case 'Mon':
                return "<td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            case 'Tue':
                return "<td></td><td></td><td></td><td></td><td></td></tr>";
            case 'Wed':
                return "<td></td><td></td><td></td><td></td></tr>";
            case 'Thu':
                return "<td></td><td></td><td></td></tr>";
            case 'Fri':
                return "<td></td><td></td></tr>";
            case 'Sat':
                return "<td></td></tr>";
// Sunday doesn't need any suffix since it is last day
//            case 'Sun':
//                return "";
        }
        return "";
    }

    public static function printPrice($date, $nights, $useNetValues)
    {
        $message      = '';

        $minimumStay      = $date['date']->getMinimumStay();
        $maximumStay      = $date['date']->getMaximumStay();
        $dateStock        = $date['date']->getStock();
        $dateCutOff       = $date['date']->getCutOff();
        $dateDate         = $date['date']->getDate('d M Y');
        $isOnRequest      = $date['onRequest'];
        $isFree           = $date['isFree'];
        $isPremium        = $date['isPremium'];
        if($useNetValues == true) {
            $price = $date['totalCost'];
        }
        else {
//            $price = $date['total'];
            $price = $date['totalWithServicesAndCommission'];
        }
        $daysUntilCheckIn = self::daysUntilCheckIn($date['date']->getDate());

        if ($isFree)
            $message .= "Night off\n";

        if ($daysUntilCheckIn < $dateCutOff)
            $message .= "Cut-off required ($dateCutOff nights)\n";

        if ($nights < $minimumStay)
            $message .= "Minimum stay required ($minimumStay nights)\n";

        if ($nights > $maximumStay)
            $message   .= "Maximum stay required ($maximumStay nights)\n";

        if ($dateStock < 1)
            $message   .= "On Request\n";

        if($price <= 0)
            $price =  $isFree ? "Free" : 'On Request';
        else
            $price = "$ " . number_format((float) $price, 2);

        if ($isOnRequest)
            $code = "<span class='label label-info'>$dateDate</span><br><span class='label label-warning' data-toggle='tooltip' data-placement='bottom' title='On Request: $message'> " . $price . "</span>";
        else if ($isFree)
            $code = "<span class='label label-info'>$dateDate</span><br><span class='label label-success' data-toggle='tooltip' data-placement='bottom' title='Free: $message'>" . $price . "</span>";
        else if ($isPremium)
            $code = "<span class='label label-info'>$dateDate</span><br><span class='label label-success' data-toggle='tooltip' data-placement='bottom' title='Available, Premium Date'> " . $price . "</span>";
        else
            $code = "<span class='label label-info'>$dateDate</span><br><span class='label label-success tooltip-success' data-toggle='tooltip' data-placement='bottom' title='Available'> " . $price . "</span>";

        return htmlspecialchars_decode($code);
    }

    public static function daysUntilCheckIn($date)
    {
        $today = new Carbon();
        return $today->diffInDays(new Carbon($date));
    }

    /**
     * [realAdults description]
     *
     * @author   Anibal Fernández   <anibaldfernandez@hotmail.com>
     *
     * @param    Hotel $hotel
     * @param    int     $adults            [description]
     * @param    array   $childrenAge       [description]
     *
     * @return   int     $realAdults
     */
    public static function realAdults(Hotel $hotel, $adults, $childrenAge)
    {
        $realAdults = $adults;
        $freeChildrenAge = $hotel->getChildAge();

        foreach ($childrenAge as $childAge) {
            if ($childAge > $freeChildrenAge)
                $realAdults++;
        }

        return $realAdults;
    }

    public static function createID($concat = null, $val, $complete = 0, $long = 7)
    {
        if ($concat) {
            $text = $concat . str_pad($val, $long, $complete, STR_PAD_LEFT);
        } else {
            $text = str_pad($val, $long, $complete, STR_PAD_LEFT);
        }

        return $text;
    }

    public static function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public static function getOccupancy($adults) {

        switch ($adults) {
            case 1: return 'SINGLE';
            case 2: return 'DOUBLE';
            case 3: return 'TRIPLE';
            case 4: return 'QUADRUPLE';
            case 5: return 'QUINTUPLE';
            case 6: return 'SEXTUPLE';
            case 7: return 'SEPTUPLE';
            case 8: return 'OCTUPLE';
            case 9: return 'NONUPLE';
            case 10: return 'DECUPLE';
            case 11: return 'UNDECUPLE';
            case 12: return 'DUODECUPLE';
            case 13: return 'TREDECUPLE';
            default: 'UNKNOWN';
        }
    }
}
