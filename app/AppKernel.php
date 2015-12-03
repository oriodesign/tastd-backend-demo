<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    /**
     * @return array|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),

            // Sensio
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Doctrine
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            // Jcroll
            new Jcroll\FoursquareApiBundle\JcrollFoursquareApiBundle(),

            // Jms
            new JMS\SerializerBundle\JMSSerializerBundle(),

            // Fos
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),

            // Nelmio
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),

            // Wrep
            new Wrep\Bundle\NotificatoBundle\NotificatoBundle(),

            // Knp
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // Rms
            new RMS\PushNotificationsBundle\RMSPushNotificationsBundle(),

            // Sonata
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),

            // Apperclass
            new Apperclass\Bundle\FixtureBundle\ApperclassFixtureBundle(),


            // Bazinga
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),

            // Tastd
            new Tastd\Bundle\CoreBundle\TastdCoreBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Apperclass\TestDocBundle\ApperclassTestDocBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
