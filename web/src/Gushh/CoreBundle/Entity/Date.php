<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Gushh\CoreBundle\Classes\Util;

/**
 * Date
 *
 * @ORM\Table(name="date")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\DateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Date
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="cut_off", type="integer")
     */
    private $cutOff = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="minimum_stay", type="integer")
     */
    private $minimumStay = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="maximum_stay", type="integer")
     */
    private $maximumStay = 29;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="premium_date", type="boolean")
     */
    private $premiumDate = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stop_sell", type="boolean")
     */
    private $stopSell = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="black_out", type="boolean")
     */
    private $blackOut = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="daily_rates", type="boolean")
     */
    private $dailyRates = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime", nullable=true)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    private $dateTo;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="dates")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="mondayDates")
     * @ORM\JoinColumn(name="monday_rate_id", referencedColumnName="id")
     */
    protected $mondayRate;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="tuesdayDates")
     * @ORM\JoinColumn(name="tuesday_rate_id", referencedColumnName="id")
     */
    protected $tuesdayRate;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="wednesdayDates")
     * @ORM\JoinColumn(name="wednesday_rate_id", referencedColumnName="id")
     */
    protected $wednesdayRate;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="thursdayDates")
     * @ORM\JoinColumn(name="thursday_rate_id", referencedColumnName="id")
     */
    protected $thursdayRate;
    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="fridayDates")
     * @ORM\JoinColumn(name="friday_rate_id", referencedColumnName="id")
     */
    protected $fridayRate;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="saturdayDates")
     * @ORM\JoinColumn(name="saturday_rate_id", referencedColumnName="id")
     */
    protected $saturdayRate;

    /**
     * @ORM\ManyToOne(targetEntity="Rate", inversedBy="sundayDates")
     * @ORM\JoinColumn(name="sunday_rate_id", referencedColumnName="id")
     */
    protected $sundayRate;

    protected $rate;

    /**
     * @ORM\ManyToOne(targetEntity="CancellationPolicy", inversedBy="dates")
     * @ORM\JoinColumn(name="cancellation_policy_id", referencedColumnName="id")
     */
    protected $cancellationPolicy;

    /**
     * To string function
     *
     * @return string
     */
    public function __toString()
    {
        return $this->date->format('Y-m-d H:i:s');
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Date
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Date
     */
    public function setDate($theDate)
    {
        $this->date = new \DateTime($theDate);

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate($format = null)
    {
        if (!$this->date) :
            return '';
        endif;

        return ($format) ? $this->date->format($format) : $this->date->format('Y-m-d H:i:s');
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getInternalDate()
    {
        return $this->date;
    }

    /**
     * Set cutOff
     *
     * @param integer $cutOff
     * @return Date
     */
    public function setCutOff($cutOff)
    {
        $this->cutOff = $cutOff;

        return $this;
    }

    /**
     * Get cutOff
     *
     * @return integer
     */
    public function getCutOff()
    {
        return $this->cutOff;
    }

    /**
     * Set minimumStay
     *
     * @param integer $minimumStay
     * @return Date
     */
    public function setMinimumStay($minimumStay)
    {
        $this->minimumStay = $minimumStay;

        return $this;
    }

    /**
     * Get minimumStay
     *
     * @return integer
     */
    public function getMinimumStay()
    {
        return $this->minimumStay;
    }

    /**
     * Set maximumStay
     *
     * @param integer $maximumStay
     * @return Date
     */
    public function setMaximumStay($maximumStay)
    {
        $this->maximumStay = $maximumStay;

        return $this;
    }

    /**
     * Get maximumStay
     *
     * @return integer
     */
    public function getMaximumStay()
    {
        return $this->maximumStay;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Date
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set premiumDate
     *
     * @param boolean $premiumDate
     * @return Date
     */
    public function setPremiumDate($premiumDate)
    {
        $this->premiumDate = $premiumDate;

        return $this;
    }

    /**
     * Get premiumDate
     *
     * @return boolean
     */
    public function getPremiumDate()
    {
        return $this->premiumDate;
    }

    /**
     * Set stopSell
     *
     * @param boolean $stopSell
     * @return Date
     */
    public function setStopSell($stopSell)
    {
        $this->stopSell = $stopSell;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getStopSell()
    {
        return $this->stopSell;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Date
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Date
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set room
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Date
     */
    public function setRoom(\Gushh\CoreBundle\Entity\Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Gushh\CoreBundle\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }


    /**
     * Set stock
     *
     * @param integer $stock
     * @return Date
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }


    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return Date
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = new \DateTime($dateFrom);;
        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        if (!$this->dateFrom) :
            return '';
        endif;

        return $this->dateFrom->format('Y-m-d H:i:s');
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     * @return Date
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = new \DateTime($dateTo);;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        if (!$this->dateTo) :
            return '';
        endif;

        return $this->dateTo->format('Y-m-d H:i:s');
    }


    /**
     * Set cancellationPolicy
     *
     * @param \Gushh\CoreBundle\Entity\CancellationPolicy $cancellationPolicy
     * @return Date
     */
    public function setCancellationPolicy(\Gushh\CoreBundle\Entity\CancellationPolicy $cancellationPolicy = null)
    {
        $this->cancellationPolicy = $cancellationPolicy;

        return $this;
    }

    /**
     * Get cancellationPolicy
     *
     * @return \Gushh\CoreBundle\Entity\CancellationPolicy
     */
    public function getCancellationPolicy()
    {
        return $this->cancellationPolicy;
    }

    /**
     * Get salePrice
     *
     * @return string
     */
    public function getSalePrice($pax = 2, $nights = 1)
    {
        return $this->getRate()->getSalePrice($pax, $nights);
    }

    /**
     * Set blackOut
     *
     * @param boolean $blackOut
     * @return Date
     */
    public function setBlackOut($blackOut)
    {
        $this->blackOut = $blackOut;

        return $this;
    }

    /**
     * Get blackOut
     *
     * @return boolean
     */
    public function getBlackOut()
    {
        return $this->blackOut;
    }

    /**
     * Set dailyRates
     *
     * @param boolean $dailyRates
     * @return Date
     */
    public function setDailyRates($dailyRates)
    {
        $this->dailyRates = $dailyRates;

        return $this;
    }

    /**
     * Get dailyRates
     *
     * @return boolean
     */
    public function getDailyRates()
    {
        return $this->dailyRates;
    }

    /**
     * Set mondayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $mondayRate
     * @return Date
     */
    public function setMondayRate(\Gushh\CoreBundle\Entity\Rate $mondayRate = null)
    {
        $this->mondayRate = $mondayRate;

        return $this;
    }

    /**
     * Get mondayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getMondayRate()
    {
        return $this->mondayRate;
    }

    /**
     * Set tuesdayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $tuesdayRate
     * @return Date
     */
    public function setTuesdayRate(\Gushh\CoreBundle\Entity\Rate $tuesdayRate = null)
    {
        $this->tuesdayRate = $tuesdayRate;

        return $this;
    }

    /**
     * Get tuesdayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getTuesdayRate()
    {
        return $this->tuesdayRate;
    }

    /**
     * Set wednesdayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $wednesdayRate
     * @return Date
     */
    public function setWednesdayRate(\Gushh\CoreBundle\Entity\Rate $wednesdayRate = null)
    {
        $this->wednesdayRate = $wednesdayRate;

        return $this;
    }

    /**
     * Get wednesdayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getWednesdayRate()
    {
        return $this->wednesdayRate;
    }

    /**
     * Set thursdayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $thursdayRate
     * @return Date
     */
    public function setThursdayRate(\Gushh\CoreBundle\Entity\Rate $thursdayRate = null)
    {
        $this->thursdayRate = $thursdayRate;

        return $this;
    }

    /**
     * Get thursdayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getThursdayRate()
    {
        return $this->thursdayRate;
    }

    /**
     * Set fridayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $fridayRate
     * @return Date
     */
    public function setFridayRate(\Gushh\CoreBundle\Entity\Rate $fridayRate = null)
    {
        $this->fridayRate = $fridayRate;

        return $this;
    }

    /**
     * Get fridayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getFridayRate()
    {
        return $this->fridayRate;
    }

    /**
     * Set saturdayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $saturdayRate
     * @return Date
     */
    public function setSaturdayRate(\Gushh\CoreBundle\Entity\Rate $saturdayRate = null)
    {
        $this->saturdayRate = $saturdayRate;

        return $this;
    }

    /**
     * Get saturdayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getSaturdayRate()
    {
        return $this->saturdayRate;
    }

    /**
     * Set sundayRate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $sundayRate
     * @return Date
     */
    public function setSundayRate(\Gushh\CoreBundle\Entity\Rate $sundayRate = null)
    {
        $this->sundayRate = $sundayRate;

        return $this;
    }

    /**
     * Get sundayRate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getSundayRate()
    {
        return $this->sundayRate;
    }

    /**
     * Get rate
     *
     * @return \Gushh\CoreBundle\Entity\Rate
     */
    public function getRate()
    {
        if ($this->date !== null) :
            $day = $this->date->format('l');

            switch ($day) :
                case 'Monday':
                    return $this->mondayRate;
                    break;
                case 'Tuesday':
                    return $this->tuesdayRate;
                    break;
                case 'Wednesday':
                    return $this->wednesdayRate;
                    break;
                case 'Thursday':
                    return $this->thursdayRate;
                    break;
                case 'Friday':
                    return $this->fridayRate;
                    break;
                case 'Saturday':
                    return $this->saturdayRate;
                    break;
                case 'Sunday':
                    return $this->sundayRate;
                    break;
            endswitch;
        endif;

        return $this->sundayRate;
    }

    /**
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return ($this->date !== null) ? $this->date->format('D') : '';
    }

    /**
     * Set rate
     *
     * @param \Gushh\CoreBundle\Entity\Rate $rate
     * @return Date
     */
    public function setRate(\Gushh\CoreBundle\Entity\Rate $rate = null)
    {
        // $this->mondayRate       = $rate;
        // $this->tuesdayRate      = $rate;
        // $this->wednesdayRate    = $rate;
        // $this->thursdayRate     = $rate;
        // $this->fridayRate       = $rate;
        // $this->saturdayRate     = $rate;
        // $this->sundayRate       = $rate;

        return $this;
    }

    /**
    * Get isStopSell
    * @return
    */
    public function isStopSell()
    {
        return $this->stopSell;
    }

    /**
    * Get isStopSell
    * @return
    */
    public function isPremiumDate()
    {
        return $this->premiumDate;
    }
}
