<?php

namespace Remorhaz\UniLex\Lexer;

use ReflectionClass;
use ReflectionMethod;
use Remorhaz\UniLex\Exception;

class TokenMatcherSpec
{

    private $targetClassName;

    private $templateClassName;

    private $templateClass;

    private $usedClassList;

    private $targetNamespaceName;

    private $targetShortName;

    private $fileCommentList = [];

    private $beforeMatch = '';

    private $onError = '';

    private $onTransition = '';

    private $onToken = '';

    private $tokenSpecList = [];

    public function __construct(string $targetClassName, string $templateClassName)
    {
        $this->targetClassName = $targetClassName;
        $this->templateClassName = $templateClassName;
    }

    /**
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    public function getTemplateClass(): ReflectionClass
    {
        if (!isset($this->templateClass)) {
            $this->templateClass = new ReflectionClass($this->templateClassName);
        }
        return $this->templateClass;
    }

    public function getTargetClassName(): string
    {
        return $this->targetClassName;
    }

    /**
     * @param string $name
     * @param string|null $alias
     * @return TokenMatcherSpec
     * @throws \ReflectionException
     */
    public function addUsedClass(string $name, string $alias = null): self
    {
        $this->initUsedClassList();
        if (in_array($name, $this->usedClassList)) {
            return $this;
        }
        $class = new ReflectionClass($name);
        if ($this->getTargetNamespaceName() == $class->getNamespaceName()) {
            return $this;
        }
        if (isset($alias)) {
            $this->usedClassList[$alias] = $name;
            return $this;
        }
        $this->usedClassList[] = $name;
        return $this;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getUsedClassList(): array
    {
        $this->initUsedClassList();
        $usedClassList = $this->usedClassList;
        asort($usedClassList);
        return $usedClassList;
    }

    public function addFileComment(string ...$textLineList): self
    {
        foreach ($textLineList as $textLine) {
            $this->fileCommentList[] = $textLine;
        }
        return $this;
    }

    public function getFileComment(): string
    {
        return implode("\n", $this->fileCommentList);
    }

    public function getTargetNamespaceName(): string
    {
        if (!isset($this->targetNamespaceName)) {
            [0 => $namespaceName] = $this->splitTargetClassName();
            $this->targetNamespaceName =$namespaceName;
        }
        return $this->targetNamespaceName;
    }

    public function getTargetShortName(): string
    {
        if (!isset($this->targetShortName)) {
            [1 => $shortName] = $this->splitTargetClassName();
            $this->targetShortName = $shortName;
        }
        return $this->targetShortName;
    }

    /**
     * @return ReflectionMethod
     * @throws \ReflectionException
     */
    public function getMatchMethod(): ReflectionMethod
    {
        return $this->getTemplateClass()->getMethod('match');
    }

    public function setBeforeMatch(string $code): self
    {
        $this->beforeMatch = $code;
        return $this;
    }

    public function getBeforeMatch(): string
    {
        return $this->beforeMatch;
    }

    public function setOnError(string $code): self
    {
        $this->onError = $code;
        return $this;
    }

    public function getOnError(): string
    {
        return $this->onError;
    }

    public function setOnTransition(string $code): self
    {
        $this->onTransition = $code;
        return $this;
    }

    public function getOnTransition(): string
    {
        return $this->onTransition;
    }

    public function setOnToken(string $code): self
    {
        $this->onToken = $code;
        return $this;
    }

    public function getOnToken(): string
    {
        return $this->onToken;
    }

    /**
     * @param TokenSpec ...$tokenSpecList
     * @return TokenMatcherSpec
     * @throws Exception
     */
    public function addTokenSpec(TokenSpec ...$tokenSpecList): self
    {
        foreach ($tokenSpecList as $tokenSpec) {
            if (isset($this->tokenSpecList[$tokenSpec->getRegExp()])) {
                throw new Exception("Token spec for pattern {$tokenSpec->getRegExp()} is already set");
            }
            $this->tokenSpecList[$tokenSpec->getRegExp()] = $tokenSpec;
        }
        return $this;
    }

    /**
     * @return TokenSpec[]
     */
    public function getTokenSpecList(): array
    {
        return $this->tokenSpecList;
    }

    private function splitTargetClassName(): array
    {
        $nameSpaceSeparator = '\\';
        $classNameParts = explode($nameSpaceSeparator, $this->getTargetClassName());
        $className = array_pop($classNameParts);
        $namespaceName = implode($nameSpaceSeparator, $classNameParts);
        return [$namespaceName, $className];
    }

    /**
     * @throws \ReflectionException
     */
    private function initUsedClassList(): void
    {
        if (isset($this->usedClassList)) {
            return;
        }
        $this->usedClassList = [];
        foreach ($this->getMatchMethod()->getParameters() as $parameter) {
            if ($parameter->hasType() && !$parameter->getType()->isBuiltin()) {
                $this->addUsedClass($parameter->getType()->getName());
            }
        }
        $this->addUsedClass($this->getTemplateClass()->getName());
    }
}
