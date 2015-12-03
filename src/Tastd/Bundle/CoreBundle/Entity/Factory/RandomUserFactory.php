<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;

use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class RandomUserFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class RandomUserFactory
{
    protected $counter;
    protected $maleFirstNames;
    protected $femaleFirstNames;
    protected $lastNames;
    protected $maleAvatars;
    protected $femaleAvatars;
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->counter = 0;
        $this->femaleAvatars = array();
        $this->maleAvatars = array();
        for ($i=1;$i<21;$i++) {
            $this->femaleAvatars[] = 'avatar/w' . $i . '.jpg';
            $this->maleAvatars[] = 'avatar/' . $i . '.jpg';
        }

        $this->maleFirstNames = array(
            'William',
            'James',
            'John',
            'Jacob',
            'Christopher',
            'Michael',
            'Ethan',
            'Hunter',
            'Tyler',
            'Matthew',
            'Jose',
            'Ryan',
            'Logan',
            'Gabriel',
            'Gavin',
            'Anthony',
            'Alexander'
        );

        $this->femaleFirstNames = array(
            'Emily',
            'Ava',
            'Isabella',
            'Olivia',
            'Sophia',
            'Alyssa',
            'Brianna',
            'Chloe',
            'Kayla',
            'Ashley',
            'Katherine',
            'Grace',
            'Emma',
            'Addison',
            'Madison',
            'Taylor'
        );

        $this->lastNames = array(
            'Smith',
            'Johnson',
            'Brown',
            'Davis',
            'Miller',
            'Martin',
            'Garcia',
            'Wilson',
            'Thomas',
            'Lee',
            'Lewis',
            'Walker',
            'Hill',
            'Lopez',
            'Adams',
            'Green',
            'Cox',
            'Gray',
            'Ross',
            'Foster'
        );
    }

    public function createAll($max = 10)
    {
        $users = array();
        for ($i = 0; $i < $max; $i++) {
            $users[] = $this->create();
        }
        $this->entityManager->flush();

        return $users;
    }

    /**
     * @return User
     */
    public function create()
    {
        $isMale = rand(0, 1);
        $this->counter++;
        $user = new User();
        $user->setBirthYear($this->getRandomBirthYear());
        $email = $this->getRandomEmail();
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setLastName($this->getRandomLastName());
        $user->setEnabled(true);
        $user->setHeadline($this->getRandomHeadline());
        $user->setAbout($this->getRandomAbout());
        $user->setPlainPassword($this->getRandomPassword());
        $user->setScore($this->getRandomScore());
        if ($isMale) {
            $user->setFirstName($this->getRandomMaleFirstName());
            $user->setAvatar($this->getRandomMaleAvatar());
        } else {
            $user->setFirstName($this->getRandomFemaleFirstName());
            $user->setAvatar($this->getRandomFemaleAvatar());
        }
        $this->entityManager->persist($user);

        return $user;
    }

    /**
     * @return string
     */
    public function getRandomPassword()
    {
        return '123456';
    }

    /**
     * @return string
     */
    public function getRandomAbout()
    {
        return 'Lorem ipsum dolor sit';
    }

    /**
     * @return string
     */
    public function getRandomHeadline()
    {
        return 'My fantastic Headline';
    }

    /**
     * @return mixed
     */
    public function getRandomFemaleFirstName()
    {
        return $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
    }

    /**
     * @return mixed
     */
    public function getRandomMaleFirstName()
    {
        return $this->maleFirstNames[array_rand($this->maleFirstNames)];
    }

    /**
     * @return mixed
     */
    public function getRandomLastName()
    {
        return $this->lastNames[array_rand($this->lastNames)];
    }

    /**
     * @return string
     */
    public function getRandomEmail()
    {
        return 'email' . $this->counter . '@gmail.com';
    }

    /**
     * @return mixed
     */
    public function getRandomFemaleAvatar()
    {
        return $this->femaleAvatars[array_rand($this->femaleAvatars)];
    }

    /**
     * @return mixed
     */
    public function getRandomMaleAvatar()
    {
        return $this->maleAvatars[array_rand($this->maleAvatars)];
    }

    /**
     * @return int
     */
    public function getRandomBirthYear()
    {
        return 1950 + rand(0, 50);
    }

    /**
     * @return int
     */
    public function getRandomScore()
    {
        return rand(0,10000);
    }
}