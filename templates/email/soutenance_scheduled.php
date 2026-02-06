<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soutenance planifiée</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                        <td style="padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Soutenance programmée</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin-bottom: 20px;">Bonjour <?php echo htmlspecialchars($studentName); ?>,</p>
                            
                            <p style="margin-bottom: 20px;">Votre soutenance a été programmée. Voici les détails :</p>
                            
                            <div style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; margin: 30px 0; border-left: 4px solid #fa709a;">
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Date :</strong> <?php echo htmlspecialchars($date); ?>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Salle :</strong> <?php echo htmlspecialchars($salle); ?>
                                </p>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Assurez-vous d'arriver 15 minutes avant l'heure prévue. Vous devrez apporter votre pièce d'identité.</p>
                            
                            <p style="margin-bottom: 20px;">Toute absence non justifiée entraînera l'annulation de votre soutenance.</p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo getenv('APP_URL'); ?>/dashboard/soutenances" style="display: inline-block; background-color: #fa709a; color: white; padding: 12px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                    Consulter mes détails
                                </a>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Bonne chance pour votre soutenance !</p>
                            
                            <p style="margin-top: 40px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                                Si vous avez des questions ou si vous ne pouvez pas vous présenter, veuillez contacter immédiatement notre équipe.
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
