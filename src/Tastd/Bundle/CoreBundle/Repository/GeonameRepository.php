<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Geo\UnresolvedGeonameException;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\GeonameNotFoundException;
use Tastd\Bundle\CoreBundle\Pager\OutOfRangePager;

/**
 * Class GeonameRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class GeonameRepository extends BaseEntityRepository
{


    /**
     * @param integer $id
     *
     * @return Geoname
     * @throws GeonameNotFoundException
     */
    public function get($id)
    {
        $geoname = $this->find($id);
        if (!$geoname) {
            throw new GeonameNotFoundException();
        }

        return $geoname;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Pagerfanta
     */
    public function getAll(Request $request, User $user = null)
    {
        // Handle deprecated parameter < client 1.7.2
        $experiencedBy = $request->query->get('experiencedBy');
        if ($experiencedBy) {
            $request->query->set('user', $experiencedBy);
        }

        $asciiName = $request->query->get('asciiName');
        $pageNumber = $request->query->get('page', 1);
        $country = $request->query->get('country');
        $lng = $request->query->get('lng', 0);
        $lat = $request->query->get('lat', 0);
        $orderBy = $request->query->get('orderBy');
        $userId = $request->query->get('user');

        if ($userId) {
            return $this->getAllExperiencedBy($request, $user);
        }

        // @TODO fix client
        if (isset($asciiName) && trim($asciiName) === '') {
            $asciiName = null;
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g');

        if ($asciiName) {
            $queryBuilder
                ->andWhere('g.asciiName LIKE :asciiName')
                ->setParameter('asciiName', $asciiName . '%');
        }

        if ($country) {
            $queryBuilder
                ->andWhere('g.country = :country')
                ->setParameter('country', $country);
        }

        if ($orderBy === 'distance') {
            $queryBuilder
                ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
                ->setParameter('lat', $lat)
                ->setParameter('lng', $lng)
                ->orderBy('distance', 'ASC');
        }

        if (!isset($orderBy)) {
            $queryBuilder
                ->orderBy('g.population', 'DESC');
        }

        return $this->getPager($queryBuilder, $pageNumber);
    }

    /**
     * @return array
     */
    public function getFeatured()
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->where('g.id IN (:ids)')
            ->setParameter('ids', array(
                5128581, // New York
                3173435, // Milan
                2643743, // London
                2988507, // Paris
            ));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return ArrayAdapter
     */
    public function getAllExperiencedBy(Request $request, User $user)
    {
        $userId = $request->query->get('user');
        $pageNumber = $request->query->get('page', 1);

        $sql = '
            SELECT DISTINCT
              geonames.id,
              ascii_name as asciiName,
              lat,
              lng,
              country,
              admin1,
              population,
              timezone,
              ascii_name as formattedName,
              currency_symbol as currencySymbol,
              currency_code as currencyCode
            FROM geonames
            RIGHT JOIN reviews ON geonames.id = reviews.geoname_id
            WHERE reviews.user_id = :user_id
            OR geonames.id = :geonames_id
            ORDER BY geonames.population DESC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geonames_id', $user->getId());
        $stmt->execute();
        $results = $stmt->fetchAll();
        $adapter = new ArrayAdapter($results);
        $pager = new Pagerfanta($adapter);
        try {
            $pager = $pager->setCurrentPage($pageNumber);
        } catch (\Exception $e) {
            $adapter = new ArrayAdapter(array());
            $pager = new OutOfRangePager($adapter);
            $pager->setOriginalPage($pageNumber);
        }
        $pager->setMaxPerPage(20);

        return $pager;
    }

    /**
     * @param string $asciiName
     *
     * @return array
     */
    public function searchByAsciiName($asciiName)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->where('g.asciiName LIKE :asciiName')
            ->setParameter('asciiName', $asciiName . '%')
            ->setMaxResults(5);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $asciiName
     * @param string $country
     * @param string $lat
     * @param string $lng
     *
     * @return array
     */
    public function getByAsciiNameCountryLatLng($asciiName, $country, $lat, $lng, $page = 1)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->andWhere('g.asciiName LIKE :asciiName')
            ->andWhere('g.country = :country')
            ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->setParameter('country', $country)
            ->setParameter('asciiName', $asciiName . '%')
            ->orderBy('distance', 'ASC');

        return $this->getPager($queryBuilder, $page);
    }

    /**
     * @param Address $address
     *
     * @return mixed
     * @throws UnresolvedGeonameException
     */
    public function getOneByAddress(Address $address)
    {
        if ($address->getCity() === null) {
            return $this->getNearestCity($address);
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->leftJoin('g.translations', 't')
            ->where('g.asciiName = :asciiName')
            ->having('distance < 1')
            ->setParameter('lat', $address->getLat())
            ->setParameter('lng', $address->getLng())
            ->setParameter('asciiName', $address->getCity());

        $query = $queryBuilder->getQuery();


        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return $this->getOneByAddressTranslation($address);
        } catch (NonUniqueResultException $e) {
            return $this->getNearestCity($address);
        }
    }

    /**
     * @param Address $address
     *
     * @return mixed
     * @throws UnresolvedGeonameException
     */
    public function getOneByAddressTranslation(Address $address)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->leftJoin('g.translations', 't')
            ->where('t.alternateName = :alternateName')
            ->having('distance < 1')
            ->setParameter('lat', $address->getLat())
            ->setParameter('lng', $address->getLng())
            ->setParameter('alternateName', $address->getCity());

        $query = $queryBuilder->getQuery();
        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return $this->getNearestCity($address);
        } catch (NonUniqueResultException $e) {
            return $this->getNearestCity($address);
        }
    }

    /**
     * @param Address $address
     *
     * @return mixed
     * @throws UnresolvedGeonameException
     */
    public function getNearestCity(Address $address)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->leftJoin('g.translations', 't')
            ->setParameter('lat', $address->getLat())
            ->setParameter('lng', $address->getLng())
            ->orderBy('distance', 'ASC');

        $query = $queryBuilder->getQuery()->setMaxResults(1);
        $results = $query->getResult();

        if (is_array($results) && count($results)>0) {
            return $results[0];
        }

        throw new UnresolvedGeonameException();
    }

    /**
     * @param Geoname $geoname
     *
     * @return array
     */
    public function getNearbyGeonames(Geoname $geoname)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->addSelect('DISTANCE(g.lat,g.lng,:lat,:lng) AS HIDDEN distance')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->setParameter('lat', $geoname->getLat())
            ->setParameter('lng', $geoname->getLng())
            ->orderBy('distance', 'ASC');
        $query = $queryBuilder->getQuery()->setMaxResults(30);

        return $query->getResult();
    }

    /**
     * @param Geoname $geoname
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return boolean
     */
    public function checkIfUsed(Geoname $geoname)
    {
        $id = $geoname->getId();
        $sql = 'SELECT(
            (SELECT COUNT(reviews.geoname_id) FROM reviews WHERE geoname_id = '.$id.') +
            (SELECT COUNT(users.geoname_id) FROM users WHERE geoname_id = '.$id.') +
            (SELECT COUNT(wishes.geoname_id) FROM wishes WHERE geoname_id = '.$id.'6) +
            (SELECT COUNT(restaurants.geoname_id) FROM restaurants WHERE geoname_id = '.$id.')
            ) as geonameCount';

        $result = $this->getEntityManager()->getConnection()->fetchAll($sql);

        return $result[0]['geonameCount'] > 0;
    }

    /**
     * @param int $top
     *
     * @return array
     */
    public function findEuroTop($top = 100)
    {
        $topCountries = array(
            "AD",
            "AT",
            "BE",
            "BG",
            "CH",
            "CZ",
            "DE",
            "DK",
            "EE",
            "ES",
            "FI",
            "FR",
            "GB",
            "GR",
            "HR",
            "HU",
            "IE",
            "IT",
            "LI",
            "LU",
            "MC",
            "MK",
            "MT",
            "NL",
            "NO",
            "PT",
            "RO",
            "SE",
            "SK",
            "SM",
            "TR"
        );
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->where('g.country IN (:country)')
            ->setParameter('country', $topCountries)
            ->orderBy('g.population', 'DESC');
        $query = $queryBuilder->getQuery()->setMaxResults($top);

        return $query->getResult();

    }

    /**
     * @param int $top
     *
     * @return array
     */
    public function findUsTop($top = 100)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Geoname::SHORTCUT_CLASS_NAME, 'g')
            ->where('g.country = :country')
            ->setParameter('country', 'US')
            ->orderBy('g.population', 'DESC');
        $query = $queryBuilder->getQuery()->setMaxResults($top);

        return $query->getResult();
    }


}