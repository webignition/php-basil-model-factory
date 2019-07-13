<?php

namespace webignition\BasilModelFactory\Test;

use webignition\BasilModel\Test\Configuration;
use webignition\BasilModel\Test\ConfigurationInterface;
use webignition\BasilDataStructure\Test\Configuration as ConfigurationData;

class ConfigurationFactory
{
    public static function createFactory(): ConfigurationFactory
    {
        return new ConfigurationFactory();
    }

    /**
     * @param ConfigurationData $configurationData
     *
     * @return ConfigurationInterface
     */
    public function createFromConfigurationData(ConfigurationData $configurationData): ConfigurationInterface
    {
        return new Configuration($configurationData->getBrowser(), $configurationData->getUrl());
    }
}
