<?php

namespace Tastd\Bundle\CoreBundle\Foursquare;

use \Jcroll\FoursquareApiClient\Client\FoursquareClient as Foursquare;

/**
 * Class FoursquareClient
 *
 * @package Tastd\Bundle\CoreBundle\Foursquare
 */
class FoursquareClient
{
    /** @var Foursquare */
    protected $foursquare;

    public function __construct(Foursquare $foursquare)
    {
        $this->foursquare = $foursquare;
    }

    /**
     * @param string $near
     * @param int    $offset
     *
     * @return array
     */
    public function explore($near, $offset = 0)
    {
        $venues = array();
        $params = array(
            'near' => $near,
            'radius' => 2000,
            'section' => 'food',
            'limit' => 50,
            'offset' => $offset
        );

        $command = $this->foursquare->getCommand('venues/explore', $params);
        $results = $command->execute();

        foreach ($results['response']['groups'] as $group) {
            foreach ($group['items'] as $item) {
                $venues[] = $item['venue'];
            }
        }

        return $venues;
    }



}