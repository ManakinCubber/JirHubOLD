<?php

namespace App\Model;

readonly class JiraTransition
{
    public function __construct(
        private int $id,
        private string $comment = ''
    ) {
    }

    public function toArray(): array
    {
        return [
            'update' => [
                'comment' => [
                    [
                        'add' => [
                            'body' => [
                                'type' => 'doc',
                                'version' => 1,
                                'content' => [
                                    ['text' => $this->comment, 'type' => 'text'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'transition' => [
                'id' => (string) $this->id,
            ],
        ];
    }
}
