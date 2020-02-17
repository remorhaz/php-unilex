<?php

namespace Remorhaz\UniLex\Lexer;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use ReflectionException;
use Remorhaz\UniLex\Exception;

class TokenMatcherSpecParser
{

    private const TAG_LEX_TARGET_CLASS = 'lexTargetClass';
    private const TAG_LEX_TEMPLATE_CLASS = 'lexTemplateClass';
    private const TAG_LEX_HEADER = 'lexHeader';
    private const TAG_LEX_BEFORE_MATCH = 'lexBeforeMatch';
    private const TAG_LEX_ON_TRANSITION = 'lexOnTransition';
    private const TAG_LEX_ON_ERROR = 'lexOnError';
    private const TAG_LEX_TOKEN = 'lexToken';
    private const TAG_LEX_MODE = 'lexMode';
    private const LEX_NAMESPACE = 'namespace';
    private const LEX_USE = 'use';
    private const LEX_TOKEN_REGEXP = 'token_regexp';
    private const LEX_TOKEN_CONTEXT = 'token_context';

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

    private $tokenSpecList = [];

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
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    private function buildMatcherSpec(): TokenMatcherSpec
    {
        $phpTokenList = token_get_all($this->source);
        foreach ($phpTokenList as $phpToken) {
            $argList = is_array($phpToken) ? $phpToken : [null, $phpToken];
            $this->processPhpToken(...$argList);
        }
        $this->afterProcessingSource();
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
        foreach ($this->tokenSpecList as $mode => $tokenSpecList) {
            $matcherSpec->addTokenSpec($mode, ...array_values($tokenSpecList));
        }
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
            $this->appendCodeBlock("\\");
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
            $this->resetCodeBlock();
            $this->restoreCurrentCodeBlock();
            $this->skipToken = true;
        }
        if (T_DOC_COMMENT === $tokenId) {
            $docBlock = $this->getDocBlockFactory()->create($code);
            $this->detectTargetClassName($docBlock);
            $this->detectTemplateClassName($docBlock);
            $this->detectCodeBlock($docBlock);
            $this->detectTokenRegExp($docBlock);
        }
        if (!$this->skipToken) {
            $this->appendCodeBlock($code);
        }
    }

    /**
     * @throws Exception
     */
    private function afterProcessingSource(): void
    {
        $this->detectUnprocessedTokenBlock();
    }

    /**
     * @param string $usedClass
     * @return array
     * @throws Exception
     */
    private function parseUsedClass(string $usedClass): array
    {
        /** @noinspection HtmlUnknownTag */
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
            self::TAG_LEX_TOKEN,
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
            $this->detectUnprocessedTokenBlock();
            if ($this->codeBlockExists($codeBlockKey)) {
                throw new Exception("Invalid lexer specification: duplicated @{$codeBlockKey} tag");
            }
            $this->skipToken = true;
            $this->switchCurrentCodeBlock($codeBlockKey);
        }
    }

    /**
     * @throws Exception
     */
    private function detectUnprocessedTokenBlock(): void
    {
        $codeBlockKey = self::TAG_LEX_TOKEN;
        if (!$this->codeBlockExists($codeBlockKey)) {
            return;
        }
        $context = $this->getCodeBlock(self::LEX_TOKEN_CONTEXT);
        $this->resetCodeBlock(self::LEX_TOKEN_CONTEXT);
        $tokenRegExp = $this->getCodeBlock(self::LEX_TOKEN_REGEXP);
        if (isset($this->tokenSpecList[$context][$tokenRegExp])) {
            throw new Exception("Invalid lexer specification: duplicated @{$codeBlockKey} /{$tokenRegExp}/ tag");
        }
        $this->resetCodeBlock(self::LEX_TOKEN_REGEXP);
        $tokenCode = $this->getCodeBlock($codeBlockKey);
        $this->resetCodeBlock($codeBlockKey);
        $tokenSpec = new TokenSpec($tokenRegExp, $tokenCode);
        $this->tokenSpecList[$context][$tokenSpec->getRegExp()] = $tokenSpec;
    }

    /**
     * @param DocBlock $docBlock
     * @throws Exception
     */
    private function detectTokenRegExp(DocBlock $docBlock): void
    {
        if (!$this->isCurrentCodeBlock(self::TAG_LEX_TOKEN)) {
            return;
        }
        $tagValue = $docBlock->getTagsByName(self::TAG_LEX_TOKEN)[0];
        $matchResult = preg_match('#^/(?P<regexp>.*)/$#', $tagValue, $matches);
        if (1 !== $matchResult) {
            throw new Exception("Invalid lexer specification: regular expression is not framed by \"/\"");
        }
        $regExp = $matches['regexp'];
        $this->replaceCurrentCodeBlock(self::LEX_TOKEN_REGEXP);
        $this->appendCodeBlock($regExp);
        $this->restoreCurrentCodeBlock();
        $context = $docBlock->hasTag(self::TAG_LEX_MODE)
            ? $docBlock->getTagsByName(self::TAG_LEX_MODE)[0]
            : TokenMatcherInterface::DEFAULT_MODE;
        $matchResult = preg_match('#^[a-zA-Z][0-9a-zA-Z]*$#i', $context);
        if (1 !== $matchResult) {
            throw new Exception("Invalid lexer specification: invalid context name {$context}");
        }
        $this->replaceCurrentCodeBlock(self::LEX_TOKEN_CONTEXT);
        $this->appendCodeBlock($context);
        $this->restoreCurrentCodeBlock();
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

    private function getCodeBlock(string $key = null, string $defaultValue = ''): string
    {
        $effectiveKey = $key ?? $this->codeBlockKey;
        return isset($effectiveKey)
            ? trim($this->codeBlockList[$effectiveKey] ?? $defaultValue)
            : trim($defaultValue);
    }

    private function resetCodeBlock(string $key = null): void
    {
        $effectiveKey = $key ?? $this->codeBlockKey;
        if (isset($effectiveKey)) {
            unset($this->codeBlockList[$key]);
        }
    }

    private function appendCodeBlock(string $code, string $key = null): void
    {
        $effectiveKey = $key ?? $this->codeBlockKey;
        if (isset($effectiveKey)) {
            $this->codeBlockList[$effectiveKey] .= $code;
        }
    }

    private function isCurrentCodeBlock(string $key): bool
    {
        return $key === $this->codeBlockKey;
    }
}
