<?php

namespace webignition\BasilModelFactory;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\DataSet\DataSetCollection;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Step\StepInterface;
use webignition\BasilDataStructure\Step as StepData;
use webignition\BasilModelFactory\Action\ActionFactory;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;

class StepFactory
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var AssertionFactory
     */
    private $assertionFactory;

    /**
     * @var IdentifierFactory
     */
    private $identifierFactory;

    public function __construct(
        ActionFactory $actionFactory,
        AssertionFactory $assertionFactory,
        IdentifierFactory $identifierFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->assertionFactory = $assertionFactory;
        $this->identifierFactory = $identifierFactory;
    }

    public static function createFactory(): StepFactory
    {
        return new StepFactory(
            ActionFactory::createFactory(),
            AssertionFactory::createFactory(),
            IdentifierFactory::createFactory()
        );
    }

    /**
     * @param StepData $stepData
     *
     * @return StepInterface
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createFromStepData(StepData $stepData): StepInterface
    {
        $actionStrings = $stepData->getActions();
        $assertionStrings = $stepData->getAssertions();

        $actions = [];
        $assertions = [];

        $actionString = '';
        $assertionString = '';

        try {
            foreach ($actionStrings as $actionString) {
                if ('string' === gettype($actionString)) {
                    $actionString = trim($actionString);

                    if ('' !== $actionString) {
                        $actions[] = $this->actionFactory->createFromActionString($actionString);
                    }
                }
            }

            foreach ($assertionStrings as $assertionString) {
                if ('string' === gettype($assertionString)) {
                    $assertionString = trim($assertionString);

                    if ('' !== $assertionString) {
                        $assertions[] = $this->assertionFactory->createFromAssertionString($assertionString);
                    }
                }
            }
        } catch (MalformedPageElementReferenceException $contextAwareException) {
            $contextAwareException->applyExceptionContext([
                ExceptionContextInterface::KEY_CONTENT => $assertionString !== '' ? $assertionString : $actionString,
            ]);

            throw $contextAwareException;
        }

        $step = new Step($actions, $assertions);

        if ($stepData->getImportName() || $stepData->getDataImportName()) {
            $step = new PendingImportResolutionStep(
                $step,
                $stepData->getImportName(),
                $stepData->getDataImportName()
            );
        }

        $dataArray = $stepData->getDataArray();
        if (!empty($dataArray)) {
            $step = $step->withDataSetCollection(DataSetCollection::fromArray($dataArray));
        }

        $elementIdentifiers = [];

        foreach ($stepData->getElements() as $elementName => $elementIdentifierString) {
            $elementIdentifier = $this->identifierFactory->create($elementIdentifierString, $elementName);

            if ($elementIdentifier instanceof IdentifierInterface) {
                $elementIdentifiers[] = $elementIdentifier;
            }
        }

        if (!empty($elementIdentifiers)) {
            $step = $step->withIdentifierCollection(new IdentifierCollection($elementIdentifiers));
        }

        return $step;
    }
}
