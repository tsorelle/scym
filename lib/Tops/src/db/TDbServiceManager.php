<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2015
 * Time: 3:30 PM
 */

namespace Tops\db;

use Doctrine\ORM\EntityRepository;
use Tops\db\TEntityManagers;

class TDbServiceManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    private $repositories = array();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager() {
        if (!(isset($this->entityManager))) {
            $this->entityManager = TEntityManagers::Get();
        }
        return $this->entityManager;
    }

    /**
     * @param $className
     * @return EntityRepository
     */
    protected function getRepository($className) {
        if (!isset($this->repositories[$className])) {
            $em = $this->getEntityManager();
            $repository = $em->getRepository($className);
            $this->repositories[$className] = $repository;
        }
        return $this->repositories[$className];
    }

    public function updateEntity($entity) {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function persistEntity($entity) {
        $em = $this->getEntityManager();
        $em->persist($entity);
    }


    public function deleteEntity($entity) {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }



    public function saveChanges() {
        if ($this->entityManager != null) {
            $this->entityManager->flush();
            $this->entityManager = null;
        }
    }


}