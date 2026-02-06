<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport approuvé</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <td style="padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Rapport approuvé ✓</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin-bottom: 20px;">Bonjour <?php echo htmlspecialchars($studentName); ?>,</p>
                            
                            <p style="margin-bottom: 20px;">Nous avons le plaisir de vous annoncer que votre rapport a été approuvé par la commission d'évaluation.</p>
                            
                            <p style="margin-bottom: 20px;">Vous pouvez maintenant accéder à votre espace personnel pour consulter les commentaires détaillés.</p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo getenv('APP_URL'); ?>/dashboard/rapports" style="display: inline-block; background-color: #667eea; color: white; padding: 12px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                    Consulter mon rapport
                                </a>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Les prochaines étapes de votre processus d'évaluation seront communiquées dans les jours à venir.</p>
                            
                            <p style="margin-top: 40px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                                Félicitations pour cette étape franchie avec succès !
                            </p>
                        </td>
                    </tr>
                    
                    <tr style="background-color: #f9f9f9; border-top: 1px solid #eee;">
                        <td style="padding: 20px 30px; text-align: center; font-size: 12px; color: #999;">
                            <p style="margin: 0;">© 2026 Tous droits réservés.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
