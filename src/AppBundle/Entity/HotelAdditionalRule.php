<?php

namespace AppBundle\Entity;

use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Дополнительные условия проживания")
 */
class HotelAdditionalRule
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="hotelAdditionalRules")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\Column(name="age_from", type="smallint", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $ageFrom;

    /**
     * @ORM\Column(name="age_to", type="smallint", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $ageTo;

    /**
     * @ORM\Column(name="living_cost", type="float", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $livingCost;

    /**
     * @ORM\Column(name="meal_cost", type="float", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $mealCost;

    /**
     * @ORM\Column(name="comment", type="string", length=255)
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * HotelAdditionalRule constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
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
     * Set ageFrom
     *
     * @param integer $ageFrom
     *
     * @return HotelAdditionalRule
     */
    public function setAgeFrom($ageFrom)
    {
        $this->ageFrom = $ageFrom;

        return $this;
    }

    /**
     * Get ageFrom
     *
     * @return integer
     */
    public function getAgeFrom()
    {
        return $this->ageFrom;
    }

    /**
     * Set ageTo
     *
     * @param integer $ageTo
     *
     * @return HotelAdditionalRule
     */
    public function setAgeTo($ageTo)
    {
        $this->ageTo = $ageTo;

        return $this;
    }

    /**
     * Get ageTo
     *
     * @return integer
     */
    public function getAgeTo()
    {
        return $this->ageTo;
    }

    /**
     * Set livingCost
     *
     * @param float $livingCost
     *
     * @return HotelAdditionalRule
     */
    public function setLivingCost($livingCost)
    {
        $this->livingCost = $livingCost;

        return $this;
    }

    /**
     * Get livingCost
     *
     * @return float
     */
    public function getLivingCost()
    {
        return $this->livingCost;
    }

    /**
     * Set mealCost
     *
     * @param float $mealCost
     *
     * @return HotelAdditionalRule
     */
    public function setMealCost($mealCost)
    {
        $this->mealCost = $mealCost;

        return $this;
    }

    /**
     * Get mealCost
     *
     * @return float
     */
    public function getMealCost()
    {
        return $this->mealCost;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return HotelAdditionalRule
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return HotelAdditionalRule
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return HotelAdditionalRule
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
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return HotelAdditionalRule
     */
    public function setHotel(\AppBundle\Entity\Hotel $hotel)
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * Get hotel
     *
     * @return \AppBundle\Entity\Hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }
}
