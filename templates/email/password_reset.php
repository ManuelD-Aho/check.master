<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <td style="padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Réinitialisation de mot de passe</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin-bottom: 20px;">Bonjour,</p>
                            
                            <p style="margin-bottom: 20px;">Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour procéder :</p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo getenv('APP_URL'); ?>/reset-password/<?php echo htmlspecialchars($token); ?>" style="display: inline-block; background-color: #667eea; color: white; padding: 12px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                    Réinitialiser mon mot de passe
                                </a>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Ce lien expire dans 24 heures.</p>
                            
                            <p style="margin-bottom: 20px;">Si vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail.</p>
                            
                            <p style="margin-top: 40px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                                Cet e-mail a été envoyé à votre adresse e-mail associée à votre compte. Si ce n'est pas vous, veuillez nous contacter immédiatement.
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
