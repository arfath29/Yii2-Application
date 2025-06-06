<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Gherkin\Filter;

use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioInterface;

/**
 * Filters scenarios by definition line number.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class LineFilter implements FilterInterface
{
    /**
     * @var int
     */
    protected $filterLine;

    /**
     * Initializes filter.
     *
     * @param int|numeric-string $filterLine Line of the scenario to filter on
     */
    public function __construct($filterLine)
    {
        $this->filterLine = (int) $filterLine;
    }

    /**
     * Checks if Feature matches specified filter.
     *
     * @param FeatureNode $feature Feature instance
     *
     * @return bool
     */
    public function isFeatureMatch(FeatureNode $feature)
    {
        return $this->filterLine === $feature->getLine();
    }

    /**
     * Checks if scenario or outline matches specified filter.
     *
     * @param ScenarioInterface $scenario Scenario or Outline node instance
     *
     * @return bool
     */
    public function isScenarioMatch(ScenarioInterface $scenario)
    {
        if ($this->filterLine === $scenario->getLine()) {
            return true;
        }

        if ($scenario instanceof OutlineNode && $scenario->hasExamples()) {
            return $this->filterLine === $scenario->getLine()
                || in_array($this->filterLine, $scenario->getExampleTable()->getLines());
        }

        return false;
    }

    /**
     * Filters feature according to the filter and returns new one.
     *
     * @return FeatureNode
     */
    public function filterFeature(FeatureNode $feature)
    {
        $scenarios = [];
        foreach ($feature->getScenarios() as $scenario) {
            if (!$this->isScenarioMatch($scenario)) {
                continue;
            }

            if ($scenario instanceof OutlineNode && $scenario->hasExamples()) {
                foreach ($scenario->getExampleTables() as $exampleTable) {
                    $table = $exampleTable->getTable();
                    $lines = array_keys($table);

                    if (in_array($this->filterLine, $lines)) {
                        $filteredTable = [$lines[0] => $table[$lines[0]]];

                        if ($lines[0] !== $this->filterLine) {
                            $filteredTable[$this->filterLine] = $table[$this->filterLine];
                        }

                        $scenario = $scenario->withTables(
                            [new ExampleTableNode($filteredTable, $exampleTable->getKeyword(), $exampleTable->getTags())],
                        );
                    }
                }
            }

            $scenarios[] = $scenario;
        }

        return $feature->withScenarios($scenarios);
    }
}
