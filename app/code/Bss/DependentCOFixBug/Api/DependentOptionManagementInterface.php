<?php
namespace Bss\DependentCOFixBug\Api;

interface DependentOptionManagementInterface
{
    /**
     * @return \Bss\DependentCustomOption\Api\Data\DependentOptionConfigInterface
     */
    public function getConfig();
}
