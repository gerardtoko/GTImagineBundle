<?php

/*
 * This file is part of GT.
 *
 * (c) Gerard TOKO <gerard.toko@gt.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GT\ImagineBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use GT\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Finder\Finder;


class ImagineService
{

    /**
     *
     * @var type
     */
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
     *
     * @param type $imageParam
     * @param type $filterParam
     * @return type
     */
    public function filter($imageParam, $filterParam, $force = false)
    {

        $driver = $this->container->getParameter('gt_imagine.driver');
        $mkdir_mode = $this->container->getParameter('gt_imagine.mkdir_mode');
        $web_root = $this->container->getParameter('gt_imagine.web_root');
        $data_root = $this->container->getParameter('gt_imagine.data_root');
        $formats = $this->container->getParameter('gt_imagine.formats');
        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        $filterManager = new FilterManager($this->container);

        if ($filterManager->valid($filterParam, $imageParam, $web_root, $data_root, $filter_sets, $formats, $mkdir_mode)) {
            return $filterManager->filter($filter_sets, $filterParam, $imageParam, $driver, $data_root, $mkdir_mode, $web_root, $force);
        }
    }

    /**
     *
     * @param type $imageParam
     * @param type $filterParam
     * @return type
     */
    public function filterAll($filterParam, $force = false)
    {
        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        if (empty($filter_sets[$filterParam])) {
            throw new InvalidArgumentException("Invalid Filter Set $filterParam : Not Exist");
        }

        $filter_set = $filter_sets[$filterParam];
        $formats = $this->container->getParameter('gt_imagine.formats');
        $finder = new Finder();
        $regexFormats = implode("|", $formats);
        $directory = $filter_set["directory"];
        $finder->files()->in($directory)->name("#\.($regexFormats)$#");

        foreach ($finder as $file) {
            $this->filter($file->getRelativePathname(), $filterParam, $force);
        }
    }

    /**
     * filterSets
     *
     * @param mixed $force Description.
     *
     * @access public
     *
     * @return mixed Value.
     */
    public function filterSets($force = false){
        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        foreach ($filter_sets as $value) {
            $this->filterAll($value, $force);
        }
    }

    /**
     * removeFilterSets
     *
     * @access public
     *
     * @return mixed Value.
     */
    public function removeAll(){

        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        foreach ($filter_sets as $filter => $value) {
            $this->removeFilterAll($filter);
        }
    }

    /**
     * removeFilterAll
     *
     * @param mixed $filter Description.
     *
     * @access public
     *
     * @return mixed Value.
     */
    public function removeAllFilteredImage($filter){
        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        if (empty($filter_sets[$filter])) {
            throw new InvalidArgumentException("Invalid Filter Set $filterParam : Not Exist");
        }
        $directory = $filter_sets[$filter]["directory"];
        $dir_data = sprintf("%s/%",$directory, $filter);
           if(file_exists($dir_data)){
                unlink($dir_data);
           }
    }

    /**
     * removeFilterAll
     *
     * @param mixed $filter Description.
     *
     * @access public
     *
     * @return mixed Value.
     */
    public function removeFilteredImage($image, $filter){
        $filter_sets = $this->container->getParameter('gt_imagine.filter_sets');
        if (empty($filter_sets[$filter])) {
            throw new InvalidArgumentException("Invalid Filter Set $filterParam : Not Exist");
        }
        $directory = $filter_sets[$filter]["directory"];
        $dir_data = sprintf("%s/%/%s",$directory, $filter, $image);
           if(file_exists($dir_data)){
                unlink($dir_data);
           }
    }


}