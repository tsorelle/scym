<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/13/2015
 * Time: 12:18 PM
 */
namespace tops\scripts\buildproxies;

use Tops\db\TEntityManagers;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\scym\ScymMeeting;
use App\db\scym\ScymQuarterlyMeeting;
use Doctrine\ORM\EntityManager;


class program
{
    /**
     * @var  EntityManager
     */
    private $em;

    public function run()
    {
        $this->em = TEntityManagers::Get('application',true);
        print "Building entity proxies...\n";
        print "ScymAddress\n";
        $this->testEntity('ScymAddress');
        print "ScymQuarterlyMeeting\n";
        $this->testEntity('ScymQuarterlyMeeting');
        print "ScymMeeting\n";
        $this->testEntity('ScymMeeting');
        print "ScymPerson\n";
        $this->testEntity('ScymPerson');
    }

    public function testEntity($className,$testCriteria=null)
    {
        if (!$testCriteria) {
            $testCriteria = array('active' => 1);
        }
        $repository = $this->em->getRepository("App\\db\\scym\\$className");
        $e = $repository->findOneBy($testCriteria);

        if (!$e) {
            print "Failed to find entity\n";
        }
        return $e;

    }

}
$program = new program();
$program->run();
