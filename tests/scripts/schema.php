<?php
ini_set('display_errors', 'on');

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$parser = new \Jtl\OpenApiComponentGenerator\SchemaParser();
//$url = 'http://sw6.dev.lan/api/v1/_info/openapi3.json';
$url = sprintf('%s/openapi3.json', __DIR__);

$parser->addRegexPattern('/\_flat$/');

$schema = $parser->read($url);

print_r($schema);