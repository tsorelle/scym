<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/23/2015
 * Time: 3:01 PM
 */

namespace Tops\sys;


class TConfigMailboxManager implements IMailboxManager {

    /**
     *
     * @param $id
     * @return IMailbox
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param $id
     */
    public function drop($id)
    {
        // TODO: Implement drop() method.
    }

    /**
     * @param $mailboxCode
     * @return IMailbox
     */
    public function findByCode($mailboxCode)
    {
        // TODO: Implement findByCode() method.
    }

    /**
     * @param null $filter
     * @return IMailbox[]
     */
    public function getMailboxes($filter = null)
    {
        // TODO: Implement getMailboxes() method.
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
        // TODO: Implement addMailbox() method.
    }

    /**
     * @param IMailbox $mailbox
     * @return int
     */
    public function updateMailbox(IMailbox $mailbox)
    {
        // TODO: Implement updateMailbox() method.
    }

    /**
     * @param $code
     * @param $name
     * @param $address
     * @param $description
     * @return IMailbox
     */
    public function createMailbox($code, $name, $address, $description)
    {
        return TMailbox::Create($code, $name, $address, $description);
    }

    public function saveChanges()
    {
        // not implemented
    }
}