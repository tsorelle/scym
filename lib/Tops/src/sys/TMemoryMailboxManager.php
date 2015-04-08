<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/23/2015
 * Time: 3:02 PM
 */

namespace Tops\sys;

class TMemoryMailboxManager implements IMailboxManager {

    /**
     * @var IMailbox
     */
    private $boxes;
    private $compareByIdCallBack;
    private $compareByCodeCallBack;


    public function __construct(array $boxes = null) {
        $this->boxes = new TCollection($boxes);
        $this->compareByIdCallBack = function (IMailbox $mailBox, $idValue) {
            return $mailBox->getMailboxId() == $idValue;
        };
        $this->compareByCodeCallBack = function (IMailbox $mailBox, $code) {
            return $mailBox->getMailboxCode() == $code;
        };
    }

    /**
     *
     * Use with unit test
     *
     * @param $id
     * @return IMailbox
     */
    public function find($id)
    {
        return $this->boxes->first($this->compareByIdCallBack, $id);
    }

    /**
     * @param $id
     */
    public function drop($id)
    {
        $this->boxes->removeItem($this->compareByIdCallBack,$id);
    }

    /**
     * @param $mailboxCode
     * @return IMailbox
     */
    public function findByCode($mailboxCode)
    {
        return $this->boxes->first($this->compareByCodeCallBack,$mailboxCode);
    }

    /**
     * @param null $filter
     * @param null $arguments
     * @return IMailbox[]
     */
    public function getMailboxes($filter = null, $arguments = null)
    {
        if ($filter == null) {
            return $this->boxes->toArray();
        }
        $result = $this->boxes->filter($filter,$arguments);
        if ($result == null) {
            return null;
        }
        return $result->toArray();
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
        $box = $this->createMailbox($code, $name, $address, $description);
        $id = $this->boxes->getCount() + 1;
        $box->setMailboxId($id);
//        $this->boxes->add($box);
        $this->boxes->setItem($id,$box);
        return $box;
    }

    /**
     * @inheritdoc
     */
    public function createMailbox($code, $name, $address, $description) {
        return TMailbox::Create($code,$name,$address,$description);
    }

    /**
     * @param IMailbox $mailbox
     * @return int
     */
    public function updateMailbox(IMailbox $mailbox)
    {
        if ($mailbox->getMailboxId() < 1) {
            $this->boxes->add($mailbox);
        }
        else {
            $code = $mailbox->getMailboxCode();
            $key = $this->boxes->findKey($this->compareByCodeCallBack, $code);
            if ($key == null) {
                return false;
            }
            $this->boxes->set($key,$mailbox);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCount() {
        return $this->boxes->getCount();
    }

    protected function clearMailboxes() {
        $this->boxes->clear();
    }

    public function saveChanges() {
        // not implemented
    }


}