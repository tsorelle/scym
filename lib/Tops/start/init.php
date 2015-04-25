<?php
/**
 * Initialize dependency injection.
 *
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/8/2014
 * Time: 6:02 PM
 */
/*
    Application specific objects should be registered in /App/start/init.php (or corresponding directory)
    e.g.
     \Tops\sys\TObjectContainer::Register('mailbox','\App\db\ScymMailbox');
*/

require_once(__DIR__.'/../../App/start/init.php');
// \Tops\sys\TErrorException::SetErrorHandler();
// \Tops\sys\TSession::Initialize();
\Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');
\Tops\sys\TObjectContainer::Register('mailer','\Tops\sys\TSwiftMailer','configManager');
\Tops\sys\TObjectContainer::Register('logManager','\Tops\sys\TLogManager','configManager,mailer');
\Tops\sys\TObjectContainer::Register('errorLogger','\Tops\sys\TLogger');
\Tops\sys\TObjectContainer::Register('traceLogger','\Tops\sys\TLogger',':trace');
\Tops\sys\TObjectContainer::Register('tracer','\Tops\sys\TTracer','configManager,traceLogger');
\Tops\sys\TObjectContainer::Register('exceptionHandler','\Tops\sys\TExceptionHandler','errorLogger,configManager');
\Tops\sys\TObjectContainer::Register('serviceFactory','\Tops\services\TServiceFactory','configManager');
\Tops\sys\TObjectContainer::Register('serviceHost','\Tops\services\TServiceHost','serviceFactory,tops.userfactory,exceptionHandler');
\Tops\sys\TObjectContainer::Register('mailboxManager','\App\test\TestMailboxManager');
\Tops\sys\TObjectContainer::Register('postoffice','\Tops\sys\TPostOffice','mailer,mailboxManager');

// Use with Drupal 7 for D8 emulation
\Tops\sys\TObjectContainer::Register('path.alias_manager','Drupal\Core\Path\AliasManager');
\Tops\sys\TObjectContainer::Register('current_user','\Drupal\tops\Identity\TDrupalAccountProxy');
\Tops\sys\TObjectContainer::Register('tops-controller','\Drupal\tops\Controller\TopsController');

// \Tops\sys\TTracer::setJsDebug(true);
// \Tops\sys\TTracer::On();




