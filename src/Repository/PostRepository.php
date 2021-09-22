<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    /**
    * @var EntityManagerInterface
    */
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Post::class);
        $this->entityManager = $entityManager;
    }


    public function findAll()
    {
        $query = $this->entityManager
          ->createQueryBuilder()
          ->select('p.id, p.title, p.content, p.created_at, p.image, u.name')
          ->from(Post::class, 'p')
          ->leftJoin(User::class, 'u', 'with', "u.id = p.user")
          ->orderBy('p.id', 'DESC')
          ->getQuery()
          ;

        return $query->getResult();
    }
}
