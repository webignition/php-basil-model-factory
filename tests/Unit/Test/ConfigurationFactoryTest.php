<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Test;

use webignition\BasilModel\Test\Configuration;
use webignition\BasilModel\Test\ConfigurationInterface;
use webignition\BasilDataStructure\Test\Configuration as ConfigurationData;
use webignition\BasilModelFactory\Test\ConfigurationFactory;

class ConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationFactory = ConfigurationFactory::createFactory();
    }

    /**
     * @dataProvider createFromConfigurationDataDataProvider
     */
    public function testCreateFromConfigurationData(
        ConfigurationData $configurationData,
        ConfigurationInterface $expectedConfiguration
    ) {
        $configuration = $this->configurationFactory->createFromConfigurationData($configurationData);

        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function createFromConfigurationDataDataProvider(): array
    {
        return [
            'empty' => [
                'configurationData' => new ConfigurationData([]),
                'expectedConfiguration' => new Configuration('', ''),
            ],
            'non-string values' => [
                'configurationData' => new ConfigurationData([
                    ConfigurationData::KEY_BROWSER => true,
                    ConfigurationData::KEY_URL => 3
                ]),
                'expectedConfiguration' => new Configuration('1', '3'),
            ],
            'string values' => [
                'configurationData' => new ConfigurationData([
                    ConfigurationData::KEY_BROWSER => 'chrome',
                    ConfigurationData::KEY_URL => 'http://example.com',
                ]),
                'expectedConfiguration' => new Configuration('chrome', 'http://example.com'),
            ],
            'page url reference' => [
                'configurationData' => new ConfigurationData([
                    ConfigurationData::KEY_BROWSER => 'chrome',
                    ConfigurationData::KEY_URL => 'page_import_name.url',
                ]),
                'expectedConfiguration' => new Configuration('chrome', 'page_import_name.url'),
            ],
        ];
    }
}
