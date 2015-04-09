<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/23/2015
 * Time: 3:00 PM
 */

namespace Tops\db;
use \Tops\sys\IMailboxManager;
use \Tops\sys\IMailbox;


abstract class TDbMailboxManager implements IMailboxManager {

    /**
     * @return \Doctrine\ORM\EntityRepository The repository class.
     */
    private $repository;

    /**
     * @return \Doctrine\ORM\EntityManager The repository class.
     */
    private $entityManager;

    /**
     * @return \Doctrine\ORM\EntityManager The repository class.
     */
    private function getEntityManager() {
        if (!(isset($this->entityManager))) {
            $this->entityManager = TEntityManagers::Get();
        }
        return $this->entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository The repository class.
     */
    private function getRepository() {
        if ($this->repository == null) {
            $em = TEntityManagers::Get();
            $this->repository = $em->getRepository($this->getMailboxClassName());
        }
        return $this->repository;
    }

    /**
     * @return string
     */
    protected abstract function getCodeColumn();
    /**
     * @return string
     */
    protected abstract function getMailboxClassName();

    /**
     *
     * @param $id
     * @return IMailbox
     */
    public function find($id)
    {
        $repository = $this->getRepository();
        return $repository->find($id);
    }

    /**
     * @param $id
     */
    public function drop($id)
    {
        $current = $this->find($id);
        if ($current == null) {
            return;
        }
        $em = $this->getEntityManager();
        $em->remove($current);
        $em->flush();
    }

    /**
     * @param $mailboxCode
     * @return IMailbox
     */
    public function findByCode($mailboxCode)
    {
        $repository = $this->getRepository();
        $columnName = $this->getCodeColumn();

        return $repository->findOneBy(array($columnName => $mailboxCode));

    }

    /**
     * @param null $filter
     * @return IMailbox[]
     */
    public function getMailboxes($filter = null)
    {
        $repository = $this->getRepository();
        $repository->findAll();
    }

    /**
     * @param $code
     * @param $name
     * @param $address
     * @param $description
     * @return IMailbox
     */
    public function addMailbox($code, $name, $address, $description)
    {
        $newBox = $this->createMailbox($code, $name, $address, $description);
        $em = $this->getEntityManager();
        $em->persist($newBox);
        $em->flush();

        return $newBox;

    }

    /**
     * @param IMailbox $mailbox
     * @return int
     */
    public function updateMailbox(IMailbox $mailbox)
    {
        $current = null;
        $em = $this->getEntityManager();
        if ($mailbox->getMailboxId() > 0) {
            $current = $this->find($mailbox->getMailboxId());
        }

        if ($current == null) {
            $em->persist($mailbox);
        }
        else {
            $current->setDescription($mailbox->getDescription());
            $current->setMailboxCode($mailbox->getMailboxCode());
            $current->setName($mailbox->getName());
            $current->setDescription($mailbox->getDescription());
            $current->setEmail($mailbox->getEmail());
        }
        $em->flush();
    }


    /**
     * @param $code
     * @param $name
     * @param $address
     * @param $description
     * @return IMailbox
     */
    public function createMailbox($code, $name, $address, $description) {
        $result = $this->createMailboxEntity();
        $result->setMailboxCode($code);
        $result->setEmail($address);
        $result->setDescription($description);
        $result->setName($name);
        return $result;
    }

    /**
     * @return IMailbox
     */
    protected abstract function createMailboxEntity();

    public function saveChanges() {
        // not implemented
    }
}