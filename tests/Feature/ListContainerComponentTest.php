<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListContainerComponentTest extends TestCase
{
    /**
     * Test komponen dengan data object
     */
    public function test_component_with_object_data()
    {
        $items = collect([
            (object) [
                'name' => 'Test Item 1',
                'icon' => 'map-pin',
                'status' => 'Active',
                'meta' => 'Test meta'
            ],
            (object) [
                'name' => 'Test Item 2',
                'status' => 'Inactive'
            ]
        ]);

        $view = $this->blade(
            '<x-list-container 
                id="test-list" 
                theme="blue" 
                title="Test Items" 
                :count="2" 
                :items="$items" 
                item-type="default" 
            />',
            ['items' => $items]
        );

        $view->assertSee('Test Item 1');
        $view->assertSee('Test Item 2');
        $view->assertSee('Active');
        $view->assertSee('Test meta');
    }

    /**
     * Test komponen dengan data array
     */
    public function test_component_with_array_data()
    {
        $items = [
            [
                'name' => 'Array Item 1',
                'icon' => 'star',
                'status' => 'Active',
                'meta' => 'Array meta'
            ],
            [
                'name' => 'Array Item 2',
                'status' => 'Pending'
            ]
        ];

        $view = $this->blade(
            '<x-list-container 
                id="test-array-list" 
                theme="green" 
                title="Array Items" 
                :count="2" 
                :items="$items" 
                item-type="default" 
            />',
            ['items' => $items]
        );

        $view->assertSee('Array Item 1');
        $view->assertSee('Array Item 2');
        $view->assertSee('Active');
        $view->assertSee('Array meta');
    }

    /**
     * Test komponen CCTV
     */
    public function test_component_cctv_type()
    {
        $items = collect([
            (object) [
                'id_cctv' => 1,
                'nama_lokasi' => 'CCTV Test Location',
                'link' => 'http://test-cctv.com',
                'lat' => -5.113,
                'lang' => 105.307,
                'count_seen' => 150
            ]
        ]);

        $view = $this->blade(
            '<x-list-container 
                id="cctv-test" 
                theme="red" 
                title="CCTV Test" 
                :count="1" 
                :items="$items" 
                item-type="cctv" 
            />',
            ['items' => $items]
        );

        $view->assertSee('CCTV Test Location');
        $view->assertSee('Online');
        $view->assertSee('150 views');
    }

    /**
     * Test komponen Virtual Tour
     */
    public function test_component_virtual_tour_type()
    {
        $items = collect([
            (object) [
                'nama_lokasi' => 'Virtual Tour Test',
                'link' => 'http://test-tour.com',
                'lat' => -5.113,
                'lang' => 105.307
            ]
        ]);

        $view = $this->blade(
            '<x-list-container 
                id="tour-test" 
                theme="blue" 
                title="Tour Test" 
                :count="1" 
                :items="$items" 
                item-type="virtual-tour" 
            />',
            ['items' => $items]
        );

        $view->assertSee('Virtual Tour Test');
        $view->assertSee('Available');
        $view->assertSee('360° Tour');
    }

    /**
     * Test komponen dengan mixed data
     */
    public function test_component_with_mixed_data()
    {
        $items = [
            (object) ['name' => 'Object Item', 'status' => 'Active'],
            ['name' => 'Array Item', 'status' => 'Inactive'],
            (object) ['nama_lokasi' => 'Location Item', 'status' => 'Pending']
        ];

        $view = $this->blade(
            '<x-list-container 
                id="mixed-test" 
                theme="purple" 
                title="Mixed Test" 
                :count="3" 
                :items="$items" 
                item-type="default" 
            />',
            ['items' => $items]
        );

        $view->assertSee('Object Item');
        $view->assertSee('Array Item');
        $view->assertSee('Location Item');
    }
}
