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
use Discodian\Extend\Responses\Response;

class IssueSearch implements AnswersMessages
{
    public function respond(Message $message, array $options = []): ?Response
    {
        logs("Matches", $options['matches']);

        return null;
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
                '(?<project>%s)\-(?<issue>[0-9]+?)',
                $projects->implode('|')
            );
        }

        return null;
    }
}
