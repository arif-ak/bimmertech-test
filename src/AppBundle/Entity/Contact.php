<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class FAQQuestion
 * @ORM\Table(name="app_contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 */
class Contact implements ResourceInterface
{
    const VERTICAL_POSITION_CONTENT = 'Vertical';
    const HORIZONTAL_POSITION_CONTENT = 'Horizontal';

    protected $id;

    protected $contactMainTitle;

    protected $contactPosition;

    protected $contactContent;

    public function __construct()
    {
        $this->contactContent = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContactMainTitle()
    {
        return $this->contactMainTitle;
    }

    /**
     * @param mixed $contactMainTitle
     */
    public function setContactMainTitle($contactMainTitle): void
    {
        $this->contactMainTitle = $contactMainTitle;
    }

    /**
     * @return mixed
     */
    public function getContactPosition()
    {
        return $this->contactPosition;
    }

    /**
     * @param mixed $contactPosition
     */
    public function setContactPosition($contactPosition): void
    {
        $this->contactPosition = $contactPosition;
    }

    /**
     * @return ArrayCollection
     */
    public function getContactContent()
    {
        return $this->contactContent;
    }

    /**
     * @return bool
     */
    public function hasContactContent(): bool
    {
        return !$this->contactContent->isEmpty();
    }

    /**
     * @param ContactContent $contactContent
     */
    public function addContactContent(ContactContent $contactContent)
    {
        if (true === $this->contactContent->contains($contactContent)) {
            return;
        }

        $this->contactContent->add($contactContent);
        $contactContent->setContact($this);
    }

    /**
     * @param ContactContent $contactContent
     */
    public function removeContactContent(ContactContent $contactContent)
    {
        if (false === $this->contactContent->contains($contactContent)) {
            return;
        }

        $this->contactContent->removeElement($contactContent);
        $contactContent->setContact(null);
    }
}
