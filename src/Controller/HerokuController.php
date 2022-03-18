<?php

namespace App\Controller;

use App\Handler\ChangelogHandler;
use JoliCode\Slack\Api\Client as SlackClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HerokuController extends AbstractController
{
    /** @var SlackClient */
    protected $slack;

    /** @var ChangelogHandler */
    protected $changelogHandler;

    public function __construct(SlackClient $slack, ChangelogHandler $changelogHandler)
    {
        $this->slack            = $slack;
        $this->changelogHandler = $changelogHandler;
    }

    /**
     * @Route("/heroku/deploy-hook", name="heroku_deploy_hook", methods={"POST"})
     *
     * Available post parameters are:
     * - app (ex: chronos-api-production)
     * - app_uuid
     * - user (ex: prenom.nom@tiime.fr)
     * - url (ex: http://chronos-api-production.herokuapp.com)
     * - head (short commit hash)
     * - head_long (commit hash)
     * - prev_head (commit hash)
     * - git_log
     * - release (ex: v42)
     */
    public function herokuDeployHookAction(Request $request): Response
    {
        $repositoryOwner = getenv('GITHUB_REPOSITORY_OWNER');
        $repositoryName  = getenv('GITHUB_REPOSITORY_NAME');

        $requestBag = $request->request;

        $head      = $requestBag->get('head');
        $prev_head = $requestBag->get('prev_head');
        $release   = $requestBag->get('release');
        $diff_url  = sprintf('https://github.com/%s/%s/compare/%s...%s', $repositoryOwner, $repositoryName, $prev_head, $head);

        $commits = $this->changelogHandler->getOrderedChangelog($prev_head, $head);

        $this->slack->filesUpload([
            'channels'        => getenv('SLACK_CHANGELOG_CHANNEL'),
            'content'         => implode(PHP_EOL, $commits),
            'title'           => 'Changelog for release ' . $release,
            'filename'        => 'changelog_release_' . $release . '.txt',
            'initial_comment' => sprintf('<%s|MEP Back>', $diff_url),
        ]);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
