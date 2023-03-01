<?php

/**
 * Contao Leaflet MetaModels integration.
 *
 * @package    contao-leaflet-metamodels
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Cliff Parnitzky <contao@cliff-parnitzky.de>
 * @copyright  2015-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later https://github.com/netzmacht/contao-leaflet-metamodels/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\MetaModels\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class NetzmachtContaoLeafletMetaModelsExtension
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
final class NetzmachtContaoLeafletMetaModelsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('listeners.xml');
        $loader->load('services.xml');

        $this->registerLayer($container);
    }

    /**
     * Register MetaModels layer in the container.
     *
     * @param ContainerBuilder $container Dependency container.
     *
     * @return void
     */
    private function registerLayer(ContainerBuilder $container): void
    {
        $layers               = $container->getParameter('netzmacht.contao_leaflet.layers');
        $layers['metamodels'] = [
            'children'   => false,
            'icon'       => 'bundles/netzmachtcontaoleafletmetamodels/img/layer.png',
            'metamodels' => true,
            'boundsMode' => [
                'extend' => true,
                'fit'    => true,
            ],
        ];

        $container->setParameter('netzmacht.contao_leaflet.layers', $layers);
    }
}
