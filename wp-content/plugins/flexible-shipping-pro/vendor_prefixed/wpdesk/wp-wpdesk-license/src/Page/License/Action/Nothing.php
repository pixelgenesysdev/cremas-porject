<?php

namespace FSProVendor\WPDesk\License\Page\License\Action;

use FSProVendor\WPDesk\License\Page\Action;
/**
 * Do nothing.
 *
 * @package WPDesk\License\Page\License\Action
 */
class Nothing implements Action
{
    public function execute(array $plugin)
    {
        // NOOP
    }
}
