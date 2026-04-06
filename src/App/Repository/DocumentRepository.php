<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Document;
use PDO;

final class DocumentRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return list<Document> */
    public function findBySection(string $section): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, section, title, pdf_path, icon_class, sort_order
             FROM documents WHERE section = :s
             ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute(['s' => $section]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }

        return $out;
    }

    public function findById(int $id): ?Document
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, section, title, pdf_path, icon_class, sort_order FROM documents WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function insert(Document $doc): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO documents (section, title, pdf_path, icon_class, sort_order)
             VALUES (:section, :title, :pdf_path, :icon_class, :sort_order)'
        );
        $stmt->execute([
            'section' => $doc->section,
            'title' => $doc->title,
            'pdf_path' => $doc->pdfPath,
            'icon_class' => $doc->iconClass,
            'sort_order' => $doc->sortOrder,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Document $doc): void
    {
        if ($doc->id === null) {
            throw new \InvalidArgumentException('update requires id');
        }
        $stmt = $this->pdo->prepare(
            'UPDATE documents SET section = :section, title = :title, pdf_path = :pdf_path,
             icon_class = :icon_class, sort_order = :sort_order WHERE id = :id'
        );
        $stmt->execute([
            'id' => $doc->id,
            'section' => $doc->section,
            'title' => $doc->title,
            'pdf_path' => $doc->pdfPath,
            'icon_class' => $doc->iconClass,
            'sort_order' => $doc->sortOrder,
        ]);
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function countBySection(string $section): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM documents WHERE section = :s');
        $stmt->execute(['s' => $section]);

        return (int) $stmt->fetchColumn();
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Document
    {
        return new Document(
            (int) $row['id'],
            (string) $row['section'],
            (string) $row['title'],
            (string) $row['pdf_path'],
            (string) $row['icon_class'],
            (int) $row['sort_order'],
        );
    }
}
