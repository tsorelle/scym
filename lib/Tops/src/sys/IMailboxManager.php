<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 2:34 PM
 */

namespace Tops\sys;


/**
 * Interface IMailboxManager
 * @package Tops\sys
 */
interface IMailboxManager {
    /**
     *
     * @param $id
     * @return IMailbox
     */
    public function find($id);

    /**
     * @param $id
     */
    public function drop($id);

    /**
     * @param $mailboxCode
     * @return IMailbox
     */
    public function findByCode($mailboxCode);

    /**
     * @param null $filter
     * @return IMailbox[]
     */
    public function getMailboxes($filter = null);

    /**
     * @param $code
     * @param $name
     * @param $address
     * @param $description
     * @return IMailbox
     */
    public function addMailbox($code,$name,$address,$description);

    /**
     * @param IMailbox $mailbox
     * @return int
     */
    public function updateMailbox(IMailbox $mailbox);

    /**
     * @param $code
     * @param $name
     * @param $address
     * @param $description
     * @return IMailbox
     */
    public function createMailbox($code, $name, $address, $description);

    public function saveChanges();

}