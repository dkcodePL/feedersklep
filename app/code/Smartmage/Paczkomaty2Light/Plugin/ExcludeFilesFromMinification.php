<?php

namespace Smartmage\Paczkomaty2Light\Plugin;
use Magento\Framework\View\Asset\Minification;

class ExcludeFilesFromMinification
{
    public function afterGetExcludes(Minification $subject, array $result, $contentType)
    {
        if ($contentType == 'js') {
            $result[] = 'https://geowidget.easypack24.net/js/sdk-for-javascript.js';
            $result[] = 'leaflet.js';
            $result[] = 'Leaflet.fullscreen.min.js';
            $result[] = 'L.Control.Locate.min.js';
            $result[] = 'leaflet.markercluster.js';
        }
        return $result;
    }
}