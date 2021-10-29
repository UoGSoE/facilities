<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuildingReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_building_report_page()
    {
        $this->markTestSkipped('Do we need a building report?');
    }
}
