<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClientFile
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClientFile
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
     * @ORM\Column(name="client_name", type="string", length=255)
     */
    private $clientName;

    /**
     * @var string
     *
     * @ORM\Column(name="client_last_name", type="string", length=255)
     */
    private $clientLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_sale", type="decimal", precision=15, scale=2)
     */
    private $total_sale;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_net", type="decimal", precision=15, scale=2)
     */
    private $total_net;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_commission", type="decimal", precision=15, scale=2)
     */
    private $total_commission;

    /**
     * @ORM\OneToMany(targetEntity="ClientFileItem", mappedBy="clientFile", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="ClientFilePaymentMade", mappedBy="clientFile", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $payments_made;

    /**
     * @ORM\OneToMany(targetEntity="ClientFilePaymentReceived", mappedBy="clientFile", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $payments_received;

    /**
     * @ORM\OneToMany(targetEntity="ClientFileCommissionPaid", mappedBy="clientFile", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $commissions_paid;


    /**
     * @ORM\ManyToOne(targetEntity="Agency", inversedBy="client_files")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     */
    protected $agency;

    /**
     * @ORM\ManyToOne(targetEntity="ClientFileStatus", inversedBy="client_files")
     * @ORM\JoinColumn(name="client_file_status_id", referencedColumnName="id")
     */
    protected $status;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments_made = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments_received = new \Doctrine\Common\Collections\ArrayCollection();
        $this->commissions_paid = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return clientFile
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set payerName
     *
     * @param string $clientName
     * @return ClientFile
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set clientLastName
     *
     * @param string $clientLastName
     * @return ClientFile
     */
    public function setClientLastName($clientLastName)
    {
        $this->clientLastName = $clientLastName;

        return $this;
    }

    /**
     * Get clientLastName
     *
     * @return string
     */
    public function getClientLastName()
    {
        return $this->clientLastName;
    }

    /**
     * Set total_sale
     *
     * @param string $total_sale
     * @return ClientFileItem
     */
    public function setTotalSale($amount)
    {
        $this->total_sale = $amount;

        return $this;
    }

    /**
     * Get total_sale
     *
     * @return string
     */
    public function getTotalSale()
    {
        return $this->total_sale;
    }

    /**
     * Set total_commission
     *
     * @param string $total_commission
     * @return ClientFileItem
     */
    public function setTotalCommission($amount)
    {
        $this->total_commission = $amount;

        return $this;
    }

    /**
     * Get total_commission
     *
     * @return string
     */
    public function getTotalCommission()
    {
        return $this->total_commission;
    }

    /**
     * Set total_net
     *
     * @param string $total_net
     * @return ClientFileItem
     */
    public function setTotalNet($amount)
    {
        $this->total_net = $amount;

        return $this;
    }

    /**
     * Get total_net
     *
     * @return string
     */
    public function getTotalNet()
    {
        return $this->total_net;
    }

    /**
     * Get Total gross profit
     *
     * @return string
     */
    public function getTotalGrossProfit()
    {
        return $this->total_sale - $this->total_net;
    }

    /**
     * Get Total net profit
     *
     * @return string
     */
    public function getTotalNetProfit()
    {
        return $this->total_sale - $this->total_net - $this->total_commission;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ClientFile
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
     * @return ClientFile
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
     * Add items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileItem $items
     * @return ClientFile
     */
    public function addItem(\Gushh\CoreBundle\Entity\ClientFileItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileItem $items
     */
    public function removeItem(\Gushh\CoreBundle\Entity\ClientFileItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add payment
     *
     * @param \Gushh\CoreBundle\Entity\ClientFilePaymentMade $items
     * @return ClientFile
     */
    public function addPaymentMade(\Gushh\CoreBundle\Entity\ClientFilePaymentMade $payments_made)
    {
        $this->payments_made[] = $payments_made;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFilePaymentMade $items
     */
    public function removePaymentMade(\Gushh\CoreBundle\Entity\ClientFilePaymentMade $payment)
    {
        $this->payments_made->removeElement($payment);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentsMade()
    {
        return $this->payments_made;
    }

    /**
     * Add items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFilePaymentReceived $items
     * @return ClientFile
     */
    public function addPaymentReceived(\Gushh\CoreBundle\Entity\ClientFilePaymentReceived $items)
    {
        $this->payments_received[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFilePaymentReceived $items
     */
    public function removePaymentReceived(\Gushh\CoreBundle\Entity\ClientFilePaymentReceived $item)
    {
        $this->payments_received->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentsReceived()
    {
        return $this->payments_received;
    }

    /**
     * Add items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileCommissionPaid $items
     * @return ClientFile
     */
    public function addCommissionPaid(\Gushh\CoreBundle\Entity\ClientFileCommissionPaid $items)
    {
        $this->commissions_paid[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileCommissionPaid $items
     */
    public function removeCommissionPaid(\Gushh\CoreBundle\Entity\ClientFileCommissionPaid $item)
    {
        $this->commissions_paid->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommissionsPaid()
    {
        return $this->commissions_paid;
    }

    /**
     * Set status
     *
     * @param \Gushh\CoreBundle\Entity\ClientFileStatus $status
     * @return ClientFile
     */
    public function setStatus(\Gushh\CoreBundle\Entity\ClientFileStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Gushh\CoreBundle\Entity\ClientFileStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set agency
     *
     * @param \Gushh\CoreBundle\Entity\Agency $agency
     * @return ClientFile
     */
    public function setAgency(\Gushh\CoreBundle\Entity\Agency $agency = null)
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * Get agency
     *
     * @return \Gushh\CoreBundle\Entity\Agency
     */
    public function getAgency()
    {
        return $this->agency;
    }

    public function getBalanceNet()
    {
        $balance = $this->total_net;
        foreach ($this->getPaymentsMade() as $item)
            $balance = $balance - $item->getAmount();
        return $balance;
    }

    public function getBalanceSale()
    {
        $balance = $this->total_sale;
        foreach ($this->getPaymentsReceived() as $item)
            $balance = $balance - $item->getAmount();
        return $balance;
    }

    public function getBalanceCommission()
    {
        $balance = $this->total_commission;
        foreach ($this->getCommissionsPaid() as $item)
            $balance = $balance - $item->getAmount();
        return $balance;
    }

    public function getIsReady()
    {
        return $this->getBalanceNet() == 0 && $this->getBalanceSale() == 0 && $this->getBalanceCommission() == 0;
    }
}


