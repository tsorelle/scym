<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/26/2015
 * Time: 7:36 AM
 */
\Tops\sys\TObjectContainer::register('mailboxManager','\App\db\TScymMailboxManager','configManager');
// \Tops\sys\TObjectContainer::register('directoryManager','\App\db\TScymDirectoryManager');
\Tops\sys\TObjectContainer::register('tops.userfactory','\App\sys\ScymUserFactory');

// \Tops\sys\TObjectContainer::LoadConfig('di.yml');
