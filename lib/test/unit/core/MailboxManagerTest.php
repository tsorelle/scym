<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/24/2015
 * Time: 8:27 AM
 */

class MailboxManagerTest extends PHPUnit_Framework_TestCase {

    public function testMemoryMailbox() {
        $mgr = new \Tops\sys\TMemoryMailboxManager();
        $box = $mgr->addMailbox("TEST","Test box","test@mailboxes.org","Test Mail box");
        $this->assertNotNull($box);
        $box2 = $mgr->addMailbox("TEST2","Test box 2","test2@mailboxes.org","Test Mail box two");
        $this->assertNotNull($box2);
        $mgr->addMailbox("TEST3","Test box 3","test3@mailboxes.org","Test Mail box three");
        $this->assertEquals(3,$mgr->getCount());

        $mgr->drop($box2->getMailboxId());
        $this->assertEquals(2,$mgr->getCount());

    }
}
