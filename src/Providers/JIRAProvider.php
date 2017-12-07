<?php

/*
 * This file is part of the Discodian bot toolkit.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://discodian.com
 * @see https://github.com/discodian
 */

namespace Discodian\JIRA\Providers;

use Illuminate\Support\ServiceProvider;
use JiraRestApi\Project\ProjectService;

class JIRAProvider extends ServiceProvider
{
    public function register()
    {
    }
}
