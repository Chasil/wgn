<?php

namespace App\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestRedirectCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:redirect:test')
            ->setDescription('test redirects');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isHeader = true;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $path = __DIR__ . '/urls.csv';
        $handleOutput = fopen(__DIR__ . '/output.csv', "w");

        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if($isHeader)
                {
                    $isHeader = false;
                    continue;
                }
                $url = $data[0];
                curl_setopt($ch, CURLOPT_URL, $url);
                $out = curl_exec($ch);

                // line endings is the wonkiest piece of this whole thing
                $out = str_replace("\r", "", $out);

                // only look at the headers
                $headers_end = strpos($out, "\n\n");
                if( $headers_end !== false ) {
                    $out = substr($out, 0, $headers_end);
                }

                $headers = explode("\n", $out);
                foreach($headers as $header) {
                    if( substr($header, 0, 10) == "Location: " ) {
                        $target = substr($header, 10);

                        $output->writeln("[$url] redirects to [$target]") ;
                        fputcsv($handleOutput, [$url,$target]);
                        continue 2;
                    }
                }
                $output->writeln("[$url] does not redirect") ;
                fputcsv($handleOutput, [$url,'brak przekierowania']);
            }
            fclose($handle);
            fclose($handleOutput);
        }
    }
}
