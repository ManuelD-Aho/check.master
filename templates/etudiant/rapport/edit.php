<div class="rapport-edit">
    <div class="edit-header">
        <div><h1><?php echo htmlspecialchars($rapport['titre'] ?? 'Nouveau rapport'); ?></h1><p><?php echo htmlspecialchars($rapport['entreprise'] ?? ''); ?></p></div>
        <div><span class="auto-save">Enregistrement automatique</span><a href="<?php echo BASE_URL; ?>/etudiant/rapports" class="btn-back">← Retour</a></div>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/etudiant/rapports/<?php echo $rapport['id']; ?>/save" class="rapport-form">
        <div class="editor-toolbar">
            <label>Titre</label>
            <input type="text" id="titre_rapport" class="titre-input" value="<?php echo htmlspecialchars($rapport['titre'] ?? ''); ?>" placeholder="Titre du rapport">
        </div>

        <div class="editor-container">
            <textarea id="editor" name="contenu" class="tinymce-editor"><?php echo htmlspecialchars($rapport['contenu'] ?? ''); ?></textarea>
        </div>

        <div class="editor-footer">
            <button type="submit" name="action" value="save" class="btn-save">Enregistrer</button>
            <button type="submit" name="action" value="submit" class="btn-submit">Soumettre</button>
            <a href="<?php echo BASE_URL; ?>/etudiant/rapports" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<style>
.rapport-edit{background:#f5f5f5;min-height:100vh;display:flex;flex-direction:column}
.edit-header{background:white;border-bottom:1px solid #e5e5e5;padding:1.5rem 2rem;display:flex;justify-content:space-between;align-items:center;gap:2rem}
.edit-header h1{margin:0 0 0.35rem 0;font-size:1.8rem;font-weight:700;color:#1a1a1a}
.edit-header p{margin:0;color:#666;font-size:0.95rem}
.auto-save{font-size:0.85rem;color:#28a745;font-weight:500;padding:0.4rem 0.75rem;background:#d4edda;border-radius:4px}
.btn-back{padding:0.6rem 1rem;background:#f0f0f0;color:#333;text-decoration:none;border-radius:6px;font-weight:600;transition:all 0.3s ease}
.btn-back:hover{background:#e0e0e0}
.editor-toolbar{background:white;padding:1rem 2rem;border-bottom:1px solid #e5e5e5;display:flex;flex-direction:column;gap:0.5rem}
.editor-toolbar label{font-weight:600;color:#333;font-size:0.9rem}
.titre-input{padding:0.75rem;border:1px solid #ddd;border-radius:6px;font-size:1rem;font-family:inherit;max-width:600px}
.titre-input:focus{outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1)}
.rapport-form{flex:1;display:flex;flex-direction:column}
.editor-container{padding:1rem;background:white;margin:1rem;border-radius:8px;border:1px solid #e5e5e5;flex:1;overflow:hidden}
.tinymce-editor{width:100%;min-height:500px;border:none;font-family:Georgia,serif;font-size:1rem;line-height:1.8;color:#333;padding:0}
.tinymce-editor:focus{outline:none}
.editor-footer{background:white;border-top:1px solid #e5e5e5;padding:1.5rem 2rem;display:flex;justify-content:flex-end;gap:1rem}
.btn-save,.btn-submit,.btn-cancel{padding:0.75rem 1.5rem;border:none;border-radius:8px;font-weight:600;font-size:0.9rem;cursor:pointer;text-decoration:none;transition:all 0.3s ease;display:inline-block}
.btn-save{background:#f0f0f0;color:#333}
.btn-save:hover{background:#e0e0e0}
.btn-submit{background:#667eea;color:white}
.btn-submit:hover{background:#764ba2;transform:translateY(-2px)}
.btn-cancel{background:transparent;color:#333;border:1px solid #ddd}
.btn-cancel:hover{border-color:#333;background:#f9f9f9}
@media(max-width:768px){.rapport-edit{padding:0}.edit-header{flex-direction:column;align-items:stretch;padding:1rem}.edit-header h1{font-size:1.4rem}.editor-container{margin:0.5rem;border-radius:0}.editor-footer{flex-direction:column;padding:1rem}.btn-save,.btn-submit,.btn-cancel{width:100%}}
</style>

<script src="https://cdn.tiny.cloud/1/no-license/tinymce/7/tinymce.min.js"></script>
<script>
tinymce.init({selector:'#editor',plugins:['lists','image','link','code','help'],toolbar:'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',height:500,language:'fr_FR',branding:false,statusbar:true});
</script>
EOFRAPEDIT
echo "✓ rapport/edit.php populated"
