<?php

namespace Watcher\Watcher;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use Watcher\Exception\InvalidContainerException;
use Watcher\Watcher;

class WatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldGetArrayObject_whenGetContainer()
    {
        // Arrange
        $excepted = ArrayObject::class;

        // Act
        $target = new Watcher();
        $actual = $target->getContainer();

        // Assert
        $this->assertInstanceOf($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldGetStdClass_whenGetContainer_withCreateObjectPassStdClass()
    {
        // Arrange
        $container = new stdClass();
        $excepted = stdClass::class;

        // Act
        $target = new Watcher($container);
        $actual = $target->getContainer();

        // Assert
        $this->assertInstanceOf($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldGetSameArrayObject_whenGetContainer_withCreateObjectPassArrayObject()
    {
        // Arrange
        $excepted = new ArrayObject();

        // Act
        $target = new Watcher($excepted);
        $actual = $target->getContainer();

        // Assert
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidContainerException_whenCreateObjectPassNumber()
    {
        // Arrange
        $this->setExpectedException(InvalidContainerException::class);

        // Act
        new Watcher('123');
    }

    /**
     * @test
     */
    public function shouldCanGetFiles_whenShowSomeThing()
    {
        // Arrange
        $exceptedFile = __DIR__ . '/Fixtures/sample.log';
        $exceptedAlias = 'some-alias';
        $container = new ArrayObject();
        $container->append($exceptedFile);

        // Act
        $target = new Watcher($container);
        $target->setFile($exceptedAlias, $exceptedFile);
        $target->run(function ($actualAlias, $actualFile) use ($exceptedAlias, $exceptedFile) {
            PHPUnit_Framework_TestCase::assertEquals($exceptedFile, $actualFile);
            PHPUnit_Framework_TestCase::assertEquals($exceptedAlias, $actualAlias);
        });
    }

    /**
     * @test
     */
    public function shouldCanUsingArrayObject_whenShowSomeThing()
    {
        // Arrange
        $excepted = 'some-value';
        $container = new ArrayObject();
        $container->append($excepted);

        // Act
        $target = new Watcher($container);
        $target->setFile('alias', __DIR__ . '/Fixtures/sample.log');
        $target->run(function () use ($excepted) {
            /** @var ArrayObject $this */
            $data = $this->getArrayCopy();
            $actual = $data[0];

            PHPUnit_Framework_TestCase::assertEquals($excepted, $actual);
        });
    }

    /**
     * @test
     */
    public function shouldCanUsingOtherContainer_whenShowSomeThing()
    {
        // Arrange
        $excepted = 'some-value';
        $container = new \Pimple\Container();
        $container['some-key'] = $excepted;

        // Act
        $target = new Watcher($container);
        $target->setFile('alias', __DIR__ . '/Fixtures/sample.log');
        $target->run(function () use ($excepted) {
            /** @var \Pimple\Container $this */
            $actual = $this['some-key'];

            PHPUnit_Framework_TestCase::assertEquals($excepted, $actual);
        });
    }
}
