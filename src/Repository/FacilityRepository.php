<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\FacilityType;
use App\Entity\User;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Facility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facility[]    findAll()
 * @method Facility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacilityRepository extends AbstractRepository
{
    /**
     * FacilityRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facility::class);
    }

    /**
     * @param $ids
     * @return int|mixed|string
     */
    public function getFacilitiesWithIds($ids)
    {
        return $this->createQueryBuilder('facility')
            ->where('facility.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * @param $id
     * @param array $latLngData
     * @param int $radius
     * @param bool|null $petsAllowed
     * @param bool|int $getClientCount
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFacilitiesByFilters($id, $latLngData, $radius, $petsAllowed, $getClientCount = false)
    {
        $clientSelect = "";
        $clientJoin = "";
        $clientGroupBy = "";

        if ($getClientCount) {
            $clientSelect = ", COUNT(cd.id) as count_clients";
            $clientJoin = " LEFT JOIN client_detail cd ON (cd.facility_id = f.id)";
            $clientGroupBy = " GROUP BY f.id";
        }

        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT f.id, f.name, f.address, f.available_beds, f.pets_allowed, f. hours_of_operation, f.status, f.created,
        f.lat, f.lng, f.city, f.state, f.zip_code, f.facility_type_id, f.dependents_allowed, f.work_all_day, f.opening_time,
         f.closing_time, f.primary_color, f.secondary_color, f.url_prefix, u.id as user_id, u.username, u.first_name, u.last_name, u.phone as contact_phone, 
         u.email as contact_email" . $clientSelect;

        if (!empty($latLngData )) {
            $lat = $latLngData['lat'];
            $lng = $latLngData['lng'];

            $query .= ", (6371 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng)) 
            + sin(radians($lat)) * sin(radians(lat)) )) as distance";
        }

        $query .= " FROM facility f LEFT JOIN fos_user u ON (f.user_id = u.id) ". $clientJoin ."
         WHERE f.status = " . StatusEnum::ACTIVE;

        if (!empty($id)) {
            $query .= " AND f.id = $id";
        }

        if (!is_null($petsAllowed)) {
            $query .= " AND f.pets_allowed = $petsAllowed";
        }

        $query .= $clientGroupBy;

        if (!empty($latLngData)) {
            $query .= " HAVING distance < " . ($radius * 1.609);        // To get result WRT miles
            $query .= " ORDER BY distance ASC";
        }

        $stmt = $conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param User $user
     * @param array $data
     * @param $desktopLogoFileSource
     * @param $mobileLogoFileSource
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFacility(User $user, array $data, $desktopLogoFileSource, $mobileLogoFileSource)
    {
        $facility = new Facility();
        $facility->setUser($user);
        $response = $this->setFacilityData($facility, $data, $desktopLogoFileSource, $mobileLogoFileSource);
        if ($response['status'] !== false) {
            $this->persist($facility);
        }

        return $response;
    }

    /**
     * @param Facility $facility
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFacility(Facility $facility)
    {
        $facility->setStatus(StatusEnum::INACTIVE);
        $facility->getUser()->setEnabled(StatusEnum::INACTIVE);
        $this->flush();
    }

    /**
     * @param Facility $facility
     * @param $data
     * @param $desktopLogoFileSource
     * @param $mobileLogoFileSource
     * @return array|bool[]
     */
    public function setFacilityData(Facility $facility, $data, $desktopLogoFileSource, $mobileLogoFileSource)
    {
        isset($data['name']) ? $facility->setName($data['name']) : null;
        isset($data['street_address']) ? $facility->setAddress($data['street_address']) : null;
        isset($data['available_beds']) ? $facility->setAvailableBeds((int)$data['available_beds']) : null;
        isset($data['total_dependents']) ? $facility->setDependentsAllowed($data['total_dependents']) : null;
        isset($data['pets_allowed']) ? $facility->setPetsAllowed($data['pets_allowed']) : null;
        $facility->setStatus(StatusEnum::ACTIVE);
        isset($data['lat']) ? $facility->setLat($data['lat']) : null;
        isset($data['lng']) ? $facility->setLng($data['lng']) : null;
        isset($data['city']) ? $facility->setCity($data['city']) : null;
        isset($data['state']) ? $facility->setState($data['state']) : null;
        isset($data['zip_code']) ? $facility->setZipCode($data['zip_code']) : null;
        isset($data['primary_color']) ? $facility->setPrimaryColor($data['primary_color']) : null;
        isset($data['secondary_color']) ? $facility->setSecondaryColor($data['secondary_color']) : null;
        isset($data['url_prefix']) ? $facility->setUrlPrefix($data['url_prefix']) : null;
        $facility->setDesktopLogo($desktopLogoFileSource);
        $facility->setMobileLogo($mobileLogoFileSource);
        if (isset($data['work_all_day']) && $data['work_all_day']) {
            $facility->setWorkAllDay(true);
            $facility->setOpeningTime(null);
            $facility->setClosingTime(null);
        } else {
            $facility->setWorkAllDay(false);
            isset($data['opening_time']) ? $facility->setOpeningTime($data['opening_time']) : null;
            isset($data['closing_time']) ? $facility->setClosingTime($data['closing_time']) : null;
        }
        if (isset($data['type_id']) && (!$facility->getFacilityType() ||
                $facility->getFacilityType()->getId() != $data['type_id'])) {
            $facilityType = $this->getEntityManager()->getRepository(FacilityType::class)
                ->findOneBy(['id' => $data['type_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($facilityType)) {
                return ["status" => false, "message" => "Invalid Facility Type provided."];
            }
            $facility->setFacilityType($facilityType);
        }
        return ["status" => true];
    }
}
