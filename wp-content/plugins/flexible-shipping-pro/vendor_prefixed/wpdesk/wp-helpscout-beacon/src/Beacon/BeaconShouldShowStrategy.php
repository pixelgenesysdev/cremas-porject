<?php

namespace FSProVendor\WPDesk\Beacon;

/**
 * When to show Beacon.
 */
interface BeaconShouldShowStrategy
{
    /**
     * Should Beacon be visible?
     *
     * @return bool
     */
    public function shouldDisplay();
}
