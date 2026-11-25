<?php

declare(strict_types=1);

/*
 * Copyright OSIR. SPDX-License-Identifier: Apache-2.0.
 */

namespace ClientFence\Mod\Osir\Controller;

class Admin implements \ClientFence\InjectionAwareInterface
{
    protected ?\Pimple\Container $di = null;

    public function setDi(\Pimple\Container $di): void
    {
        $this->di = $di;
    }

    public function getDi(): ?\Pimple\Container
    {
        return $this->di;
    }

    /** @return array<string, list<array<string, int|string>>> */
    public function fetchNavigation(): array
    {
        $url = $this->container()['url'];
        if (!$url instanceof \ClientFence\Url) {
            throw new \LogicException('Unexpected url service.');
        }

        return [
            'subpages' => [
                [
                    'location' => 'system',
                    'index' => 151,
                    'label' => __trans('OSIR import'),
                    'uri' => $url->adminLink('osir'),
                    'class' => '',
                ],
            ],
        ];
    }

    private function container(): \Pimple\Container
    {
        return $this->di ?? throw new \LogicException('The dependency injection container has not been set.');
    }

    public function register(\ClientFence\App &$app): void
    {
        $app->get('/osir', 'get_index', [], static::class);
    }

    public function get_index(\ClientFence\App $app): string
    {
        $di = $this->container();
        $di['is_admin_logged'];
        $api = $di['api_admin'];
        if (!$api instanceof \ClientFence\Api\Proxy) {
            throw new \LogicException('Unexpected api_admin service.');
        }

        return $app->render('mod_osir_index', [
            'registrars' => $api->call('osir_registrars'),
            'max_tlds' => \ClientFence\Mod\Osir\Service::MAX_TLDS_PER_CALL,
            'max_domains' => \ClientFence\Mod\Osir\Service::MAX_DOMAINS_PER_CALL,
        ]);
    }
}
