<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/31/2014
 * Time: 7:54 AM
 */
require_once __DIR__ . '/../../core/vendor/autoload.php';
include (__DIR__."/../Tops/start/init.php");
\Tops\sys\TClassPath::Add('\Doctrine\ORM','vendor\doctrine\orm\lib\Doctrine\ORM');
\Tops\sys\TClassPath::Add('\Doctrine\DBAL','vendor\doctrine\dbal\lib\Doctrine\DBAL');
\Tops\sys\TClassPath::Add('\scym','App/src');

use Tops\sys\TConfig;
use Tops\db\TEntityManagers;
use scym\db\Person;
use scym\db\UserEntity;

if (class_exists('scym\db\Person',true)) {
    throw new \Exception("person entity class not found");
}

if (!class_exists('\Doctrine\ORM\EntityManager',true)) {

    throw new \Exception("manager class not found");
}


function show(Person $p) {
    print "\n";
    print $p->getFirstname().' '.$p->getLastname();
    print "\n\n";

    return $p;
}

$em = TEntityManagers::Get();
$repository = $em->getRepository('scym\db\Person');
$person = $repository->find(180);
show($person);
