<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte créé avec succès</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                        <td style="padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Bienvenue !</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin-bottom: 20px;">Bonjour <?php echo htmlspecialchars($userName); ?>,</p>
                            
                            <p style="margin-bottom: 20px;">Votre compte a été créé avec succès. Veuillez utiliser les identifiants suivants pour votre première connexion :</p>
                            
                            <div style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; margin: 30px 0;">
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Identifiant :</strong> <?php echo htmlspecialchars($userName); ?>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Mot de passe temporaire :</strong> <?php echo htmlspecialchars($temporaryPassword); ?>
                                </p>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Vous devez changer ce mot de passe temporaire lors de votre première connexion.</p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo getenv('APP_URL'); ?>/login" style="display: inline-block; background-color: #11998e; color: white; padding: 12px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                    Se connecter
                                </a>
                            </div>
                            
                            <p style="margin-bottom: 20px; color: #999; font-size: 13px;">
                                Pour des raisons de sécurité, nous vous conseillons de modifier votre mot de passe immédiatement après votre première connexion.
                            </p>
                            
                            <p style="margin-top: 40px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                                Si vous n'avez pas créé ce compte, veuillez contacter l'administrateur immédiatement.
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
