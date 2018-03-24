<?php

namespace Remorhaz\UniLex\RegExp\FSM;

interface StateMapInterface
{

    public function stateExists(int $stateId): bool;
}
