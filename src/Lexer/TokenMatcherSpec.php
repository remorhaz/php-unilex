<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Remorhaz\UniLex\Exception;

use function array_keys;
use function array_pop;
use function asort;
use function explode;
use function implode;
use function in_array;

class TokenMatcherSpec
{
    private ?ReflectionClass $templateClass = null;

    /**
     * @var null|array<string>
     */
    private ?array $usedClassList = null;

    private ?string $targetNamespaceName = null;

    private ?string $targetShortName = null;

    /**
     * @var list<string>
     */
    private array $fileCommentList = [];

    private string $header = '';

    private string $beforeMatch = '';

    private string $onError = '';

    private string $onTransition = '';

    private string $onToken = '';

    /**
     * @var array<string, array<string, TokenSpec>>
     */
    private array $tokenSpecList = [];

    public function __construct(
        private string $targetClassName,
        private string $templateClassName,
    ) {
    }

    /**
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public function getTemplateClass(): ReflectionClass
    {
        return $this->templateClass ??= new ReflectionClass($this->templateClassName);
    }

    public function getTargetClassName(): string
    {
        return $this->targetClassName;
    }

    /**
     * @throws ReflectionException
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
     * @throws ReflectionException
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
            [$namespaceName] = $this->splitTargetClassName();
            $this->targetNamespaceName = $namespaceName;
        }

        return $this->targetNamespaceName;
    }

    public function getTargetShortName(): string
    {
        if (!isset($this->targetShortName)) {
            [, $shortName] = $this->splitTargetClassName();
            $this->targetShortName = $shortName;
        }

        return $this->targetShortName;
    }

    /**
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public function getMatchMethod(): ReflectionMethod
    {
        return $this->getTemplateClass()->getMethod('match');
    }

    public function setHeader(string $code): self
    {
        $this->header = $code;

        return $this;
    }

    public function getHeader(): string
    {
        return $this->header;
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
     * @throws Exception
     */
    public function addTokenSpec(string $context, TokenSpec ...$tokenSpecList): self
    {
        foreach ($tokenSpecList as $tokenSpec) {
            $regExp = $tokenSpec->getRegExp();
            $this->tokenSpecList[$context][$regExp] = isset($this->tokenSpecList[$context][$regExp])
                ? throw new Exception("Token spec for pattern $regExp is already set")
                : $tokenSpec;
        }

        return $this;
    }

    /**
     * @param string $mode
     * @return array<string, TokenSpec>
     */
    public function getTokenSpecList(string $mode): array
    {
        return $this->tokenSpecList[$mode] ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTokenSpec(string $mode, string $regExp): TokenSpec
    {
        foreach ($this->getTokenSpecList($mode) ?? [] as $tokenSpec) {
            if ($tokenSpec->getRegExp() == $regExp) {
                return $tokenSpec;
            }
        }

        throw new Exception("Token spec not found: {$regExp}");
    }

    /**
     * @return list<string>
     */
    public function getModeList(): array
    {
        return array_keys($this->tokenSpecList);
    }

    /**
     * @return array{string, string}
     */
    private function splitTargetClassName(): array
    {
        $nameSpaceSeparator = '\\';
        $classNameParts = explode($nameSpaceSeparator, $this->getTargetClassName());
        $className = array_pop($classNameParts);
        $namespaceName = implode($nameSpaceSeparator, $classNameParts);

        return [$namespaceName, $className];
    }

    /**
     * @throws ReflectionException
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
