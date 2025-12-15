<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationTemplate
 * 
 * Template pour les notifications (Email, SMS, App).
 * Table: notification_templates
 */
class NotificationTemplate extends Model
{
    protected string $table = 'notification_templates';
    protected string $primaryKey = 'id_template';
    protected array $fillable = [
        'code',
        'sujet_template',
        'corps_template',
        'type_canal', // 'EMAIL', 'SKS', 'APP'
        'description',
    ];

    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code' => $code]);
    }
}
