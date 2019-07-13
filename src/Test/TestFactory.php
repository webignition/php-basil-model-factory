<?php

namespace webignition\BasilModelFactory\Test;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\Test\TestInterface;
use webignition\BasilModel\Test\Test;
use webignition\BasilDataStructure\Step;
use webignition\BasilDataStructure\Test\Test as TestData;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\StepFactory;

class TestFactory
{
    private $configurationFactory;
    private $stepFactory;

    public function __construct(ConfigurationFactory $configurationFactory, StepFactory $stepFactory)
    {
        $this->configurationFactory = $configurationFactory;
        $this->stepFactory = $stepFactory;
    }

    public static function createFactory(
        ?ConfigurationFactory $configurationFactory = null,
        ?StepFactory $stepFactory = null
    ): TestFactory {
        return new TestFactory(
            $configurationFactory ?? ConfigurationFactory::createFactory(),
            $stepFactory ?? StepFactory::createFactory()
        );
    }

    /**
     * @param string $name
     * @param TestData $testData
     *
     * @return TestInterface
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createFromTestData(string $name, TestData $testData)
    {
        $configuration = $this->configurationFactory->createFromConfigurationData($testData->getConfiguration());
        $steps = [];

        /* @var Step $stepData */
        foreach ($testData->getSteps() as $stepName => $stepData) {
            try {
                $steps[$stepName] = $this->stepFactory->createFromStepData($stepData);
            } catch (MalformedPageElementReferenceException $contextAwareException) {
                $contextAwareException->applyExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => $name,
                    ExceptionContextInterface::KEY_STEP_NAME => $stepName,
                ]);

                throw $contextAwareException;
            }
        }

        return new Test($name, $configuration, $steps);
    }
}
