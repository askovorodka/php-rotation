<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="deal_offer_partner_manager")
 * @ORM\Entity
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 */
class DealOfferPartnerManager
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="DealOffer", inversedBy="dealOfferPartnerManagers")
     * @ORM\JoinColumn(name="deal_offer_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $dealOffer;

    /**
     * @ORM\Column(name="manager_email", type="string", length=180, nullable=false)
     * @Gedmo\Versioned
     */
    protected $managerEmail;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DealOffer
     */
    public function getDealOffer()
    {
        return $this->dealOffer;
    }

    /**
     * @param mixed $dealOffer
     * @return DealOfferPartnerManager
     */
    public function setDealOffer(DealOffer $dealOffer)
    {
        $this->dealOffer = $dealOffer;
        return $this;
    }

    /**
     * @return string
     */
    public function getManagerEmail()
    {
        return $this->managerEmail;
    }

    /**
     * @param string $managerEmail
     * @return DealOfferPartnerManager
     */
    public function setManagerEmail(string $managerEmail)
    {
        $this->managerEmail = $managerEmail;
        return $this;
    }

}
