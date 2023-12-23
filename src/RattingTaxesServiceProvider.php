<?php

namespace Helious\SeatRattingTaxes;

use Seat\Services\AbstractSeatPlugin;


class RattingTaxesServiceProvider extends AbstractSeatPlugin
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/seat-ratting-taxes.php', 'seat-moons');
        $this->mergeConfigFrom(__DIR__ . '/Config/seat-ratting-taxes.sidebar.php', 'package.sidebar.tools.entries');
        $this->registerPermissions(__DIR__ . '/Config/seat-ratting-taxes.permissions.php', 'seat-moons');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'seat-ratting-taxes');
    }

    /**
     * Get the package's routes.
     *
     * @return string
     */
    protected function getRouteFile()
    {
        return __DIR__.'/routes.php';
    }

    

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     * @example SeAT Web
     *
     */
    public function getName(): string
    {
        return 'SeAT Ratting Taxes';
    }

    /**
     * Return the plugin repository address.
     *
     * @example https://github.com/eveseat/web
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/mackenziexD/seat-ratting-taxes';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     * @example web
     *
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-ratting-taxes';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     * @example eveseat
     *
     */
    public function getPackagistVendorName(): string
    {
        return 'helious';
    }
}