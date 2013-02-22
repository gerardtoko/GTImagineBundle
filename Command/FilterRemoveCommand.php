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
 * Description of FilterDumpCommand
 *
 * @author gerardtoko
 */
class FilterRemoveCommand extends ContainerAwareCommand
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
        $this->setName('gt:imagine:remove:filter')
                ->setDescription('remove a images group filtered')
                ->addArgument('filter', null, 'Define filter name.')
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

        if ($filterArgument == NULL) {
            throw new \Exception("Filter is undefined");
        }

        //variable
        $container = $this->getContainer();
        $imagine = $container->get('gt_imagine');
        $imagine->removeAllFilteredImage($filterArgument);

        $finalTime = (int) time() - $time;
        $output->writeln("<info>finish!</info>\n");
        $output->writeln("Time: $finalTime s\n");
    }

}