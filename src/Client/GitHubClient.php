<?php

namespace App\Client;

use Github\Api\Issue;
use Github\Api\Issue\Labels;
use Github\Api\PullRequest;
use Github\Api\PullRequest\Review;
use Github\Api\Repository\Commits;
use Github\AuthMethod;
use Github\Client;
use Github\HttpClient\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class GitHubClient
{
    private Client $client;

    public function __construct(
        string $gitHubAppId,
        string $gitHubPrivateRsaKey,
        string $gitHubAppInstallationId
    ) {
        $builder = new Builder();

        $this->client = new Client($builder, 'machine-man-preview');

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            Key\InMemory::plainText($gitHubPrivateRsaKey)
        );

        $now = new \DateTimeImmutable();
        $jwt = $config->builder(ChainedFormatter::withUnixTimestampDates())
            ->issuedBy($gitHubAppId)
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 minute'))
            ->getToken(new Sha256(), $config->signingKey());

        $this->client->authenticate($jwt->toString(), null, AuthMethod::JWT);
        $res = $this->client->apps()->createInstallationToken($gitHubAppInstallationId);
        $this->client->authenticate($res['token'], null, AuthMethod::ACCESS_TOKEN);
    }

    public function issues(): Issue
    {
        return $this->client->issues();
    }

    public function pullRequests(): PullRequest
    {
        return $this->client->pullRequests();
    }

    public function labels(): Labels
    {
        return $this->client->issues()->labels();
    }

    public function reviews(): Review
    {
        return $this->client->pullRequests()->reviews();
    }

    public function commits(): Commits
    {
        return $this->client->repositories()->commits();
    }
}
