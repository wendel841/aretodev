<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBySlug($slug)
    {
        $data = $this->findBy([
            'slug' => $slug
        ]);

        if (empty($data)) {
            return;
        }

        return $data[0];
    }
}