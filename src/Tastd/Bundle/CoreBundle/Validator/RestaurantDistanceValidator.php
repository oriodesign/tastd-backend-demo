<?php

namespace Tastd\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;

class RestaurantDistanceValidator extends ConstraintValidator
{
    /** @var RestaurantRepository */
    protected $restaurantRepository;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var Restaurant $restaurant */
        $restaurant = $value;

        $name = $restaurant->getName();
        $lat = $restaurant->getLat();
        $lng = $restaurant->getLng();
        $id = $restaurant->getId();

        if ($this->restaurantRepository->existsWithSimilarCoordinatesAndName($name, $lat, $lng, $id)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('name')
                ->setParameter('%string%', $name)
                ->addViolation();
        }


    }
}