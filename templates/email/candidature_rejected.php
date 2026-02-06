<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature rejetée</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <td style="padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Candidature rejetée</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin-bottom: 20px;">Bonjour <?php echo htmlspecialchars($studentName); ?>,</p>
                            
                            <p style="margin-bottom: 20px;">Nous regrettons de vous annoncer que votre candidature n'a pas été acceptée.</p>
                            
                            <p style="margin-bottom: 20px;"><strong>Raison du rejet :</strong></p>
                            <div style="background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #f5576c;">
                                <p style="margin: 0;"><?php echo htmlspecialchars($reason); ?></p>
                            </div>
                            
                            <p style="margin-bottom: 20px;">Nous vous encourageons à postuler à nouveau lors de la prochaine session de candidature.</p>
                            
                            <p style="margin-bottom: 20px;">Pour toute question ou information supplémentaire, vous pouvez contacter notre équipe d'admission.</p>
                            
                            <p style="margin-top: 40px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                                Nous apprécions votre intérêt et vous souhaitons bonne chance pour vos futurs projets.
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
