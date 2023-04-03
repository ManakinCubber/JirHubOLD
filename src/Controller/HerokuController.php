<?php

namespace App\Controller;

use App\Handler\ChangelogHandler;
use JoliCode\Slack\Api\Client as SlackClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HerokuController extends AbstractController
{
    private FilesystemAdapter $cache;

    public function __construct(
        private readonly SlackClient $slack,
        private readonly ChangelogHandler $changelogHandler,
        private readonly string $deployHookToken,
        private readonly string $repositoryOwner,
        private readonly string $repositoryName,
        private readonly string $slackChangelogChannel
    ) {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * Available post parameters are:
     * - app (ex: chronos-api-production)
     * - app_uuid
     * - user (ex: prenom.nom@tiime.fr)
     * - url (ex: http://chronos-api-production.herokuapp.com)
     * - head (short commit hash)
     * - head_long (commit hash)
     * - prev_head (commit hash)
     * - git_log
     * - release (ex: v42).
     */
    #[Route('/heroku/deploy-hook', name: 'heroku_deploy_hook', methods: ['POST'])]
    public function herokuDeployHookAction(Request $request): Response
    {
        $token = $request->query->get('token');

        if (!$token || $token !== $this->deployHookToken) {
            throw new UnauthorizedHttpException('Wrong of missing token');
        }

        $repositoryOwner = $this->repositoryOwner;
        $repositoryName = $this->repositoryName;

        $requestBag = $request->request;

        $head = $requestBag->get('head');
        $prev_head = $requestBag->get('prev_head');
        $diff_url = sprintf('https://github.com/%s/%s/compare/%s...%s', $repositoryOwner, $repositoryName, $prev_head, $head);
        $release = $requestBag->get('release');

        $lastRelease = $this->cache->getItem('lastRelease');

        if ($lastRelease->isHit() && $lastRelease->get() === $release) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $lastRelease->set($release);
        $this->cache->save($lastRelease);

        $commits = $this->changelogHandler->getChangelog($prev_head, $head);

        $this->slack->filesUpload([
            'channels' => $this->slackChangelogChannel,
            'content' => implode(PHP_EOL, $commits),
            'title' => 'Changelog for release '.$release,
            'filename' => 'changelog_release_'.$release.'.txt',
            'initial_comment' => sprintf('<%s|MEP Back>', $diff_url),
        ]);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
