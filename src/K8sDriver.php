<?php
declare(strict_types=1);

namespace Kuke\K8sConfigDiscovery;

use Hyperf\ConfigCenter\AbstractDriver;
use Psr\Container\ContainerInterface;

class K8sDriver extends AbstractDriver
{
    /**
     * @var Client
     */
    protected $client;

    protected $driverName = 'k8s';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->client = $container->get(Client::class);
    }
}