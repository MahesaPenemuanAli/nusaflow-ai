<?php
require 'vendor/autoload.php';
foreach ((new ReflectionClass('Filament\Resources\Resource'))->getMethods() as $m) {
    if (in_array($m->getName(), ['form', 'table'])) {
        echo $m->getName() . '(' . $m->getParameters()[0]->getType() . '): ' . $m->getReturnType() . PHP_EOL;
    }
}
