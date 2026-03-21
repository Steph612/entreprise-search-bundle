<?php

namespace Steph612\EntrepriseSearchBundle\Tests;

use PHPUnit\Framework\TestCase;
use Steph612\EntrepriseSearchBundle\Steph612EntrepriseSearchBundle;

class Steph612EntrepriseSearchBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new Steph612EntrepriseSearchBundle();
        $this->assertInstanceOf(Steph612EntrepriseSearchBundle::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new Steph612EntrepriseSearchBundle();
        $path = $bundle->getPath();
        $this->assertDirectoryExists($path);
    }
}
