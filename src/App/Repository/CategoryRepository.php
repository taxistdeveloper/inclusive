<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;
use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return list<Category> */
    public function findAllOrdered(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, slug, title, subtitle, icon_class, pill_label, badge_class, badge_style,
                    alert_strong, alert_text, modal_id, sort_order,
                    admin_icon_wrap_class, admin_border_class, admin_icon_wrap_style, admin_border_style,
                    diagram_icon_class
             FROM categories ORDER BY sort_order ASC, id ASC'
        );
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }

        return $out;
    }

    /** @return array<string, string> slug => title */
    public function titlesBySlug(): array
    {
        $stmt = $this->pdo->query('SELECT slug, title FROM categories ORDER BY sort_order ASC, id ASC');
        $map = [];
        while ($row = $stmt->fetch()) {
            $map[(string) $row['slug']] = (string) $row['title'];
        }

        return $map;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, slug, title, subtitle, icon_class, pill_label, badge_class, badge_style,
                    alert_strong, alert_text, modal_id, sort_order,
                    admin_icon_wrap_class, admin_border_class, admin_icon_wrap_style, admin_border_style,
                    diagram_icon_class
             FROM categories WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findBySlug(string $slug): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, slug, title, subtitle, icon_class, pill_label, badge_class, badge_style,
                    alert_strong, alert_text, modal_id, sort_order,
                    admin_icon_wrap_class, admin_border_class, admin_icon_wrap_style, admin_border_style,
                    diagram_icon_class
             FROM categories WHERE slug = :s LIMIT 1'
        );
        $stmt->execute(['s' => $slug]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        if ($exceptId === null) {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE slug = :s LIMIT 1');
            $stmt->execute(['s' => $slug]);
        } else {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE slug = :s AND id <> :id LIMIT 1');
            $stmt->execute(['s' => $slug, 'id' => $exceptId]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function modalIdExists(string $modalId, ?int $exceptId = null): bool
    {
        if ($exceptId === null) {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE modal_id = :m LIMIT 1');
            $stmt->execute(['m' => $modalId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE modal_id = :m AND id <> :id LIMIT 1');
            $stmt->execute(['m' => $modalId, 'id' => $exceptId]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function insert(Category $c): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (
                slug, title, subtitle, icon_class, pill_label, badge_class, badge_style,
                alert_strong, alert_text, modal_id, sort_order,
                admin_icon_wrap_class, admin_border_class, admin_icon_wrap_style, admin_border_style,
                diagram_icon_class
            ) VALUES (
                :slug, :title, :subtitle, :icon_class, :pill_label, :badge_class, :badge_style,
                :alert_strong, :alert_text, :modal_id, :sort_order,
                :admin_icon_wrap_class, :admin_border_class, :admin_icon_wrap_style, :admin_border_style,
                :diagram_icon_class
            )'
        );
        $stmt->execute([
            'slug' => $c->slug,
            'title' => $c->title,
            'subtitle' => $c->subtitle,
            'icon_class' => $c->iconClass,
            'pill_label' => $c->pillLabel,
            'badge_class' => $c->badgeClass,
            'badge_style' => $c->badgeStyle,
            'alert_strong' => $c->alertStrong,
            'alert_text' => $c->alertText,
            'modal_id' => $c->modalId,
            'sort_order' => $c->sortOrder,
            'admin_icon_wrap_class' => $c->adminIconWrapClass,
            'admin_border_class' => $c->adminBorderClass,
            'admin_icon_wrap_style' => $c->adminIconWrapStyle,
            'admin_border_style' => $c->adminBorderStyle,
            'diagram_icon_class' => $c->diagramIconClass,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Category $c): void
    {
        if ($c->id === null) {
            throw new \InvalidArgumentException('update requires id');
        }
        $stmt = $this->pdo->prepare(
            'UPDATE categories SET
                title = :title, subtitle = :subtitle, icon_class = :icon_class, pill_label = :pill_label,
                badge_class = :badge_class, badge_style = :badge_style, alert_strong = :alert_strong,
                alert_text = :alert_text, modal_id = :modal_id, sort_order = :sort_order,
                admin_icon_wrap_class = :admin_icon_wrap_class, admin_border_class = :admin_border_class,
                admin_icon_wrap_style = :admin_icon_wrap_style, admin_border_style = :admin_border_style,
                diagram_icon_class = :diagram_icon_class
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $c->id,
            'title' => $c->title,
            'subtitle' => $c->subtitle,
            'icon_class' => $c->iconClass,
            'pill_label' => $c->pillLabel,
            'badge_class' => $c->badgeClass,
            'badge_style' => $c->badgeStyle,
            'alert_strong' => $c->alertStrong,
            'alert_text' => $c->alertText,
            'modal_id' => $c->modalId,
            'sort_order' => $c->sortOrder,
            'admin_icon_wrap_class' => $c->adminIconWrapClass,
            'admin_border_class' => $c->adminBorderClass,
            'admin_icon_wrap_style' => $c->adminIconWrapStyle,
            'admin_border_style' => $c->adminBorderStyle,
            'diagram_icon_class' => $c->diagramIconClass,
        ]);
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Category
    {
        $bs = $row['badge_style'];
        $aws = $row['admin_icon_wrap_style'];
        $abs = $row['admin_border_style'];

        return new Category(
            (int) $row['id'],
            (string) $row['slug'],
            (string) $row['title'],
            (string) $row['subtitle'],
            (string) $row['icon_class'],
            (string) $row['pill_label'],
            (string) $row['badge_class'],
            $bs !== null && $bs !== '' ? (string) $bs : null,
            (string) $row['alert_strong'],
            (string) $row['alert_text'],
            (string) $row['modal_id'],
            (int) $row['sort_order'],
            (string) $row['admin_icon_wrap_class'],
            (string) $row['admin_border_class'],
            $aws !== null && $aws !== '' ? (string) $aws : null,
            $abs !== null && $abs !== '' ? (string) $abs : null,
            (string) ($row['diagram_icon_class'] ?? 'text-primary'),
        );
    }
}
