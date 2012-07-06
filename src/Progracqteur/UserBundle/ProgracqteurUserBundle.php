<?php

namespace Progracqteur\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProgracqteurUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
