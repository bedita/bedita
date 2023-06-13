<?php
declare(strict_types=1);

namespace BEdita\Core\Search;

/**
 * Abstract search adapter.
 */
abstract class BaseAdapter
{
    /**
     * Initialize adapter with configuration.
     *
     * @param array $config Adapter configuration
     * @return bool Success or failure
     */
    public function initialize(array $config): bool
    {
        return true;
    }
}
