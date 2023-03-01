<?php

/**
 * Contao Leaflet MetaModels integration.
 *
 * @package    contao-leaflet-metamodels
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later https://github.com/netzmacht/contao-leaflet-metamodels/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\MetaModels\Renderer;

use MetaModels\IItem as Item;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Value\GeoJson\StaticFeature;

/**
 * Class GeoJsonRenderer only support lazy loading of geojson data.
 *
 * The geojson data can be stored in a file or in an attributes value.
 *
 * @package Netzmacht\Contao\Leaflet\MetaModels\Renderer
 */
class GeoJsonRenderer extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    public function loadData(
        Item $item,
        FeatureCollection $featureCollection,
        DefinitionMapper $mapper,
        $parentId,
        Request $request = null,
        $deferred = false
    ): void {
        if (!$this->model->deferred != $deferred) {
            return;
        }

        $attribute = $this->getAttribute('geojsonAttribute', $item);

        if ($attribute && $attribute->get('type') === 'file') {
            $this->loadFeaturesFromFile(
                $item,
                $attribute->getColName(),
                function ($path) use ($featureCollection) {
                    $path = TL_ROOT . '/' . $path;

                    if (file_exists($path)) {
                        $content = file_get_contents($path);
                        $featureCollection->addFeature(new StaticFeature($content));
                    }
                }
            );
        } else {
            $featureCollection->addFeature(new StaticFeature($item->get($attribute->getColName())));
        }
    }

    /**
     * Load features from file attribute.
     *
     * @param Item      $item      The metamodel.
     * @param string    $attribute The attribute name.
     * @param \callable $callback  The callback being called for each file.
     *
     * @return void
     */
    private function loadFeaturesFromFile(Item $item, $attribute, $callback): void
    {
        $value = $item->parseAttribute($attribute);

        if (empty($value['raw']['path'])) {
            return;
        }

        foreach ($value['raw']['path'] as $key => $path) {
            $callback($path, $key);
        }
    }
}
