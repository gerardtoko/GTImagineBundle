<?php

/*
 * This file is part of GT.
 *
 * (c) Gerard TOKO <gerard.toko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GT\ImagineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Description of ImageDumpCommand
 *
 * @author gerardtoko
 */
class ImageDumpCommand extends ContainerAwareCommand
{

    /**
     *
     * @param type $name
     */
    public function __construct($name = null)
    {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->setName('gt:imagine:dump:image')
                ->setDescription('Apply a filter on an image')
                ->addArgument('filter', null, 'Define filter name.')
                ->addArgument('image', null, 'Define image name.')
        ;
    }

    /**
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = time();
        $output->writeln("<info>Start GT Imagine</info>");
        $filterArgument = $input->getArgument("filter", NULL);
        $imageArgument = $input->getArgument("image", NULL);

        if ($filterArgument == NULL) {
            throw new \Exception("Filter is undefined");
        }

        if ($imageArgument == NULL) {
            throw new \Exception("Image is undefined");
        }

        //variable
        $container = $this->getContainer();

        $imagine = $container->get('gt_imagine');
        $imagine->filter($imageArgument, $filterArgument, true);

        $finalTime = (int) time() - $time;
        $output->writeln("<info>finish!</info>\n");
        $output->writeln("Time: $finalTime s\n");
    }

}