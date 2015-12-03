<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tastd\Bundle\CoreBundle\Entity\Post;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\PostNotFoundException;

/**
 * Class PostRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class PostRepository extends BaseEntityRepository
{
    /**
     * @param integer $id
     *
     * @return Post
     * @throws PostNotFoundException
     */
    public function get($id)
    {
        $post = $this->find($id);
        if (!$post) {
            throw new PostNotFoundException();
        }

        return $post;
    }


    /**
     * @return array
     */
    public function getAll()
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Post::SHORTCUT_CLASS_NAME, 'p')
            ->orderBy('p.created', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

}