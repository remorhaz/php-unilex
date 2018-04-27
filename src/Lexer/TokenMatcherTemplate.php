<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\TokenExtractInterface;

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

            public function getSymbolString(): string
            {
                $buffer = $this->getBuffer();
                if ($buffer instanceof TokenExtractInterface) {
                    return $buffer->getTokenAsString();
                }
                throw new Exception("Extracting strings is not supported by buffer");
            }

            public function getSymbolList(): array
            {
                $buffer = $this->getBuffer();
                if ($buffer instanceof TokenExtractInterface) {
                    return $buffer->getTokenAsArray();
                }
                throw new Exception("Extracting arrays is not supported by buffer");
            }
        };
    }
}
