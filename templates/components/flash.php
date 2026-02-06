<?php if (isset($flashMessages) && !empty($flashMessages)): ?>
    <div class="flash-container">
        <?php foreach ($flashMessages as $message): ?>
            <div class="flash-message flash-<?php echo htmlspecialchars($message['type']); ?>">
                <div class="flash-content">
                    <p><?php echo htmlspecialchars($message['text']); ?></p>
                </div>
                <button class="flash-close" aria-label="Fermer">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.querySelectorAll('.flash-close').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.flash-message').remove();
            });
        });

        document.querySelectorAll('.flash-message').forEach(message => {
            setTimeout(() => {
                message.remove();
            }, 5000);
        });
    </script>
<?php endif; ?>
