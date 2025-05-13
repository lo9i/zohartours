<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Carbon\Carbon;
/**
 * PromotionExceptionPeriod
 *
 * @ORM\Table(name="promotion_exception_period")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\PromotionExceptionPeriodRepository")
 */
class PromotionExceptionPeriod
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
     * @var \DateTime
     *
     * @ORM\Column(name="period_from", type="datetime")
     */
    private $periodFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="period_to", type="datetime")
     */
    private $periodTo;

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
     * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="exceptions")
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     */
    protected $promotion;


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
     * Set periodFrom
     *
     * @param \DateTime $periodFrom
     * @return Promotion
     */
    public function setPeriodFrom($periodFrom)
    {
        $periodFrom = new \DateTime($periodFrom);

        $this->periodFrom = $periodFrom;

        return $this;
    }

    /**
     * Get periodFrom
     *
     * @return \DateTime
     */
    public function getPeriodFrom()
    {
        if (!$this->periodFrom) {
            return '';
        }

        return $this->periodFrom->format('Y-m-d H:i:s');
    }

    /**
     * Set periodTo
     *
     * @param \DateTime $periodTo
     * @return Promotion
     */
    public function setPeriodTo($periodTo)
    {
        $periodTo = new Carbon($periodTo);

        $this->periodTo = $periodTo->endOfDay();

        return $this;
    }

    /**
     * Get periodTo
     *
     * @return \DateTime
     */
    public function getPeriodTo()
    {
        if (!$this->periodTo) {
            return '';
        }

        return $this->periodTo->format('Y-m-d H:i:s');
    }

    function __toString() {

        return $this->getPeriodFrom() . " -> " . $this->getPeriodTo();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PromotionCondition
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
     * @return PromotionCondition
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
     * Set promotion
     *
     * @param \Gushh\CoreBundle\Entity\Promotion $promotion
     * @return PromotionCondition
     */
    public function setPromotion(\Gushh\CoreBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \Gushh\CoreBundle\Entity\Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }
}
