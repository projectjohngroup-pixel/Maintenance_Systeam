<?php

namespace Tests\Unit;

use Tests\TestCase;

class PdsNumberTest extends TestCase
{
    public function test_strips_integer_as_default_format(): void
    {
        $this->assertSame('133,394', pdsNumber(133394.00));
        $this->assertSame('500', pdsNumber(500.00));
        $this->assertSame('13,394', pdsNumber(13394.00));
    }

    public function test_strips_trailing_zero_decimal(): void
    {
        $this->assertSame('12.5', pdsNumber(12.50));
        $this->assertSame('133.9', pdsNumber(133.90));
    }

    public function test_keeps_real_decimals(): void
    {
        $this->assertSame('13,394.45', pdsNumber(13394.45));
        $this->assertSame('13.45', pdsNumber(13.45));
        $this->assertSame('133.94', pdsNumber(133.94));
    }

    public function test_does_not_round(): void
    {
        $this->assertSame('12.3456', pdsNumber(12.3456));
        $this->assertSame('1.23456', pdsNumber(1.23456));
    }

    public function test_handles_zero(): void
    {
        $this->assertSame('0', pdsNumber(0));
        $this->assertSame('0', pdsNumber(0.00));
        $this->assertSame('0', pdsNumber('0'));
    }

    public function test_handles_negative_and_large(): void
    {
        $this->assertSame('-1,234.5', pdsNumber(-1234.50));
        $this->assertSame('1,000,000', pdsNumber(1000000));
    }

    public function test_handles_null_and_non_numeric(): void
    {
        $this->assertSame('0', pdsNumber(null));
        $this->assertSame('0', pdsNumber('abc'));
    }

    public function test_handles_float_input(): void
    {
        $this->assertSame('12.5', pdsNumber(12.5));
    }

    public function test_indonesian_format(): void
    {
        $this->assertSame('13.394,45', pdsNumber(13394.45, ',', '.'));
        $this->assertSame('12,5', pdsNumber(12.5, ',', '.'));
        $this->assertSame('500', pdsNumber(500.00, ',', '.'));
    }
}
