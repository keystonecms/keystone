<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Infrastructure\Persistence;

use PDO;
use Keystone\Plugins\InternalLinks\Domain\{
    InternalLink,
    LinkSubject,
    InternalLinkRepositoryInterface
};

final class PdoInternalLinkRepository implements InternalLinkRepositoryInterface {


    public function __construct(
        private PDO $pdo
    ) {}

public function deleteAllFrom(LinkSubject $from): void
{
    $stmt = $this->pdo->prepare(
        'DELETE FROM internal_links
         WHERE from_type = :type
         AND from_id = :id'
    );

    $stmt->execute([
        'type' => $from->type(),
        'id'   => $from->id(),
    ]);
}


public function findFrom(LinkSubject $from): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM internal_links
             WHERE from_type = :type
             AND from_id = :id'
        );

        $stmt->execute([
            'type' => $from->type(),
            'id'   => $from->id(),
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn (array $row) => new InternalLink(
            from: new LinkSubject($row['from_type'], (int) $row['from_id']),
            to: new LinkSubject($row['to_type'], (int) $row['to_id']),
            anchorText: $row['anchor_text'],
            nofollow: (bool) $row['nofollow']
        ), $rows);
    }

    public function save(InternalLink $link): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO internal_links (
                from_type,
                from_id,
                to_type,
                to_id,
                anchor_text,
                nofollow,
                created_at,
                updated_at
            ) VALUES (
                :from_type,
                :from_id,
                :to_type,
                :to_id,
                :anchor_text,
                :nofollow,
                NOW(),
                NOW()
            )'
        );

        $stmt->execute([
            'from_type'   => $link->from()->type(),
            'from_id'     => $link->from()->id(),
            'to_type'     => $link->to()->type(),
            'to_id'       => $link->to()->id(),
            'anchor_text' => $link->anchorText(),
            'nofollow'    => (int) $link->nofollow(),
        ]);
    }

    public function delete(InternalLink $link): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM internal_links
             WHERE from_type = :from_type
             AND from_id = :from_id
             AND to_type = :to_type
             AND to_id = :to_id
             AND anchor_text = :anchor_text'
        );

        $stmt->execute([
            'from_type'   => $link->from()->type(),
            'from_id'     => $link->from()->id(),
            'to_type'     => $link->to()->type(),
            'to_id'       => $link->to()->id(),
            'anchor_text' => $link->anchorText(),
        ]);
    }
}


?>