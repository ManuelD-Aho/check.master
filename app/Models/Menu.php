<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Menu
 * 
 * Représente un élément de menu.
 * Table: menus
 */
class Menu extends Model
{
    protected string $table = 'menus';
    protected string $primaryKey = 'id_menu';
    protected array $fillable = [
        'parent_id',
        'titre',
        'icone',
        'route',
        'ordre',
        'actif',
        'ressource_code',
    ];

    /**
     * Retourne les enfants de ce menu
     *
     * @return self[]
     */
    public function getEnfants(): array
    {
        $sql = "SELECT * FROM menus WHERE parent_id = :id AND actif = 1 ORDER BY ordre";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne le menu parent
     */
    public function getParent(): ?self
    {
        if ($this->parent_id === null) {
            return null;
        }
        return self::find((int) $this->parent_id);
    }

    /**
     * Vérifie si le menu a des enfants
     */
    public function aDesEnfants(): bool
    {
        $sql = "SELECT COUNT(*) FROM menus WHERE parent_id = :id AND actif = 1";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Retourne les menus racine (sans parent)
     *
     * @return self[]
     */
    public static function racines(): array
    {
        $sql = "SELECT * FROM menus WHERE parent_id IS NULL AND actif = 1 ORDER BY ordre";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Construit l'arbre de menu complet
     */
    public static function arbreComplet(): array
    {
        $menus = self::racines();
        $arbre = [];

        foreach ($menus as $menu) {
            $item = $menu->toArray();
            $item['enfants'] = self::construireEnfants($menu);
            $arbre[] = $item;
        }

        return $arbre;
    }

    /**
     * Construit les enfants récursivement
     */
    private static function construireEnfants(self $parent): array
    {
        $enfants = $parent->getEnfants();
        $result = [];

        foreach ($enfants as $enfant) {
            $item = $enfant->toArray();
            if ($enfant->aDesEnfants()) {
                $item['enfants'] = self::construireEnfants($enfant);
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Retourne les menus accessibles par un utilisateur
     */
    public static function pourUtilisateur(int $utilisateurId): array
    {
        // TODO: Implémenter le filtrage par permissions
        return self::arbreComplet();
    }
}
