<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Domain;

use RuntimeException;

final class InternalLinkService {

private const MAX_LINKS_PER_SUBJECT = 20;


    public function __construct(
        private InternalLinkRepositoryInterface $repository
    ) {}

    /**
     * @param array<int, array{
     *   to_type: string,
     *   to_id: int,
     *   anchor: string,
     *   nofollow?: bool
     * }>
     */
    public function syncLinksForSubject(
        LinkSubject $from,
        array $links
    ): void {
        // 1️⃣ bestaande links verwijderen
        $this->repository->deleteAllFrom($from);

        // 2️⃣ nieuwe links toevoegen (via bestaande regels)
        foreach ($links as $row) {
            $this->addLink(
                $from,
                new LinkSubject(
                    $row['to_type'],
                    (int) $row['to_id']
                ),
                trim($row['anchor'] ?? ''),
                !empty($row['nofollow'])
           );
     }
}


public function addLink(
        LinkSubject $from,
        LinkSubject $to,
        string $anchorText,
        bool $nofollow = false
    ): void {
        $anchorText = trim($anchorText);

        // 1️⃣ self-link blokkeren
        if (
            $from->type() === $to->type() &&
            $from->id() === $to->id()
        ) {
            throw new RuntimeException(
                'Self-linking is not allowed'
            );
        }

        // 2️⃣ lege anchor blokkeren
        if ($anchorText === '') {
            throw new RuntimeException(
                'Anchor text must not be empty'
            );
        }

        $existingLinks = $this->repository->findFrom($from);

        // 3️⃣ max links per subject
        if (count($existingLinks) >= self::MAX_LINKS_PER_SUBJECT) {
            throw new RuntimeException(
                'Maximum number of internal links reached'
            );
        }

        // 4️⃣ duplicate link blokkeren
        foreach ($existingLinks as $link) {
            if (
                $link->to()->type() === $to->type() &&
                $link->to()->id() === $to->id() &&
                $link->anchorText() === $anchorText
            ) {
                throw new RuntimeException(
                    'Duplicate internal link'
                );
            }
        }

        $this->repository->save(
            new InternalLink(
                from: $from,
                to: $to,
                anchorText: $anchorText,
                nofollow: $nofollow
            )
        );
    }

    /**
     * Use-case: verwijder een interne link
     */
    public function removeLink(InternalLink $link): void {
        $this->repository->delete($link);
    }
}


?>