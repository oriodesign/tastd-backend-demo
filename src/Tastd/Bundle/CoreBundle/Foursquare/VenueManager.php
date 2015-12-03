<?php

namespace Tastd\Bundle\CoreBundle\Foursquare;

use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class VenueManager
 *
 * @package Tastd\Bundle\CoreBundle\Foursquare
 */
class VenueManager
{
    protected $prices;
    protected $cuisines;
    /** @var CuisineRepository */
    protected $cuisineRepository;

    /**
     * @param CuisineRepository $cuisineRepository
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(CuisineRepository $cuisineRepository, GeonameRepository $geonameRepository)
    {
        $this->prices = array(5, 15, 25, 50, 80);
        $this->cuisineRepository = $cuisineRepository;
        $this->geonameRepository = $geonameRepository;
        $this->cuisines = array(
            'Afghan Restaurant' => 'Asian (other)',
            'African Restaurant' => 'African',
            'Ethiopian Restaurant' => 'Ethiopian',
            'American Restaurant' => 'American',
            'New American Restaurant' => 'New American',
            'Asian Restaurant' => 'Asian (other)',
            'Cambodian Restaurant' => 'Asian (other)',
            'Filipino Restaurant' => 'Asian (other)',
            'Himalayan Restaurant' => 'Asian (other)',
            'Hotpot Restaurant' => 'Asian (other)',
            'Japanese Restaurant' => 'Japanese',
            'Donburi Restaurant' => 'Japanese',
            'Japanese Curry Restaurant' => 'Japanese',
            'Kaiseki Restaurant' => 'Japanese',
            'Kushikatsu Restaurant' => 'Japanese',
            'Monjayaki Restaurant' => 'Japanese',
            'Nabe Restaurant' => 'Japanese',
            'Okonomiyaki Restaurant' => 'Japanese',
            'Ramen Restaurant' => 'Ramen',
            'Shabu-Shabu Restaurant' => 'Japanese',
            'Soba Restaurant' => 'Japanese',
            'Sukiyaki Restaurant' => 'Japanese',
            'Sushi Restaurant' => 'Sushi',
            'Takoyaki Place' => 'Japanese',
            'Tempura Restaurant' => 'Japanese',
            'Tonkatsu Restaurant' => 'Japanese',
            'Udon Restaurant' => 'Japanese',
            'Unagi Restaurant' => 'Japanese',
            'Wagashi Place' => 'Japanese',
            'Yakitori Restaurant' => 'Japanese',
            'Yoshoku Restaurant' => 'Japanese',
            'Korean Restaurant' => 'Korean',
            'Malaysian Restaurant' => 'Malaysian',
            'Mongolian Restaurant' => 'Asian (other)',
            'Noodle House' => 'Japanese',
            'Thai Restaurant' => 'Thai',
            'Tibetan Restaurant' => 'Asian (other)',
            'Vietnamese Restaurant' => 'Vietnamese',
            'Australian Restaurant' => 'Other',
            'Austrian Restaurant' => 'Local/Traditional',
            'BBQ Joint' => 'Barbecue',
            'Bagel Shop' => 'Bakery',
            'Bakery' => 'Bakery',
            'Belgian Restaurant' => 'Local/Traditional',
            'Bistro' => 'French',
            'Brazilian Restaurant' => 'Brazilian',
            'Acai House' => 'Brazilian',
            'Baiano Restaurant' => 'Brazilian',
            'Central Brazilian Restaurant' => 'Brazilian',
            'Churrascaria' => 'Brazilian',
            'Empada House' => 'Brazilian',
            'Goiano Restaurant' => 'Brazilian',
            'Mineiro Restaurant' => 'Brazilian',
            'Northeastern Brazilian Restaurant' => 'Brazilian',
            'Northern Brazilian Restaurant' => 'Brazilian',
            'Pastelaria' => 'Brazilian',
            'Southeastern Brazilian Restaurant' => 'Brazilian',
            'Southern Brazilian Restaurant' => 'Brazilian',
            'Tapiocaria' => 'Brazilian',
            'Breakfast Spot' => 'Brunch',
            'Bubble Tea Shop' => 'Cafe',
            'Buffet' => 'Delicatessen',
            'Burger Joint' => 'Hamburger',
            'Cafeteria' => 'Cafe',
            'Café' => 'Cafe',
            'Cajun / Creole Restaurant' => 'Caribbean',
            'Caribbean Restaurant' => 'Caribbean',
            'Caucasian Restaurant' => 'Asian (other)',
            'Chinese Restaurant' => 'Chinese',
            'Anhui Restaurant' => 'Chinese',
            'Beijing Restaurant' => 'Chinese',
            'Cantonese Restaurant' => 'Chinese',
            'Chinese Aristocrat Restaurant' => 'Chinese',
            'Chinese Breakfast Place' => 'Chinese',
            'Dim Sum Restaurant' => 'Chinese',
            'Dongbei Restaurant' => 'Chinese',
            'Fujian Restaurant' => 'Chinese',
            'Guizhou Restaurant' => 'Chinese',
            'Hainan Restaurant' => 'Chinese',
            'Hakka Restaurant' => 'Chinese',
            'Henan Restaurant' => 'Chinese',
            'Hong Kong Restaurant' => 'Chinese',
            'Huaiyang Restaurant' => 'Chinese',
            'Hubei Restaurant' => 'Chinese',
            'Hunan Restaurant' => 'Chinese',
            'Imperial Restaurant' => 'Chinese',
            'Jiangsu Restaurant' => 'Chinese',
            'Jiangxi Restaurant' => 'Chinese',
            'Macanese Restaurant' => 'Chinese',
            'Manchu Restaurant' => 'Chinese',
            'Peking Duck Restaurant' => 'Chinese',
            'Shaanxi Restaurant' => 'Chinese',
            'Shandong Restaurant' => 'Chinese',
            'Shanghai Restaurant' => 'Chinese',
            'Shanxi Restaurant' => 'Chinese',
            'Szechuan Restaurant' => 'Chinese',
            'Taiwanese Restaurant' => 'Chinese',
            'Tianjin Restaurant' => 'Chinese',
            'Xinjiang Restaurant' => 'Chinese',
            'Yunnan Restaurant' => 'Chinese',
            'Zhejiang Restaurant' => 'Chinese',
            'Coffee Shop' => 'Café',
            'Comfort Food Restaurant' => 'Global/International',
            'Creperie' => 'Delicatessen',
            'Czech Restaurant' => 'Eastern European',
            'Deli / Bodega' => 'Delicatessen',
            'Dessert Shop' => 'Delicatessen',
            'Cupcake Shop' => 'Delicatessen',
            'Donut Shop' => 'Delicatessen',
            'Frozen Yogurt' => 'Ice cream',
            'Ice Cream Shop' => 'Ice cream',
            'Pie Shop' => 'Delicatessen',
            'Diner' => 'Global/International',
            'Distillery' => 'Wine bar',
            'Dumpling Restaurant' => 'Chinese',
            'Eastern European Restaurant' => 'Eastern European',
            'Belarusian Restaurant' => 'Eastern European',
            'Romanian Restaurant' => 'Eastern European',
            'Tatar Restaurant' => 'Eastern European',
            'English Restaurant' => 'British',
            'Falafel Restaurant' => 'Turkish',
            'Fast Food Restaurant' => 'Hamburger',
            'Fish & Chips Shop' => 'British',
            'Fondue Restaurant' => 'French',
            'Food Court' => 'Global/International',
            'Food Truck' => 'Local/Traditional',
            'French Restaurant' => 'French',
            'Fried Chicken Joint' => 'Hamburger',
            'Gastropub' => 'Pub',
            'German Restaurant' => 'German',
            'Gluten-free Restaurant' => 'Other',
            'Greek Restaurant' => 'Greek',
            'Bougatsa Shop' => 'Greek',
            'Cretan Restaurant' => 'Greek',
            'Fish Taverna' => 'Greek',
            'Grilled Meat Restaurant' => 'Greek',
            'Kafenio' => 'Greek',
            'Magirio' => 'Greek',
            'Meze Restaurant' => 'Greek',
            'Modern Greek Restaurant' => 'Greek',
            'Ouzeri' => 'Greek',
            'Patsa Restaurant' => 'Greek',
            'Souvlaki Shop' => 'Greek',
            'Taverna' => 'Greek',
            'Tsipouro Restaurant' => 'Greek',
            'Halal Restaurant' => 'Middle Eastern (other)',
            'Hawaiian Restaurant' => 'Caribbean',
            'Hot Dog Joint' => 'Hamburger',
            'Hungarian Restaurant' => 'Eastern European',
            'Indian Restaurant' => 'Indian',
            'Andhra Restaurant' => 'Indian',
            'Awadhi Restaurant' => 'Indian',
            'Bengali Restaurant' => 'Indian',
            'Chaat Place' => 'Indian',
            'Chettinad Restaurant' => 'Indian',
            'Dhaba' => 'Indian',
            'Dosa Place' => 'Indian',
            'Goan Restaurant' => 'Indian',
            'Gujarati Restaurant' => 'Indian',
            'Hyderabadi Restaurant' => 'Indian',
            'Indian Chinese Restaurant' => 'Indian',
            'Indian Sweet Shop' => 'Indian',
            'Irani Cafe' => 'Indian',
            'Jain Restaurant' => 'Indian',
            'Karnataka Restaurant' => 'Indian',
            'Kerala Restaurant' => 'Indian',
            'Maharashtrian Restaurant' => 'Indian',
            'Mughlai Restaurant' => 'Indian',
            'Multicuisine Indian Restaurant' => 'Indian',
            'North Indian Restaurant' => 'Indian',
            'Northeast Indian Restaurant' => 'Indian',
            'Parsi Restaurant' => 'Indian',
            'Punjabi Restaurant' => 'Indian',
            'Rajasthani Restaurant' => 'Indian',
            'South Indian Restaurant' => 'Indian',
            'Udupi Restaurant' => 'Indian',
            'Indonesian Restaurant' => 'Asian (other)',
            'Acehnese Restaurant' => 'Asian (other)',
            'Balinese Restaurant' => 'Asian (other)',
            'Betawinese Restaurant' => 'Asian (other)',
            'Indonesian Meatball Place' => 'Asian (other)',
            'Javanese Restaurant' => 'Asian (other)',
            'Manadonese Restaurant' => 'Asian (other)',
            'Padangnese Restaurant' => 'Asian (other)',
            'Sundanese Restaurant' => 'Asian (other)',
            'Irish Pub' => 'Pub',
            'Italian Restaurant' => 'Italian',
            'Abruzzo Restaurant' => 'Local/Traditional',
            'Agriturismo' => 'Local/Traditional',
            'Aosta Restaurant' => 'Local/Traditional',
            'Basilicata Restaurant' => 'Local/Traditional',
            'Calabria Restaurant' => 'Local/Traditional',
            'Campanian Restaurant' => 'Local/Traditional',
            'Emilia Restaurant' => 'Local/Traditional',
            'Friuli Restaurant' => 'Local/Traditional',
            'Ligurian Restaurant' => 'Local/Traditional',
            'Lombard Restaurant' => 'Local/Traditional',
            'Malga' => 'Local/Traditional',
            'Marche Restaurant' => 'Local/Traditional',
            'Molise Restaurant' => 'Local/Traditional',
            'Piadineria' => 'Local/Traditional',
            'Piedmontese Restaurant' => 'Local/Traditional',
            'Puglia Restaurant' => 'Local/Traditional',
            'Rifugio di Montagna' => 'Local/Traditional',
            'Romagna Restaurant' => 'Local/Traditional',
            'Roman Restaurant' => 'Local/Traditional',
            'Sardinian Restaurant' => 'Local/Traditional',
            'Sicilian Restaurant' => 'Local/Traditional',
            'South Tyrolean Restaurant' => 'Local/Traditional',
            'Trattoria/Osteria' => 'Local/Traditional',
            'Trentino Restaurant' => 'Local/Traditional',
            'Tuscan Restaurant' => 'Local/Traditional',
            'Umbrian Restaurant' => 'Local/Traditional',
            'Veneto Restaurant' => 'Local/Traditional',
            'Jewish Restaurant' => 'Kosher',
            'Kosher Restaurant' => 'Kosher',
            'Juice Bar' => 'Cafe',
            'Latin American Restaurant' => 'Other Southern American',
            'Arepa Restaurant' => 'Other Southern American',
            'Cuban Restaurant' => 'Caribbean',
            'Empanada Restaurant' => 'Other Southern American',
            'Mac & Cheese Joint' => 'Hamburger',
            'Mediterranean Restaurant' => 'Mediterranean',
            'Moroccan Restaurant' => 'Mediterranean',
            'Mexican Restaurant' => 'Mexican',
            'Burrito Place' => 'Mexican',
            'Taco Place' => 'Mexican',
            'Middle Eastern Restaurant' => 'Middle Eastern (other)',
            'Persian Restaurant' => 'Middle Eastern (other)',
            'Modern European Restaurant' => 'Modern European',
            'Molecular Gastronomy Restaurant' => 'Gourmet',
            'Pakistani Restaurant' => 'Asian (other)',
            'Pizza Place' => 'Pizza',
            'Polish Restaurant' => 'Eastern European',
            'Portuguese Restaurant' => 'Local/Traditional',
            'Restaurant' => 'Global/International',
            'Russian Restaurant' => 'Eastern European',
            'Blini House' => 'Eastern European',
            'Pelmeni House' => 'Eastern European',
            'Salad Place' => 'Sandwich',
            'Sandwich Place' => 'Sandwich',
            'Scandinavian Restaurant' => 'Northern European',
            'Seafood Restaurant' => 'Seafood',
            'Snack Place' => 'Delicatessen',
            'Soup Place' => 'Delicatessen',
            'South American Restaurant' => 'Other Southern American',
            'Argentinian Restaurant' => 'Argentinian',
            'Peruvian Restaurant' => 'Peruvian',
            'Southern / Soul Food Restaurant' => 'Other Southern American',
            'Spanish Restaurant' => 'Spanish',
            'Paella Restaurant' => 'Spanish',
            'Tapas Restaurant' => 'Tapas',
            'Sri Lankan Restaurant' => 'Asian (other)',
            'Steakhouse' => 'Steakhouse',
            'Swiss Restaurant' => 'Local/Traditional',
            'Tea Room' => 'Cafe',
            'Turkish Restaurant' => 'Turkish',
            'Borek Place' => 'Turkish',
            'Cigkofte Place' => 'Turkish',
            'Doner Restaurant' => 'Turkish',
            'Gozleme Place' => 'Turkish',
            'Kebab Restaurant' => 'Turkish',
            'Kofte Place' => 'Turkish',
            'Kokoreç Restaurant' => 'Turkish',
            'Manti Place' => 'Turkish',
            'Meyhane' => 'Turkish',
            'Pide Place' => 'Turkish',
            'Turkish Home Cooking Restaurant' => 'Turkish',
            'Ukrainian Restaurant' => 'Eastern European',
            'Varenyky restaurant' => 'Eastern European',
            'West-Ukrainian Restaurant' => 'Eastern European',
            'Vegetarian / Vegan Restaurant' => 'Vegetarian',
            'Winery' => 'Wine bar',
            'Wings Joint' => 'Hamburger',
            'Nightlife Spot' => 'Cocktail bar',
            'Bar' => 'Cocktail bar',
            'Beach Bar' => 'Cocktail bar',
            'Beer Garden' => 'Cocktail bar',
            'Champagne Bar' => 'Cocktail bar',
            'Cocktail Bar' => 'Cocktail bar',
            'Dive Bar' => 'Cocktail bar',
            'Gay Bar' => 'Cocktail bar',
            'Hookah Bar' => 'Cocktail bar',
            'Hotel Bar' => 'Club',
            'Karaoke Bar' => 'Club',
            'Pub' => 'Pub',
            'Sake Bar' => 'Cocktail bar',
            'Sports Bar' => 'Cocktail bar',
            'Whisky Bar' => 'Cocktail bar',
            'Wine Bar' => 'Wine bar',
            'Brewery' => 'Brewery',
            'Lounge' => 'Cocktail bar',
            'Night Market' => 'Cocktail bar',
            'Nightclub' => 'Club',
            'Other Nightlife' => 'Club',
            'Speakeasy' => 'Club',
            'Strip Club' => 'Club'
        );
    }


    /**
     * @param $venue
     *
     * @return Restaurant
     */
    public function getRestaurant($venue)
    {
        $restaurant = new Restaurant();
        $this->setPrice($restaurant, $venue);
        $this->setCuisine($restaurant, $venue);

        if (isset($venue['location']['address'])) {
            $restaurant->setAddress($venue['location']['address']);
        }

        $restaurant->setLat($venue['location']['lat']);
        $restaurant->setLng($venue['location']['lng']);
        $restaurant->setName($venue['name']);

        if (isset($venue['contact']['phone'])) {
            $restaurant->setPhone($venue['contact']['phone']);
        }

        if (isset($venue['url'])) {
            $restaurant->setWebsite($venue['url']);
        }

        return $restaurant;

    }

    /**
     * @param $venue
     *
     * @return Geoname
     */
    public function getGeoname ($venue)
    {
        $address = new Address();
        $address->setLat($venue['location']['lat']);
        $address->setLng($venue['location']['lng']);

        if (isset($venue['location']['city'])) {
            $address->setCity($venue['location']['city']);
        }
        if (isset($venue['location']['country'])) {
            $address->setCountry($venue['location']['country']);
        }
        if (isset($venue['location']['state'])) {
            $address->setCounty($venue['location']['state']);
        }
        if (isset($venue['location']['postalCode'])) {
            $address->setPostalCode($venue['location']['postalCode']);
        }
        $geoname = $this->geonameRepository->getOneByAddress($address);

        return $geoname;
    }

    /**
     * @param Restaurant $restaurant
     * @param $venue
     */
    protected function setPrice (Restaurant $restaurant, $venue)
    {
        if (isset($venue['price']) && isset($venue['price']['tier'])){
            $tier = $venue['price']['tier'];
            $restaurant->setAverageCost($this->prices[$tier]);
        }
    }

    /**
     * @param Restaurant $restaurant
     * @param $venue
     */
    protected function setCuisine(Restaurant $restaurant, $venue)
    {
        $catName = $this->getPrimaryCuisineName($venue);
        $cuisine = $this->cuisineRepository->getCuisineByName($catName);
        if (!$cuisine) {
            $cuisine = $this->cuisineRepository->getDefaultCuisine();
        }
        $restaurant->setCuisine($cuisine);
    }

    /**
     * @param $venue
     * @return mixed
     */
    protected function getPrimaryCuisineName($venue)
    {
        foreach ($venue['categories'] as $category) {
            if ($category['primary']) {
                $catName = $category['name'];
                if (isset($this->cuisines[$catName])) {
                    return $this->cuisines[$catName];
                }
            }
        }

        return 'Other';
    }
}