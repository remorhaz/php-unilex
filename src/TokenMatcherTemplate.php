<?php

namespace Remorhaz\UniLex;

use Remorhaz\UniLex\Lexer\TokenMatcherContextInterface;

abstract class TokenMatcherTemplate implements TokenMatcherInterface
{

    protected $token;

    /**
     * @return Token
     * @throws Exception
     */
    public function getToken(): Token
    {
        if (!isset($this->token)) {
            throw new Exception("Token is not defined");
        }
        return $this->token;
    }

    protected function createContext(
        CharBufferInterface $buffer,
        TokenFactoryInterface $tokenFactory
    ): TokenMatcherContextInterface {
        $onConstruct = function (): void {
            unset($this->token);
        };
        $onSetNewToken = function (int $tokenType) use ($tokenFactory): void {
            $this->token = $tokenFactory->createToken($tokenType);
        };
        $onGetToken = function (): Token {
            return $this->getToken();
        };
        return new class($buffer, $onConstruct, $onSetNewToken, $onGetToken) implements TokenMatcherContextInterface {

            private $buffer;

            private $onSetNewToken;

            private $onGetToken;

            private $symbolList = [];

            public function __construct(
                CharBufferInterface $buffer,
                callable $onConstruct,
                callable $onSetNewToken,
                callable $onGetToken
            ) {
                $this->buffer = $buffer;
                $this->onSetNewToken = $onSetNewToken;
                $this->onGetToken = $onGetToken;
                call_user_func($onConstruct);
            }

            public function setNewToken(int $tokenType): TokenMatcherContextInterface
            {
                call_user_func($this->onSetNewToken, $tokenType);
                return $this;
            }

            /**
             * @param string $name
             * @param $value
             * @return TokenMatcherContextInterface
             */
            public function setTokenAttribute(string $name, $value): TokenMatcherContextInterface
            {
                $this
                    ->getToken()
                    ->setAttribute($name, $value);
                return $this;
            }

            public function getToken(): Token
            {
                return call_user_func($this->onGetToken);
            }

            public function getBuffer(): CharBufferInterface
            {
                return $this->buffer;
            }

            public function storeCurrentSymbol(): TokenMatcherContextInterface
            {
                $this->symbolList[] = $this->getBuffer()->getSymbol();
                return $this;
            }

            public function getStoredSymbolList(): array
            {
                return $this->symbolList;
            }

            public function getSymbolString(): string
            {
                $buffer = '';
                foreach ($this->symbolList as $symbol) {
                    if (0x00 <= $symbol && $symbol <= 0x7F) {
                        $buffer .= chr($symbol);
                        continue;
                    }
                    if (0x80 <= $symbol && $symbol <= 0x07FF) {
                        $buffer .= chr(0xC0 | ($symbol >> 0x06));
                        $buffer .= chr(0x80 | ($symbol & 0x3F));
                        continue;
                    }
                    if (0x0800 <= $symbol && $symbol <= 0xFFFF) {
                        $buffer .= chr(0xE0 | ($symbol >> 0x0C));
                        $buffer .= chr(0x80 | (($symbol >> 0x06) & 0x3F));
                        $buffer .= chr(0x80 | ($symbol & 0x3F));
                        continue;
                    }
                    if (0x010000 <= $symbol && $symbol <= 0x10FFFF) {
                        $buffer .= chr(0xF0 | ($symbol >> 0x12));
                        $buffer .= chr(0x80 | (($symbol >> 0x0C) & 0x3F));
                        $buffer .= chr(0x80 | (($symbol >> 0x06) & 0x3F));
                        $buffer .= chr(0x80 | ($symbol & 0x3F));
                        continue;
                    }
                    $buffer .= '�';
                }
                return $buffer;
            }
        };
    }
}
