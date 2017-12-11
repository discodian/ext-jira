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

namespace Discodian\JIRA\Listeners;

use Discodian\Extend\Concerns\AnswersMessages;
use Discodian\Extend\Messages\Message;
use Discodian\Extend\Responses\TextResponse;
use Discodian\Parts\Guild\Embed;
use Illuminate\Support\Arr;
use JiraRestApi\Issue\IssueService;
use React\Promise\Deferred;

class IssueSearch implements AnswersMessages
{
    /**
     * @var IssueService
     */
    protected $issues;

    public function __construct(IssueService $issues)
    {
        $this->issues = $issues;
    }

    public function respond(Message $message, array $options = [])
    {
        $defer = new Deferred();

        logs('matches', $options['matches']['issue']);

        collect(Arr::get($options, 'matches.issue', []))
            ->each(function (string $key) use ($defer) {
                $issue = $this->issues->get($key, [
                    'fields' => [
                        'summary',
                        'description',
                        'status',
                        'timetracking',
                        'assignee'
                    ]
                ]);

                logs("JIRA issue received for $key: ", $issue->fields->toArray());

                $embed = new Embed([
                    'title' => $issue->fields->summary,
                    'url' => $this->linkToIssue($issue->key),
                ]);

                $fields = [];

                if ($issue->fields->timeTracking) {
                    $fields[] = [
                        'name' => 'Time spent',
                        'value' => $issue->fields->timeTracking->timeSpent
                    ];
                }
                if ($issue->fields->assignee) {
                    $fields[] = [
                        'name' => 'Assignee',
                        'value' => $issue->fields->assignee->displayName
                    ];
                }
                if ($issue->fields->status) {
                    $embed->icon_url = $issue->fields->status->iconUrl;
                }

                $embed->fields = $fields;

                $response = new TextResponse();

                if (env('JIRA_REPLY_PRIVATELY', false)) {
                    $response->privately();
                }

                $response->embed = $embed;

                $defer->resolve($response);
            });

        return $defer->promise();
    }

    protected function linkToIssue(string $key)
    {
        return sprintf("%s/browse/%s", env('JIRA_HOST'), $key);
    }


    /**
     * In case you want to listen to specific commands.
     *
     * @eg with $ext as prefix: "$ext search foo"
     *
     * @return null|string
     */
    public function forPrefix(): ?string
    {
        return null;
    }

    /**
     * Listen to messages only when messaged.
     *
     * @return bool
     */
    public function whenMentioned(): bool
    {
        return false;
    }

    /**
     * Listen to messages only when addressed. So the bot
     * has to be mentioned first.
     *
     * @return bool
     */
    public function whenAddressed(): bool
    {
        return false;
    }

    /**
     * Specify the channels to listen to.
     *
     * @return array|null
     */
    public function onChannels(): ?array
    {
        return null;
    }

    /**
     * Specify a regular expression match to check for in a message
     * text to listen to.
     *
     * @return null|string
     */
    public function whenMessageMatches(): ?string
    {
        $projects = explode(',', env('JIRA_PROJECTS', ''));

        $projects = collect($projects);

        if ($projects->isNotEmpty()) {
            return sprintf(
                '(?<issue>(?<project>%s)\-(?<id>[0-9]+))',
                $projects->implode('|')
            );
        }

        return null;
    }
}
