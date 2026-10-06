<?php

use SmartDato\DhlUnified\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @return array<string, mixed>
 */
function fixtureJson(string $name): array
{
    return json_decode((string) file_get_contents(__DIR__."/Fixtures/{$name}.json"), true, flags: JSON_THROW_ON_ERROR);
}
