# OpenAPI Components Generator

This is a straight forward library for generating component classes, directly from an OpenApi schema.

### Supported 
- OpenAPI versions: 3.x
- Languages: PHP (>=7.2.x)
- API formats: JSON (Yaml Support will come soon)
 
### Usage
```php
$parser = new \Jtl\OpenApiComponentGenerator\SchemaParser();

//You can add regular expressions if you want to generate only specific components
$parser->addFilterPattern('/foo|bar|yeeha$/');

$schema = $parser->read('https://path.to/schema/openapi.json', 'My\\Fancy\\Model\\Namespace');
$destination = '/path/to/model/directory';
PhpGenerator::writeClasses($schema, $destination); //Generate component models in $destination 
```