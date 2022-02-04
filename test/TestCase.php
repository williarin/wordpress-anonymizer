<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class TestCase extends BaseTestCase
{
    protected Connection $connection;
    protected ContainerInterface $container;
    protected string $tablePrefix;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(__DIR__ . '/../config/services.yaml');
        $this->container->compile(true);

        $this->connection = $this->container->get(Connection::class);
        $this->tablePrefix = $this->container->getParameter('table_prefix');

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
    }
}
