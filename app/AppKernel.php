<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new App\UserBundle\AppUserBundle(),
            new App\ArticleBundle\AppArticleBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new App\FrontPageBundle\AppFrontPageBundle(),
            new App\BackOfficeBundle\AppBackOfficeBundle(),
            new App\AdBundle\AppAdBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new App\AppBundle\AppAppBundle(),
            new App\SettingsBundle\AppSettingsBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new App\OfferBundle\AppOfferBundle(),
            new App\PaymentBundle\AppPaymentBundle(),
            new App\MenuBundle\AppMenuBundle(),
            new App\OfficeBundle\AppOfficeBundle(),
            new App\ImportBundle\AppImportBundle(),
            new TFox\MpdfPortBundle\TFoxMpdfPortBundle(),
            new App\NewsletterBundle\AppNewsletterBundle(),
            new App\SubscriptionBundle\AppSubscriptionBundle(),
            new App\SearchLinkBundle\AppSearchLinkBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new App\ArticleLinkBundle\AppArticleLinkBundle(),
            new App\SitemapBundle\AppSitemapBundle(),
            new App\ArchiveBundle\AppArchiveBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
    protected function initializeContainer()
    {
        parent::initializeContainer();
        if (PHP_SAPI == 'cli') {
            $request = new Request();
            $request->create('/');
            $this->getContainer()->enterScope('request');
            $this->getContainer()->set('request', $request, 'request');
        }
    }
}
