<?php
declare(strict_types=1);

namespace Local\AreaBoot\Plugin;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\FrontController as Fc;

class FrontControllerAreaGuard
{
    private State $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public function aroundDispatch(Fc $subject, callable $proceed, RequestInterface $request)
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            $this->state->setAreaCode('frontend');
        }

        return $proceed($request);
    }
}