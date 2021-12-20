<?php
declare(strict_types=1);

namespace Kuke\K8sConfigDiscovery;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;

class Client implements ClientInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    protected $config;


    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function pull(): array
    {
        $listener = $this->config->get('config_center.drivers.k8s.listener_config', []);
        $config = [];
        $base_path = $this->config->get('config_center.drivers.k8s.base_path');
        foreach ($listener as $item) {
            $key = basename($item, '.php');
            $filePath = $base_path . $item;
            if (!file_exists($filePath)) {
               continue;
            }
            $scrapy = file_get_contents($filePath);
            $scrapy = ltrim($scrapy, '<?php');
            $scrapy = rtrim($scrapy, '?>');
            $scrapy = str_replace(PHP_EOL, '' ,$scrapy);
            $value = eval($scrapy);
            if (!$value) {
                $this->logger->error(sprintf('The config of %s read failed from k8s.', $key));
                continue;
            }
            $config[$key] = $value;
        }
        return $config;
    }
}

