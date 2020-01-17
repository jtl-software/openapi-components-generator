# OpenAPI Components Generator

This is a straight forward library for generating classes from component schemas inside an OpenAPI schema

### Supported 
- OpenAPI versions: 3.x
- Languages: PHP (>=7.2.x)
- API formats: JSON (Yaml Support will come soon)
 
### Usage
```php
<?php

namespace My\Space;

use Jtl\OpenApiComponentsGenerator\SchemaParser;
use Jtl\OpenApiComponentsGenerator\PhpGenerator;

$parser = new SchemaParser();

//You can add regular expressions if you want to generate only specific components
$parser->addFilterPattern('/foo|bar|yeeha$/');

//Parse the components schemas
$schema = $parser->read('https://path.to/schema/openapi3.json', 'My\\Fancy\\Model\\Namespace');

$generator = new PhpGenerator();
$destination = '/path/to/model/directory';

//Generate component models in $destination
$generator->generateEntities($schema, $destination);  
```