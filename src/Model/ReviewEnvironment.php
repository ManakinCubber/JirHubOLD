<?php

namespace App\Model;

class ReviewEnvironment
{
    /** @var string */
    private $name;

    /** @var JirHubTask */
    private $jirHubTask;

    /** @var string */
    private $pullRequestTitle;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function getPullRequestTitle(): string
    {
        if (null === $this->pullRequestTitle) {
            $explodeTitle = preg_split('/\[?ta-[0-9]{1,9}(\]|( ?(:|\|)))? ?/i', $this->jirHubTask->getGithubPullRequest()->getTitle());
            $this->pullRequestTitle = trim(array_pop($explodeTitle));
        }

        return $this->pullRequestTitle;
    }
}
