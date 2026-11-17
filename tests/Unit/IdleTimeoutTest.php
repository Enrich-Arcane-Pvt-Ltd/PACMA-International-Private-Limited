<?php

namespace Tests\Unit;

use App\Http\Middleware\IdleTimeout;
use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\TestCase;

class IdleTimeoutTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Container::setInstance(null);
        parent::tearDown();
    }

    /** @dataProvider sessions */
    public function test_deadline_is_enforced_without_refreshing_on_requests($age, $remember, $expires)
    {
        $container = new Container;
        Container::setInstance($container);
        Facade::setFacadeApplication($container);
        $factory = Mockery::mock(ResponseFactory::class);
        $factory->shouldReceive('json')->andReturnUsing(function ($data, $status) {
            return new JsonResponse($data, $status);
        });
        $container->instance(ResponseFactory::class, $factory);
        $guard = Mockery::mock();
        Auth::swap(Mockery::mock());
        Auth::shouldReceive('guard')->with('web')->andReturn($guard);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('viaRemember')->andReturn($remember);
        $guard->shouldReceive('logout')->times($expires ? 1 : 0);
        $request = Request::create('/protected', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $session = new Store('test', new ArraySessionHandler(120));
        $session->start();
        $last = $age === null ? null : microtime(true) - $age;
        if ($last) $session->put('idle_last_activity', $last);
        $request->setLaravelSession($session);
        $called = false;
        $response = (new IdleTimeout)->handle($request, function () use (&$called) {
            $called = true;
            return new JsonResponse(['ok' => true]);
        });
        $this->assertSame(!$expires, $called);
        $this->assertSame($expires ? 401 : 200, $response->getStatusCode());
        if ($remember && $last === null && !$expires) {
            $this->assertGreaterThan(microtime(true) - 2, $session->get('idle_last_activity'));
            $this->assertNotEmpty($session->get('idle_tab_key'));
        } else {
            $this->assertSame($expires ? null : $last, $session->get('idle_last_activity'));
        }
    }

    public function sessions()
    {
        return [
            'active' => [30, false, false],
            'before deadline' => [299, false, false],
            'deadline' => [300, false, true],
            'sleep resume' => [300, false, true],
            'old session' => [null, false, true],
            'remember active session' => [10, true, false],
            'remember new session' => [null, true, false],
            'remember expired session' => [300, true, true],
        ];
    }
}
