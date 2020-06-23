<?php

namespace App\Command;

use App\Entity\Deck;
use App\Entity\Decklist;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class DeleteDecklistCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('app:decklist:delete')
        ->setDescription('Delete one decklist')
        ->addArgument(
            'decklist_id',
            InputArgument::REQUIRED,
            'Id of the decklist'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $decklist_id = $input->getArgument('decklist_id');
        $decklist = $em->getRepository(Decklist::class)->find($decklist_id);

        $successors = $em->getRepository(Decklist::class)->findBy(array(
                'precedent' => $decklist
        ));
        foreach ($successors as $successor) {
            /* @var $successor Decklist */
            $successor->setPrecedent(null);
        }

        $children = $em->getRepository(Deck::class)->findBy(array(
                'parent' => $decklist
        ));
        foreach ($children as $child) {
            /* @var $child Deck */
            $child->setParent(null);
        }

        $em->flush();
        $em->remove($decklist);
        $em->flush();

        $output->writeln("Decklist deleted");
    }
}