<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/27/2015
 * Time: 8:35 AM
 */

namespace App\db;

use App\db\scym\ScymAnnualSession;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Tops\db\TDbServiceManager;
use Tops\sys\TListItem;

class ScymRegistrationsManager extends TDbServiceManager
{
    /**
     * @param null $year
     * @return null|ScymAnnualSession
     */
    public function getSession($year = null) {
        $repository =  $this->getRepository('App\db\scym\ScymAnnualSession');
        if (empty($year)) {
            $year = date("Y");
        }
        $result = $repository->find($year);
        if (empty($result)) {
            throw new Exception("Yearly meeting year $year is not in the ymdates table.");
        }
        return $result;
    }
}