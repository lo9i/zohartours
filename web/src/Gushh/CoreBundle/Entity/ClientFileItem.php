<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ClientFileItem
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClientFileItem
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
     * @ORM\Column(name="detail", type="string", length=255)
     */
    private $detail;

    /**
     * @var decimal
     *
     * @ORM\Column(name="amount_sale", type="decimal", precision=15, scale=2)
     */
    private $amount_sale = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="amount_net", type="decimal", precision=15, scale=2)
     */
    private $amount_net = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="amount_commission", type="decimal", precision=15, scale=2)
     */
    private $amount_commission = 0;

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
     * @ORM\ManyToOne(targetEntity="Provider", inversedBy="services")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    protected $provider;

    /**
     * @ORM\ManyToOne(targetEntity="ClientFile", inversedBy="items")
     * @ORM\JoinColumn(name="client_file_id", referencedColumnName="id")
     */
    protected $clientFile;

    /**
     * @ORM\ManyToOne(targetEntity="ClientFileItemStatus", inversedBy="client_file_items")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status;

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
     * Set amount_sale
     *
     * @param string $amount_sale
     * @return ClientFileItem
     */
    public function setAmountSale($amount)
    {
        $this->amount_sale = $amount;

        return $this;
    }

    /**
     * Get amount_sale
     *
     * @return string 
     */
    public function getAmountSale()
    {
        return $this->amount_sale;
    }

    /**
     * Set amount_net
     *
     * @param string $amount_net
     * @return ClientFileItem
     */
    public function setAmountNet($amount)
    {
        $this->amount_net = $amount;

        return $this;
    }

    /**
     * Get amount_net
     *
     * @return string
     */
    public function getAmountNet()
    {
        return $this->amount_net;
    }

    /**
     * Set amount_commission
     *
     * @param string $amount_commission
     * @return ClientFileItem
     */
    public function setAmountCommission($amount)
    {
        $this->amount_commission = $amount;

        return $this;
    }

    /**
     * Get amount_commission
     *
     * @return string
     */
    public function getAmountCommission()
    {
        return $this->amount_commission;
    }

    /**
     * Get gross profit
     *
     * @return string
     */
    public function getAmountGrossProfit()
    {
        return $this->amount_sale - $this->amount_net;
    }

    /**
     * Get net profit
     *
     * @return string
     */
    public function getAmountNetProfit()
    {
        return $this->amount_sale - $this->amount_net - $this->amount_commission;
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return ClientFileItem
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ClientFileItem
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
     * @return ClientFileItem
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
     * Set ClientFile
     *
     * @param \Gushh\CoreBundle\Entity\ClientFile $clientFile
     * @return ClientFileItem
     */
    public function setClientFile(\Gushh\CoreBundle\Entity\ClientFile $clientFile = null)
    {
        $this->clientFile = $clientFile;

        return $this;
    }

    /**
     * Get clientFile
     *
     * @return \Gushh\CoreBundle\Entity\ClientFile 
     */
    public function getClientFile()
    {
        return $this->clientFile;
    }
    /**
     * Set Provider
     *
     * @param \Gushh\CoreBundle\Entity\Provider $provider
     * @return ClientFileItem
     */
    public function setProvider(\Gushh\CoreBundle\Entity\Provider $provider = null)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get Provider
     *
     * @return \Gushh\CoreBundle\Entity\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
    /**
     * Set Status
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileItemStatuss $status
     * @return ClientFileItem
     */
    public function setStatus(\Gushh\CoreBundle\Entity\ClientFileItemStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get Status
     *
     * @return \Gushh\CoreBundle\Entity\ClientFileItemStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}
