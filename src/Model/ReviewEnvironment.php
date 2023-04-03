<?php

namespace App\Model;

class ReviewEnvironment
{
    private ?JirHubTask $jirHubTask;
    private ?string $pullRequestTitle;

    public function __construct(
        private readonly string $name,
        private readonly ?string $owner = null
    ) {
        $this->jirHubTask = null;
        $this->pullRequestTitle = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getJirHubTask(): ?JirHubTask
    {
        return $this->jirHubTask;
    }

    public function setJirHubTask(JirHubTask $jirHubTask): self
    {
        $this->jirHubTask = $jirHubTask;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function getPullRequestTitle(): string
    {
        if (null === $this->pullRequestTitle) {
            $explodeTitle = preg_split('/\[?ta-[0-9]{1,9}(\]|( ?(:|\|)))? ?/i', $this->jirHubTask->getGithubPullRequest()->getTitle());
            $this->pullRequestTitle = trim(array_pop($explodeTitle));
        }

        return $this->pullRequestTitle;
    }
}
