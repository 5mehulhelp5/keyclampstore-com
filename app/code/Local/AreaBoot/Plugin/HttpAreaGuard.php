<?php
declare(strict_types=1);

namespace Local\AreaBoot\Plugin;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class HttpAreaGuard
{
    private State $state;
    private LoggerInterface $logger;

    public function __construct(State $state, LoggerInterface $logger)
    {
        $this->state  = $state;
        $this->logger = $logger;
    }

    public function aroundLaunch($subject, callable $proceed)
    {
        try {
            // Let core do its thing first
            return $proceed();
        } catch (\Throwable $e) {
            // Only intervene if the failure is due to missing area code
            if ($this->isAreaNotSet($e)) {
                try {
                    $this->state->setAreaCode('frontend');
                    $this->logger->info('HttpAreaGuard: set frontend after launch() failed once');
                } catch (LocalizedException $already) {
                    // If already set by something else, ignore and retry
                }
                // Retry once
                return $proceed();
            }
            // Not our problem, rethrow
            throw $e;
        }
    }

    private function isAreaNotSet(\Throwable $e): bool
    {
        if ($e instanceof LocalizedException && strpos((string)$e->getMessage(), 'Area code is not set') !== false) {
            return true;
        }
        // Walk the chain just in case it is wrapped
        $prev = $e->getPrevious();
        while ($prev) {
            if ($prev instanceof LocalizedException && strpos((string)$prev->getMessage(), 'Area code is not set') !== false) {
                return true;
            }
            $prev = $prev->getPrevious();
        }
        return false;
    }
}
