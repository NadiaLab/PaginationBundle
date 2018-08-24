<?php

namespace Nadia\Bundle\PaginatorBundle\Factory;

use Nadia\Bundle\PaginatorBundle\Configuration\DependencyInjectionPaginatorTypeLoader;
use Nadia\Bundle\PaginatorBundle\Configuration\PaginatorBuilder;
use Nadia\Bundle\PaginatorBundle\Configuration\PaginatorTypeInterface;
use Nadia\Bundle\PaginatorBundle\Event\InputEvent;
use Nadia\Bundle\PaginatorBundle\Paginator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaginatorFactory
 */
class PaginatorFactory
{
    /**
     * @var DependencyInjectionPaginatorTypeLoader
     */
    private $typeLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $defaultOptions;

    /**
     * PaginatorFactory constructor.
     *
     * @param DependencyInjectionPaginatorTypeLoader $typeLoader
     * @param EventDispatcherInterface               $eventDispatcher
     * @param array                                  $defaultOptions
     */
    public function __construct(
        DependencyInjectionPaginatorTypeLoader $typeLoader,
        EventDispatcherInterface $eventDispatcher,
        array $defaultOptions = array()
    )
    {
        $this->typeLoader = $typeLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @return Paginator
     */
    public function createPaginator($type, array $options = array())
    {
        $type = $this->getType($type);
        $options = $this->resolveOptions($type, $options);
        $builder = new PaginatorBuilder();

        $type->build($builder, $options);

        $inputEvent = new InputEvent($builder, $options);

        $this->eventDispatcher->dispatch('nadia_paginator.input', $inputEvent);

        return new Paginator($builder, $options, $inputEvent->form, $inputEvent->input, $this->eventDispatcher);
    }

    /**
     * @param string $name PaginatorType class name
     *
     * @return PaginatorTypeInterface
     */
    private function getType($name)
    {
        if (!class_exists($name)) {
            throw new \InvalidArgumentException('Could not load type "'.$name.'": class does not exist.');
        }
        if (!is_subclass_of($name, 'Nadia\Bundle\PaginatorBundle\Configuration\PaginatorTypeInterface')) {
            throw new \InvalidArgumentException('Could not load type "'.$name.'": class does not implement "Nadia\Bundle\PaginatorBundle\Configuration\PaginatorTypeInterface".');
        }

        if ($this->typeLoader->hasType($name)) {
            return $this->typeLoader->getType($name);
        }

        return new $name();
    }

    /**
     * @param PaginatorTypeInterface $type
     * @param array                  $options
     *
     * @return array
     */
    private function resolveOptions(PaginatorTypeInterface $type, array $options)
    {
        $resolver = new OptionsResolver();

        $this->configureDefaultOptions($type, $resolver);

        $type->configureOptions($resolver);

        return $resolver->resolve($options);
    }

    /**
     * @param PaginatorTypeInterface $type
     * @param OptionsResolver        $resolver
     */
    private function configureDefaultOptions(PaginatorTypeInterface $type, OptionsResolver $resolver)
    {
        $defaultOptions = $this->defaultOptions;

        $defaultOptions['sessionKey'] = 'nadia.paginator.session.' . hash('md5', get_class($type));
        $defaultOptions['inputKeys'] = new $defaultOptions['inputKeysClass'];

        $resolver->setDefaults($defaultOptions);
    }
}
