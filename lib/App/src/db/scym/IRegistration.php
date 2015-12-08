<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/3/2015
 * Time: 10:24 AM
 */
namespace App\db\scym;


interface IRegistration
{
    /**
     * Get registrationId
     *
     * @return integer
     */
    public function getRegistrationId();

    /**
     * Get year
     *
     * @return string
     */
    public function getYear();

    /**
     * Get registrationCode
     *
     * @return string
     */
    public function getRegistrationCode();

    /**
     * Get statusId
     *
     * @return integer
     */
    public function getStatusId();

    /**
     * Get name
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Get city
     *
     * @return string
     */
    public function getCity();

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get receivedDate
     *
     * @return \DateTime
     */
    public function getReceivedDate();

    /**
     * Get amountPaid
     *
     * @return string
     */
    public function getAmountPaid();

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes();

    /**
     * Get feesReceivedDate
     *
     * @return \DateTime
     */
    public function getFeesReceivedDate();

    /**
     * Get contactRequested
     *
     * @return boolean
     */
    public function getContactRequested();

    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed();

    /**
     * Get scymNotes
     *
     * @return string
     */
    public function getScymNotes();

    /**
     * Get financialAidContribution
     *
     * @return string
     */
   //  public function getFinancialAidContribution();

    /**
     * Get attended
     *
     * @return boolean
     */
    public function getAttended();

    /**
     * Get financialAidAmount
     *
     * @return string
     */
    public function getFinancialAidAmount();
}