<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/24/2015
 * Time: 8:27 AM
 */

class ScymMailboxManagerTest extends PHPUnit_Framework_TestCase {

    public function testScymMailbox() {
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');


        $mgr = new \App\db\TScymMailboxManager();
        $box = $mgr->addMailbox("TEST","Test box","test@mailboxes.org","Test Mail box");
        $this->assertNotNull($box);
        /*
        $box2 = $mgr->addMailbox("TEST2","Test box 2","test2@mailboxes.org","Test Mail box two");
        $this->assertNotNull($box2);
        $mgr->addMailbox("TEST3","Test box 3","test3@mailboxes.org","Test Mail box three");
        */
        $found = $mgr->findByCode("TEST");
        $this->assertNotNull($found);


        $mgr->drop($box->getMailboxId());



    }
}
