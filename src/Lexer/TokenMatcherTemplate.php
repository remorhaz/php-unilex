<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\TokenExtractInterface;

abstract class TokenMatcherTemplate implements TokenMatcherInterface
{

    private $token;

    private $mode = self::DEFAULT_MODE;

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
        $onSetMode = function (string $mode): void {
            $this->mode = $mode;
        };
        $onGetMode = function (): string {
            return $this->mode;
        };

        return new class (
            $buffer,
            $onConstruct,
            $onSetNewToken,
            $onGetToken,
            $onSetMode,
            $onGetMode
        ) implements TokenMatcherContextInterface {

            private $buffer;

            private $onSetNewToken;

            private $onGetToken;

            private $onSetMode;

            private $onGetMode;

            public function __construct(
                CharBufferInterface $buffer,
                callable $onConstruct,
                callable $onSetNewToken,
                callable $onGetToken,
                callable $onSetMode,
                callable $onGetMode
            ) {
                $this->buffer = $buffer;
                $this->onSetNewToken = $onSetNewToken;
                $this->onGetToken = $onGetToken;
                $this->onSetMode = $onSetMode;
                $this->onGetMode = $onGetMode;
                call_user_func($onConstruct);
            }

            public function setNewToken(int $tokenType): TokenMatcherContextInterface
            {
                call_user_func($this->onSetNewToken, $tokenType);

                return $this;
            }

            /**
             * @param string $name
             * @param        $value
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

            public function getMode(): string
            {
                return call_user_func($this->onGetMode);
            }

            public function setMode(string $mode): TokenMatcherContextInterface
            {
                call_user_func($this->onSetMode, $mode);

                return $this;
            }
        };
    }
}
