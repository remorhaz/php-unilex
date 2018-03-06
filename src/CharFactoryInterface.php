<?php

namespace Remorhaz\UniLex;

interface CharFactoryInterface
{

    public function getChar(Token $token): int;
}
