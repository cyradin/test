<?php
require 'vendor/autoload.php';

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\UglifyJs2Filter;

$cliOptions = getopt('', [
    'build',
]);

foreach ($cliOptions as $key => $value) {
    switch ($key) {
        case 'build':
            $am = new AssetManager();

            // Minifying js
            $js = new AssetCollection([
                new FileAsset(
                    __DIR__ . '/assets/js/script.js',
                    [new UglifyJs2Filter()]
                ),
            ]);

            $js->setTargetPath('js/script.js');

            $am->set('js', $js);

            // Compiling less to css and minifying it
            $css = new AssetCollection([
                new FileAsset(
                    __DIR__ . '/assets/css/style.less',
                    [new LessFilter('/usr/bin/node', [__DIR__ . '/node_modules/']), new UglifyCssFilter()]
                ),
            ]);
            $css->setTargetPath('css/style.css');

            $am->set('css', $css);

            $writer = new AssetWriter(__DIR__ . '/public');
            $writer->writeManagerAssets($am);

            break;
    }
}
