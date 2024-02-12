<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\TokenExtractInterface;

abstract class TokenMatcherTemplate implements TokenMatcherInterface
{
    private ?Token $token = null;

    private string $mode = self::DEFAULT_MODE;

    /**
     * @return Token
     * @throws Exception
     */
    public function getToken(): Token
    {
        return $this->token ?? throw new Exception("Token is not defined");
    }

    protected function createContext(
        CharBufferInterface $buffer,
        TokenFactoryInterface $tokenFactory,
    ): TokenMatcherContextInterface {
        $onConstruct = function (): void {
            unset($this->token);
        };
        $onSetNewToken = function (int $tokenType) use ($tokenFactory): void {
            $this->token = $tokenFactory->createToken($tokenType);
        };
        $onGetToken = fn (): Token => $this->getToken();
        $onSetMode = function (string $mode): void {
            $this->mode = $mode;
        };
        $onGetMode = fn (): string => $this->mode;

        return new class (
            $buffer,
            $onConstruct,
            $onSetNewToken,
            $onGetToken,
            $onSetMode,
            $onGetMode,
        ) implements TokenMatcherContextInterface
        {
            /**
             * @var callable(int):void
             */
            private mixed $onSetNewToken;

            /**
             * @var callable():Token
             */
            private mixed $onGetToken;

            /**
             * @var callable(string):void
             */
            private mixed $onSetMode;

            /**
             * @var callable():string
             */
            private mixed $onGetMode;

            /**
             * @param CharBufferInterface   $buffer
             * @param callable():void       $onConstruct
             * @param callable(int):void    $onSetNewToken
             * @param callable():Token      $onGetToken
             * @param callable(string):void $onSetMode
             * @param callable():string     $onGetMode
             */
            public function __construct(
                private readonly CharBufferInterface $buffer,
                callable $onConstruct,
                callable $onSetNewToken,
                callable $onGetToken,
                callable $onSetMode,
                callable $onGetMode,
            ) {
                $this->onSetNewToken = $onSetNewToken;
                $this->onGetToken = $onGetToken;
                $this->onSetMode = $onSetMode;
                $this->onGetMode = $onGetMode;
                $onConstruct();
            }

            public function setNewToken(int $tokenType): TokenMatcherContextInterface
            {
                ($this->onSetNewToken)($tokenType);

                return $this;
            }

            public function setTokenAttribute(string $name, mixed $value): TokenMatcherContextInterface
            {
                $this
                    ->getToken()
                    ->setAttribute($name, $value);

                return $this;
            }

            public function getToken(): Token
            {
                return ($this->onGetToken)();
            }

            public function getBuffer(): CharBufferInterface
            {
                return $this->buffer;
            }

            public function getSymbolString(): string
            {
                $buffer = $this->getBuffer();

                return $buffer instanceof TokenExtractInterface
                    ? $buffer->getTokenAsString()
                    : throw new Exception("Extracting strings is not supported by buffer");
            }

            /**
             * @return list<int>
             */
            public function getSymbolList(): array
            {
                $buffer = $this->getBuffer();

                return $buffer instanceof TokenExtractInterface
                    ? $buffer->getTokenAsArray()
                    : throw new Exception("Extracting arrays is not supported by buffer");
            }

            public function getMode(): string
            {
                return ($this->onGetMode)();
            }

            public function setMode(string $mode): TokenMatcherContextInterface
            {
                ($this->onSetMode)($mode);

                return $this;
            }
        };
    }
}
