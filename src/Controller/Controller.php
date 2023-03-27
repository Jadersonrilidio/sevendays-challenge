<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Core\View;
use Jayrods\ScubaPHP\Utils\FlashMessage;

abstract class Controller
{
    /**
     * 
     */
    protected view $view;

    /**
     * 
     */
    protected FlashMessage $flashMsg;

    /**
     * 
     */
    public function __construct(view $view, FlashMessage $flashMsg)
    {
        $this->view = $view;
        $this->flashMsg = $flashMsg;
    }
}
