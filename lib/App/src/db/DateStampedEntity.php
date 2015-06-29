<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/29/2015
 * Time: 4:44 AM
 */

namespace App\db;

abstract class DateStampedEntity {

    private $username;
    private function getUserName() {
        if (!isset($this->username)) {
            $this->username = '';
            $user = \Tops\sys\TUser::getCurrent();
            if ($user != null) {
                $this->username = $user->getUserName();
            }

            if (empty($this->username)) {
                $this->username = 'unknown';
            }
        }
        return $this->username;
    }

    public function setDateStamp($initial = true) {
        $now = new \DateTime();
        $username = $this->getUserName();
        if ($initial) {
            $this->setDateAdded($now);
            $this->setAddedBy($username);
        }
        $this->setDateUpdated($now);
        $this->setUpdatedBy($username);
    }

    public function setUpdateStamp() {
        $this->setDateStamp(false);
    }

    /**
     * Set dateadded
     *
     * @param \DateTime $dateadded
     * @return DateStampedEntity
     */
    public abstract function setDateAdded($dateadded);

    /**
     * Get dateadded
     *
     * @return \DateTime
     */
    public abstract function getDateAdded();

    /**
     * Set dateupdated
     *
     * @param \DateTime $dateupdated
     * @return DateStampedEntity
     */
    public abstract function setDateUpdated($dateupdated);

    /**
     * Get dateupdated
     *
     * @return \DateTime
     */
    public abstract function getDateUpdated();


    /**
     * Set addedby
     *
     * @param string $addedby
     * @return DateStampedEntity
     */
    public abstract function setAddedBy($addedby);

    /**
     * Get addedby
     *
     * @return string
     */
    public abstract function getAddedBy();

    /**
     * Set updatedBy
     *
     * @param string $updatedby
     * @return DateStampedEntity
     */
    public abstract function setUpdatedBy($updatedby);

    /**
     * Get updatedby
     *
     * @return string
     */
    public abstract function getUpdatedBy();





}