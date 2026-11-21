<?php
namespace Tests\Feature;
use Tests\TestCase;
class PosPagesTest extends TestCase
{
    public function test_public_pages_and_all_navigation_links(): void
    {
        foreach (['/' => 'Point-of-Sale System', '/about' => 'About this site', '/customers' => 'Customer Accounts', '/users' => 'User Accounts'] as $path => $heading) {
            $response = $this->get($path)->assertOk()->assertSee($heading);
            foreach (['home', 'about', 'customers', 'users'] as $route) {
                $response->assertSee(route($route));
            }
        }
    }
    public function test_account_tables_show_five_records(): void
    {
        foreach (['/customers' => ['Alex Santos', 'Taylor Ramos'], '/users' => ['Admin Demo', 'Staff Demo']] as $path => $names) {
            $response = $this->get($path)->assertOk();
            foreach ($names as $name) { $response->assertSee($name); }
            $this->assertSame(6, substr_count($response->getContent(), '<tr>'));
        }
    }
}
