<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

interface StateMapInterface
{
    public function stateExists(int $stateId): bool;
}
