<?php

namespace Keystone\Plugins\Seo\Infrastructure\Persistence;

use PDO;
use Keystone\Plugins\Seo\Domain\{
    SeoRepositoryInterface,
    SeoSubject,
    SeoMetadata
};

final class PdoSeoRepository implements SeoRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function find(SeoSubject $subject): ?SeoMetadata
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM seo_metadata
             WHERE subject_type = :type
             AND subject_id = :id'
        );

        $stmt->execute([
            'type' => $subject->type(),
            'id'   => $subject->id(),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new SeoMetadata(
            $row['title'],
            $row['description'],
            (bool) $row['no_index'],
            $row['canonical'],
            $row['open_graph']
                ? json_decode($row['open_graph'], true)
                : []
        );
    }

    public function save(
        SeoSubject $subject,
        SeoMetadata $metadata
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seo_metadata (
                subject_type,
                subject_id,
                title,
                description,
                no_index,
                canonical,
                open_graph,
                created_at,
                updated_at
            ) VALUES (
                :type,
                :id,
                :title,
                :description,
                :no_index,
                :canonical,
                :open_graph,
                NOW(),
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                no_index = VALUES(no_index),
                canonical = VALUES(canonical),
                open_graph = VALUES(open_graph),
                updated_at = NOW()
        ');

        $stmt->execute([
            'type'        => $subject->type(),
            'id'          => $subject->id(),
            'title'       => $metadata->title(),
            'description' => $metadata->description(),
            'no_index'    => (int) $metadata->noIndex(),
            'canonical'   => $metadata->canonical(),
            'open_graph'  => json_encode($metadata->openGraph()),
        ]);
    }
}

?>