<?php

namespace GT\ImagineBundle\Imagine\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Imagine\Exception\InvalidArgumentException;
use GT\ImagineBundle\Imagine\Filter\Loader\ResizeFilterLoader;
use GT\ImagineBundle\Imagine\Filter\Loader\CropFilterLoader;
use GT\ImagineBundle\Imagine\Filter\Loader\ThumbnailFilterLoader;
use GT\ImagineBundle\Imagine\Filter\Loader\RelativeResizeFilterLoader;


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of ResizeFilter
 *
 * @author gerardtoko
 */
class FilterManager
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
     * @param type $filter_sets
     * @param type $filterParam
     * @param type $imageParam
     * @param type $driver
     * @param type $data_root
     * @param type $mkdir_mode
     * @return type
     */
    public function filter($filter_sets, $filterParam, $imageParam, $driver, $data_root, $mkdir_mode, $web_root, $force = false)
    {

        $filter_set = $filter_sets[$filterParam];
        $directory = $filter_set["directory"];
        $quality = $filter_set["quality"];
        $file = sprintf("/%s/%s", trim($directory, "/"), trim($imageParam, "/"));
        $imagine = $this->getImagine($driver);
        if (file_exists($file)) {

            $file_filter = sprintf("/%s/%s/%s", trim($data_root, "/"), trim($filterParam, "/"), trim($imageParam, "/"));
            if ($force == TRUE || !file_exists($file_filter)) {

                $image = $imagine->open($file);
                $image_filter = $this->apply($image, $filter_set["filters"]);

                $data_filter = sprintf("/%s/%s", trim($data_root, "/"), trim($filterParam, "/"));
                if (!file_exists($data_filter)) {
                    mkdir($data_filter, $mkdir_mode);
                }
                $image_filter->save($file_filter, array('quality' => $quality));
            }
        } else {
            $file_filter = sprintf("/%s/%s/default.jpg", trim($data_root, "/"), trim($filterParam, "/"), trim($imageParam, "/"));
        }

        $explode = explode(trim($web_root), trim($file_filter));
        foreach ($explode as $value_explode) {
            if ($value_explode != "") {
                return $value_explode;
            }
        }
    }

    /**
     *
     * @param type $driver
     * @return type
     */
    protected function getImagine($driver)
    {
        switch ($driver) {
            case "gd":
                $imagine = new \Imagine\Gd\Imagine();
                break;
            case "imagick":
                $imagine = new \Imagine\Imagick\Imagine();
                break;
            case "gmagick":
                $imagine = new \Imagine\Gmagick\Imagine();
                break;
        }
        return $imagine;
    }

    /**
     *
     * @param type $image
     * @param type $filters
     * @return type
     * @throws InvalidArgumentException
     */
    protected function apply($image, $filters)
    {
        foreach ($filters as $key_filter => $options) {
            switch (strtolower($key_filter)) {
                case "resize":
                    $loader = new ResizeFilterLoader();
                    $image = $loader->load($image, $options);
                    break;

                case "crop":
                    $loader = new CropFilterLoader();
                    $image = $loader->load($image, $options);
                    break;

                case "thumbnail":
                    $loader = new ThumbnailFilterLoader();
                    $image = $loader->load($image, $options);
                    break;

                case "relative_resize":
                    $loader = new RelativeResizeFilterLoader();
                    $image = $loader->load($image, $options);
                    break;

                default:
                    throw new InvalidArgumentException("Invalid Filter $key_filter : Not Exist");
                    break;
            }
        }
        return $image;
    }

    /**
     *
     * @param type $filterParam
     * @param type $imageParam
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function valid($filterParam, $imageParam, $web_root, $data_root, $filter_sets, $formats, $mkdir_mode)
    {
        if (!is_int($mkdir_mode)) {
            throw new InvalidArgumentException("Invalid mkdir_mode: $mkdir_mode");
        }

        if (!file_exists($web_root)) {
            throw new InvalidArgumentException("Invalid Web root: $web_root");
        }

        if (!file_exists($data_root)) {
            throw new InvalidArgumentException("Invalid Data root: $data_root");
        }

        if (empty($filter_sets[$filterParam])) {
            throw new InvalidArgumentException("Invalid Filter Set $filterParam : Not Exist");
        }


        $regexFormats = implode("|", $formats);
        if (!preg_match("#\.($regexFormats)#", $imageParam)) {
            throw new InvalidArgumentException("Invalid Formart Image $imageParam (Required:$regexFormats)");
        }

        return TRUE;
    }

}
