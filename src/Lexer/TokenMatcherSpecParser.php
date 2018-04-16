<?php

namespace Remorhaz\UniLex\Lexer;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\TokenMatcherTemplate;

class TokenMatcherSpecParser
{

    private const TAG_LEX_TARGET_CLASS = 'lexTargetClass';
    private const TAG_LEX_TEMPLATE_CLASS = 'lexTemplateClass';
    private const TAG_LEX_HEADER = 'lexHeader';
    private const TAG_LEX_BEFORE_MATCH = 'lexBeforeMatch';
    private const TAG_LEX_ON_TRANSITION = 'lexOnTransition';
    private const TAG_LEX_ON_ERROR = 'lexOnError';
    private const LEX_NAMESPACE = 'namespace';
    private const LEX_USE = 'use';

    private $source;

    private $matcherSpec;

    private $targetClassName;

    private $templateClassName;

    private $docBlockFactory;

    private $codeBlockList = [];

    private $codeBlockKey;

    private $codeBlockStack = [];

    private $skipToken = false;

    private $usedClassList = [];

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param string $fileName
     * @return TokenMatcherSpecParser
     * @throws Exception
     */
    public static function loadFromFile(string $fileName): self
    {
        $source = @file_get_contents($fileName);
        if (false === $source) {
            throw new Exception("Failed to read lexer specification from file {$fileName}");
        }
        return new self($source);
    }

    /**
     * @return TokenMatcherSpec
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getMatcherSpec(): TokenMatcherSpec
    {
        if (!isset($this->matcherSpec)) {
            $this->matcherSpec = $this->buildMatcherSpec();
        }
        return $this->matcherSpec;
    }

    /**
     * @return TokenMatcherSpec
     * @throws Exception
     * @throws \ReflectionException
     */
    private function buildMatcherSpec(): TokenMatcherSpec
    {
        $phpTokenList = token_get_all($this->source);
        foreach ($phpTokenList as $phpToken) {
            $argList = is_array($phpToken) ? $phpToken : [null, $phpToken];
            $this->processPhpToken(...$argList);
        }
        if (!isset($this->targetClassName)) {
            throw new Exception("Invalid lexer specification: target class is not defined");
        }
        $targetClassName = $this->getCodeBlock(self::LEX_NAMESPACE) . $this->targetClassName;
        $templateClassName = $this->templateClassName ?? TokenMatcherTemplate::class;
        $matcherSpec = new TokenMatcherSpec($targetClassName, $templateClassName);
        $matcherSpec
            ->setHeader($this->getCodeBlock(self::TAG_LEX_HEADER))
            ->setBeforeMatch($this->getCodeBlock(self::TAG_LEX_BEFORE_MATCH))
            ->setOnTransition($this->getCodeBlock(self::TAG_LEX_ON_TRANSITION))
            ->setOnError($this->getCodeBlock(self::TAG_LEX_ON_ERROR, "return false;"));
        foreach ($this->usedClassList as $usedClassAlias => $usedClassName) {
            $matcherSpec->addUsedClass($usedClassName, is_string($usedClassAlias) ? $usedClassAlias : null);
        }
        return $matcherSpec;
    }

    /**
     * @param int|null $tokenId
     * @param string $code
     * @throws Exception
     */
    private function processPhpToken(?int $tokenId, string $code): void
    {
        $this->skipToken = false;
        if ($this->isCurrentCodeBlock(self::TAG_LEX_HEADER)) {
            if (T_NAMESPACE === $tokenId && !$this->codeBlockExists(self::LEX_NAMESPACE)
            ) {
                $this->replaceCurrentCodeBlock(self::LEX_NAMESPACE);
                $this->skipToken = true;
            }
            if (T_USE === $tokenId) {
                $this->replaceCurrentCodeBlock(self::LEX_USE);
                $this->skipToken = true;
            }
        }
        if ($this->isCurrentCodeBlock(self::LEX_NAMESPACE) && null === $tokenId && ';' == $code) {
            $this->appendCurrentCodeBlock("\\");
            $this->restoreCurrentCodeBlock();
            $this->skipToken = true;
        }
        if ($this->isCurrentCodeBlock(self::LEX_USE) && null === $tokenId && ';' == $code) {
            [$usedClass, $alias] = $this->parseUsedClass($this->getCodeBlock(self::LEX_USE));
            if (isset($alias)) {
                $this->usedClassList[$alias] = $usedClass;
            } else {
                $this->usedClassList[] = $usedClass;
            }
            $this->resetCurrentCodeBlock();
            $this->restoreCurrentCodeBlock();
            $this->skipToken = true;
        }
        if (T_DOC_COMMENT === $tokenId) {
            $docBlock = $this->getDocBlockFactory()->create($code);
            $this->detectTargetClassName($docBlock);
            $this->detectTemplateClassName($docBlock);
            $this->detectCodeBlock($docBlock);
        }
        if (!$this->skipToken) {
            $this->appendCurrentCodeBlock($code);
        }
    }

    /**
     * @param string $usedClass
     * @return array
     * @throws Exception
     */
    private function parseUsedClass(string $usedClass): array
    {
        $pattern = '#^(?P<className>\S+)(?:\s+(?:as\s+)?(?P<alias>\S+))?$#i';
        $pregResult = preg_match($pattern, $usedClass, $matches);
        if (1 !== $pregResult) {
            throw new Exception("Invalid lexer specification: invalid used class expression");
        }
        return [$matches['className'], $matches['alias'] ?? null];
    }

    /**
     * @param DocBlock $docBlock
     * @throws Exception
     */
    private function detectTargetClassName(DocBlock $docBlock): void
    {
        $tagName = self::TAG_LEX_TARGET_CLASS;
        if (!$docBlock->hasTag($tagName)) {
            return;
        }
        $this->skipToken = true;
        $tagList = $docBlock->getTagsByName($tagName);
        if (isset($this->targetClassName) || count($tagList) != 1) {
            throw new Exception("Invalid lexer specification: duplicated @{$tagName} tag");
        }
        $this->targetClassName = (string) array_pop($tagList);
    }

    /**
     * @param DocBlock $docBlock
     * @throws Exception
     */
    private function detectTemplateClassName(DocBlock $docBlock): void
    {
        $tagName = self::TAG_LEX_TEMPLATE_CLASS;
        if (!$docBlock->hasTag($tagName)) {
            return;
        }
        $this->skipToken = true;
        $tagList = $docBlock->getTagsByName($tagName);
        if (isset($this->templateClassName) || count($tagList) != 1) {
            throw new Exception("Invalid lexer specification: duplicated @{$tagName} tag");
        }
        $this->templateClassName = (string) array_pop($tagList);
    }

    /**
     * @param DocBlock $docBlock
     * @throws Exception
     */
    private function detectCodeBlock(DocBlock $docBlock): void
    {
        $codeBlockKeyList = [
            self::TAG_LEX_HEADER,
            self::TAG_LEX_BEFORE_MATCH,
            self::TAG_LEX_ON_TRANSITION,
            self::TAG_LEX_ON_ERROR,
        ];
        $codeBlockKey = null;
        foreach ($codeBlockKeyList as $tagName) {
            if ($docBlock->hasTag($tagName)) {
                if (isset($codeBlockKey)) {
                    throw new Exception("Invalid lexer specification: @{$tagName} tag conflicts with @{$codeBlockKey}");
                }
                $codeBlockKey = $tagName;
            }
        }
        if (isset($codeBlockKey)) {
            if ($this->codeBlockExists($codeBlockKey)) {
                throw new Exception("Invalid lexer specification: duplicated @{$codeBlockKey} tag");
            }
            $this->skipToken = true;
            $this->switchCurrentCodeBlock($codeBlockKey);
        }
    }

    private function getDocBlockFactory(): DocBlockFactoryInterface
    {
        if (!isset($this->docBlockFactory)) {
            $this->docBlockFactory = DocBlockFactory::createInstance();
        }
        return $this->docBlockFactory;
    }

    private function switchCurrentCodeBlock(string $key): void
    {
        $this->codeBlockKey = $key;
        $this->codeBlockList[$this->codeBlockKey] = '';
    }

    private function replaceCurrentCodeBlock(string $key): void
    {
        $this->codeBlockStack[] = $this->codeBlockKey;
        $this->switchCurrentCodeBlock($key);
    }

    private function restoreCurrentCodeBlock(): void
    {
        $this->codeBlockKey = array_pop($this->codeBlockStack);
    }

    private function codeBlockExists(string $key): bool
    {
        return isset($this->codeBlockList[$key]);
    }

    private function getCodeBlock(string $key, string $defaultValue = ''): string
    {
        return trim($this->codeBlockList[$key] ?? $defaultValue);
    }

    private function resetCurrentCodeBlock(): void
    {
        unset($this->codeBlockList[$this->codeBlockKey]);
    }

    private function appendCurrentCodeBlock(string $code): void
    {
        if (isset($this->codeBlockKey)) {
            $this->codeBlockList[$this->codeBlockKey] .= $code;
        }
    }

    private function isCurrentCodeBlock(string $key): bool
    {
        return $key === $this->codeBlockKey;
    }
}
