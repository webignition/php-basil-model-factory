<?php

namespace webignition\BasilModelFactory;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\DataSet\DataSetCollection;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Step\StepInterface;
use webignition\BasilDataStructure\Step as StepData;
use webignition\BasilModelFactory\Action\ActionFactory;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\Identifier\PageElementReferenceIdentifierFactory;

class StepFactory
{
    private $actionFactory;
    private $assertionFactory;
    private $pageElementReferenceIdentifierFactory;

    public function __construct(
        ActionFactory $actionFactory,
        AssertionFactory $assertionFactory,
        PageElementReferenceIdentifierFactory $pageElementReferenceIdentifierFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->assertionFactory = $assertionFactory;
        $this->pageElementReferenceIdentifierFactory = $pageElementReferenceIdentifierFactory;
    }

    public static function createFactory(): StepFactory
    {
        return new StepFactory(
            ActionFactory::createFactory(),
            AssertionFactory::createFactory(),
            PageElementReferenceIdentifierFactory::createFactory()
        );
    }

    /**
     * @param StepData $stepData
     *
     * @return StepInterface
     *
     * @throws EmptyAssertionStringException
     * @throws InvalidActionTypeException
     * @throws InvalidIdentifierStringException
     * @throws MalformedPageElementReferenceException
     * @throws MissingValueException
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
                $actions[] = $this->actionFactory->createFromActionString(trim($actionString));
            }

            foreach ($assertionStrings as $assertionString) {
                $assertions[] = $this->assertionFactory->createFromAssertionString(trim($assertionString));
            }
        } catch (InvalidActionTypeException |
            InvalidIdentifierStringException |
            MissingValueException $contextAwareException
        ) {
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
            $elementIdentifier = $this->pageElementReferenceIdentifierFactory->create($elementIdentifierString);

            if ($elementIdentifier instanceof IdentifierInterface) {
                $elementIdentifier = $elementIdentifier->withName($elementName);
                $elementIdentifiers[] = $elementIdentifier;
            } else {
                throw new MalformedPageElementReferenceException(
                    new PageElementReference($elementIdentifierString)
                );
            }
        }

        if (!empty($elementIdentifiers)) {
            $step = $step->withIdentifierCollection(new IdentifierCollection($elementIdentifiers));
        }

        return $step;
    }
}
