<?php

namespace Tests\Feature;

use App\Http\Controllers\GrnController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class GrnDeleteAccessTest extends TestCase
{
    public function test_staff_cannot_delete_categories_even_with_product_permissions()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->type = 'staff';
        $user->shouldReceive('can')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        $this->assertFalse($user->canDeleteProductCategory());
        $this->assertFalse($user->canDeleteProductCategory(true));
        foreach ([\App\Http\Controllers\ProductCategoryController::class,
            \App\Http\Controllers\ProductSubCategoryController::class] as $class) {
            $controller = Mockery::mock($class)->makePartial();
            $response = $controller->destroy(1);
            $this->assertSame(302, $response->getStatusCode());
            $this->assertStringContainsString('Please contact an administrator', session('error'));
        }
        $user->type = 'Admin';
        $this->assertTrue($user->canDeleteProductCategory());
        $this->assertTrue($user->canDeleteProductCategory(true));
    }

    public function test_item_deletion_is_denied_to_staff_even_with_delete_permission()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->type = 'staff';
        $user->shouldReceive('can')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        $this->assertFalse($user->canDeleteProduct());
        $controller = Mockery::mock(\App\Http\Controllers\ProductController::class)->makePartial();
        $response = $controller->destroy(1);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('Permission denied.', session('error'));
        $user->type = 'Admin';
        $this->assertTrue($user->canDeleteProduct());
    }

    /** @dataProvider roles */
    public function test_delete_requires_admin_and_existing_delete_permission($type, $permission, $allowed)
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->type = $type;
        $user->shouldReceive('can')->with('delete grn')->andReturn($permission);
        $this->assertSame($allowed, $user->canDeleteGrn());
        if (!$allowed) {
            Auth::shouldReceive('user')->andReturn($user);
            $controller = Mockery::mock(GrnController::class)->makePartial();
            $response = $controller->destroy(1);
            $this->assertSame(302, $response->getStatusCode());
            $this->assertStringContainsString('Please contact an administrator', session('error'));
        }
    }

    public function roles()
    {
        return [
            ['staff', true, false], ['staff', false, false],
            ['Admin', true, true], ['Admin', false, false],
            ['company', true, true], ['super admin', true, true],
        ];
    }
}
