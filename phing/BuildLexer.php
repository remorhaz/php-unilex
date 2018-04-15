<?php

use Remorhaz\UniLex\Lexer\TokenMatcherSpec;

class BuildLexer extends Task
{

    public function main()
    {
        $specFile = __DIR__ . "/../lex/Unicode/Utf8Lex.php";
        $specData = file_get_contents($specFile);
        $buffer = [];
        $docBlockFactory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $concatenableBlockList = [
            'lexHeader',
            'lexBeforeMatch',
            'lexOnTransition',
            'lexOnToken',
            'lexOnError',
            'lexToken',
        ];
        $tokenBlockList = [];
        $tokenList = token_get_all($specData);
        $bufferIndex = null;
        $regExp = null;
        $targetClassName = null;
        $templateClassName = null;
        foreach ($tokenList as $tokenData) {
            if (is_array($tokenData)) {
                [$tokenId, $code] = $tokenData;
                if (T_DOC_COMMENT == $tokenId) {
                    $docBlock = $docBlockFactory->create($code);
                    if ($docBlock->hasTag('lexTargetClass') && !isset($targetClassName)) {
                        $targetClassName = (string) $docBlock->getTagsByName('lexTargetClass')[0];
                    }
                    if ($docBlock->hasTag('lexTemplateClass') && !isset($templateClassName)) {
                        $templateClassName = (string) $docBlock->getTagsByName('lexTemplateClass')[0];
                    }
                    $concatenableBlock = null;
                    foreach ($concatenableBlockList as $tagName) {
                        if ($docBlock->hasTag($tagName)) {
                            if (isset($concatenableBlock) && $tagName != $concatenableBlock) {
                                throw new BuildException(
                                    "Lex spec block conflict: {$tagName} and {$concatenableBlock}"
                                );
                            }
                            $concatenableBlock = $tagName;
                        }
                    }
                    if (isset($concatenableBlock)) {
                        if ($concatenableBlock == 'lexToken') {
                            $regExpList = $docBlock->getTagsByName($concatenableBlock);
                            if (count($regExpList) != 1) {
                                throw new BuildException("Exactly one @lexToken tag allowed");
                            }
                            $desc = (string) $regExpList[0];
                            $matchResult = preg_match('#^/(.*)/$#', $desc, $matches);
                            if (1 !== $matchResult) {
                                throw new BuildException("Regular expression must be framed by \"/\" symbols");
                            }
                            $regExp = $matches[1];
                            if (isset($tokenBlockList[$regExp])) {
                                throw new BuildException("Token block for {$regExp} already exists");
                            }
                            $tokenBlockList[$regExp] = '';
                            $bufferIndex = null;
                            continue;
                        }
                        $bufferIndex = $concatenableBlock;
                        $regExp = null;
                        if (!isset($buffer[$bufferIndex])) {
                            $buffer[$bufferIndex] = '';
                        }
                        continue;
                    }
                }
            } else {
                $code = $tokenData;
            }
            if (isset($bufferIndex)) {
                $buffer[$bufferIndex] .= $code;
            }
            if (isset($regExp)) {
                $tokenBlockList[$regExp] .= $code;
            }
        }
        $spec = new TokenMatcherSpec($targetClassName, $templateClassName);
        $spec
            ->setHeader(trim($buffer['lexHeader'] ?? ''))
            ->setBeforeMatch(trim($buffer['lexBeforeMatch'] ?? ''))
            ->setOnToken(trim($buffer['lexOnToken'] ?? ''))
            ->setOnTransition(trim($buffer['lexOnTransition'] ?? ''))
            ->setOnError(trim($buffer['lexOnError'] ?? 'return false;'));
        foreach ($tokenBlockList as $regExp => $code) {
            $tokenSpec = new \Remorhaz\UniLex\Lexer\TokenSpec($regExp, trim($code));
            $spec->addTokenSpec($tokenSpec);
        }
        $generator = new \Remorhaz\UniLex\Lexer\TokenMatcherGenerator($spec);
        echo $generator->getOutput();
    }
}
