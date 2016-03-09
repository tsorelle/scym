<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 8:16 AM
 */

namespace Fakes;



use Tops\sys\IContentManager;

class FakeContentManager implements IContentManager
{

    public function getText($pathAlias)
    {
        return $pathAlias.': processed';
    }
}