<?php

namespace GT\ImagineBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;


class ImagineExtension extends \Twig_Extension
{

    protected $container;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            'imagine_filter' => new \Twig_Filter_Method($this, 'filter'),
        );
    }

    /**
     * Gets cache path of an image to be filtered
     *
     * @param string $path
     * @param string $filter
     * @param boolean $absolute
     *
     * @return string
     */
    public function filter($path, $filter)
    {
        $imagine = $this->container->get('gt_imagine');
        return $imagine->filter($path, $filter);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'gt_imagine';
    }

}