<?php

declare(strict_types=1);
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

arch('app uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debug statements in app')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('actions live in App\Actions')
    ->expect('App\Actions')
    ->toBeClasses()
    ->toHaveSuffix('Action');

arch('controllers extend base controller')
    ->expect('App\Http\Controllers')
    ->toExtend(Controller::class);

arch('models extend eloquent model')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class);
