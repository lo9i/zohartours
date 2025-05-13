<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PromotionCondition
 *
 * @ORM\Table(name="promotion_condition")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\PromotionConditionRepository")
 */
class PromotionCondition
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
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

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
     * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="conditions")
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     */
    protected $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="PromotionConditionConditional", inversedBy="conditions")
     * @ORM\JoinColumn(name="conditional_id", referencedColumnName="id")
     */
    protected $conditional;

    /**
     * @ORM\ManyToOne(targetEntity="PromotionConditionExpression", inversedBy="conditions")
     * @ORM\JoinColumn(name="expression_id", referencedColumnName="id")
     */
    protected $expression;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    function __toString() {

        $slug = $this->expression->getSlug();

        if ($slug != 'for-each') {
            $text = "$this->conditional $this->expression $this->value";
        } else {
            $conditional = strtolower($this->conditional);
            $text = "Each $this->value $conditional";
        }

        return $text;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return PromotionCondition
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
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

    /**
     * Set conditional
     *
     * @param \Gushh\CoreBundle\Entity\PromotionConditionConditional $conditional
     * @return PromotionCondition
     */
    public function setConditional(\Gushh\CoreBundle\Entity\PromotionConditionConditional $conditional = null)
    {
        $this->conditional = $conditional;

        return $this;
    }

    /**
     * Get conditional
     *
     * @return \Gushh\CoreBundle\Entity\PromotionConditionConditional 
     */
    public function getConditional()
    {
        return $this->conditional;
    }

    /**
     * Set expression
     *
     * @param \Gushh\CoreBundle\Entity\PromotionConditionExpression $expression
     * @return PromotionCondition
     */
    public function setExpression(\Gushh\CoreBundle\Entity\PromotionConditionExpression $expression = null)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Get expression
     *
     * @return \Gushh\CoreBundle\Entity\PromotionConditionExpression 
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
