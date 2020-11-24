<?php

namespace App\Command;

use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StuurFactuurCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:factuur:versturen');
        $this->setDescription('Verstuur facturen per mail');
        $this->addArgument('date');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger();
        $logger->addOutput($output);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mailer = $this->getContainer()->get('mailer');

        $date = $input->getArgument('date');
        if ($date === null || $date === '') {
            $date = date('Y-m-d');
        }
        $output->writeln($date);

        /* @var $repo \App\Entity\KoopmanRepository */
        $repoKoopman = $this->getContainer()->get('appapi.repository.koopman');
        /* @var $repo \App\Entity\DagverguningRepository */
        $repoDagverguning = $this->getContainer()->get('appapi.repository.dagvergunning');

        /** @var $factuurPdfService PdfFactuurService */
        $factuurPdfService = $this->getContainer()->get('pdf_factuur');

        $koopmannen = $repoKoopman->getWithDagvergunningOnDag(new \DateTime($date));

        foreach ($koopmannen as $koopman) {
            try {
                /** @var Koopman $koopman */
                $output->writeln('Found koopman with id: ' . $koopman->getId() . ' ' . $koopman->getErkenningsnummer());

                if (empty($koopman->getEmail())) {
                    $output->writeln('.. Skip (no e-mail)');
                    continue;
                }

                $dagvergunningen = $repoDagverguning->search(array(
                    'dag' => $date,
                    'koopmanId' => $koopman->getId(),
                    'doorgehaald' => 0,
                ));
                $pdf = $factuurPdfService->generate($koopman, $dagvergunningen);
                $pdfFile = $pdf->Output('koopman-' . $koopman->getId() . '.pdf', 'S');

                $message = (new \Swift_Message())
                    ->setSubject('Factuur Marktbureau Gemeente Amsterdam')
                    ->setFrom(['marktbureau@amsterdam.nl' => 'Marktbureau Gemeente Amsterdam'])
                    ->setTo([$koopman->getEmail()])
                    ->setBody('Bijgesloten ontvangt u een factuur van het Marktbureau van de Gemeente Amsterdam als PDF-bestand. Deze factuur is voor uw eigen administratie en bevat tevens een btw specificatie.

De factuur heeft u reeds betaald of moet u nog betalen op de markt per pin bij de toezichthouder. De factuur is geen betalingsbewijs.')
                    ->attach((new \Swift_Attachment())->setFilename('factuur.pdf')->setContentType('application/pdf')->setBody($pdfFile))
                ;

                $mailer->send($message);
                $output->writeln('.. Mail queued for ' . $koopman->getEmail());
            } catch (\Exception $e) {
                $output->writeln('.. Failure ' . get_class($e) . ' ::: ' . $e->getMessage());
            }
        }

    }
}
