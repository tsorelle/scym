<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:19 AM
 */

namespace App\services\directory;


class InitDirectoryResponse
{
    /**
     * @var boolean
     */
    public $canEdit;
    /**
     * @var \stdClass[]
     */
    public $directoryListingTypes; // : INameValuePair[];
    /**
     * @var \stdClass[]
     */
    public $affiliationCodes;// : INameValuePair[];

    /**
     * @var \stdClass
     */
    public $family; // : IScymFamily
}