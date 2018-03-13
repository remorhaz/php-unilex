<?php

namespace Remorhaz\UniLex\Parser\SyntaxTree\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\LL1\SDD\ProductionContextInterface;

class ProductionRuleContext extends TreeRuleContext implements ProductionContextInterface
{

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getAttribute(string $name)
    {
        return $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($name);
    }

    /**
     * @param string $attr
     * @param $value
     * @return ProductionRuleContext
     * @throws Exception
     */
    public function setAttribute(string $attr, $value): self
    {
        $this
            ->getProduction()
            ->getHeader()
            ->setAttribute($attr, $value);
        return $this;
    }
}
