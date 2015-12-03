<?php

namespace Tastd\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Option
 */
class Option
{

    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Option';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Option';

    /** @var integer */
    protected $id;
    /** @var string */
    protected $name;
    /** @var User */
    protected $user;
    /** @var string */
    protected $content;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}