<?php

namespace App\Repository;

use App\Entity\Taha;
use App\Entity\Registration;
use App\Entity\SummitLocation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function countActiveForSummitLocation(SummitLocation $summitLocation): int
    {
        return (int) $this->createQueryBuilder('registration')
            ->select('COUNT(registration.id)')
            ->andWhere('registration.summitLocation = :summitLocation')
            ->andWhere('registration.status = :status')
            ->setParameter('summitLocation', $summitLocation)
            ->setParameter('status', Registration::STATUS_ACTIVE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Registration[]
     */
    public function findBookingsForUser(Taha $user): array
    {
        return $this->createQueryBuilder('registration')
            ->addSelect('summitLocation')
            ->join('registration.summitLocation', 'summitLocation')
            ->andWhere('registration.user = :user')
            ->setParameter('user', $user)
            ->orderBy('summitLocation.eventDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveForUserAndSummitLocation(Taha $user, SummitLocation $summitLocation): ?Registration
    {
        return $this->createQueryBuilder('registration')
            ->andWhere('registration.user = :user')
            ->andWhere('registration.summitLocation = :summitLocation')
            ->andWhere('registration.status = :status')
            ->setParameter('user', $user)
            ->setParameter('summitLocation', $summitLocation)
            ->setParameter('status', Registration::STATUS_ACTIVE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Registration[]
     */
    public function findForAdmin(?string $status, ?string $city, string $sort, string $direction): array
    {
        $queryBuilder = $this->createAdminQueryBuilder($status, $city);
        $sortMap = [
            'createdAt' => 'registration.createdAt',
            'eventDate' => 'summitLocation.eventDate',
        ];

        $queryBuilder->orderBy($sortMap[$sort] ?? 'registration.createdAt', strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return string[]
     */
    public function findAdminCities(): array
    {
        $rows = $this->createQueryBuilder('registration')
            ->select('DISTINCT summitLocation.city AS city')
            ->join('registration.summitLocation', 'summitLocation')
            ->orderBy('summitLocation.city', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['city'], $rows);
    }

    private function createAdminQueryBuilder(?string $status, ?string $city): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('registration')
            ->addSelect('user')
            ->addSelect('summitLocation')
            ->join('registration.user', 'user')
            ->join('registration.summitLocation', 'summitLocation');

        if ($status !== null && $status !== '') {
            $queryBuilder
                ->andWhere('registration.status = :status')
                ->setParameter('status', $status);
        }

        if ($city !== null && $city !== '') {
            $queryBuilder
                ->andWhere('summitLocation.city = :city')
                ->setParameter('city', $city);
        }

        return $queryBuilder;
    }
}
