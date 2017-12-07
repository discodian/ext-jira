<?php

namespace Discodian\JIRA;

use Illuminate\Contracts\Foundation\Application;

return function (Application $app) {
    $app->register(Providers\JIRAProvider::class);
};
